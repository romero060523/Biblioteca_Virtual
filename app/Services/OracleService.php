<?php

namespace App\Services;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use PDO;

class OracleService
{
    protected $connection;

    public function __construct()
    {
        $this->connection = DB::connection('oracle');
    }

    /**
     * Ejecutar un procedimiento almacenado
     */
    public function executeProcedure(string $procedureName, array $parameters = []): array
    {
        try {
            $pdo = $this->connection->getPdo();
            
            // Construir la llamada al procedimiento
            $paramPlaceholders = [];
            $paramNames = [];
            
            foreach ($parameters as $name => $value) {
                $paramPlaceholders[] = ":$name";
                $paramNames[] = $name;
            }
            
            $sql = "BEGIN $procedureName(" . implode(', ', $paramPlaceholders) . "); END;";
            
            $stmt = $pdo->prepare($sql);
            
            // Bind de parámetros
            foreach ($parameters as $name => $value) {
                if ($value === null) {
                    $stmt->bindValue(":$name", null, PDO::PARAM_NULL);
                } elseif (is_bool($value)) {
                    $stmt->bindValue(":$name", $value ? 1 : 0, PDO::PARAM_INT);
                } elseif (is_int($value)) {
                    $stmt->bindValue(":$name", $value, PDO::PARAM_INT);
                } else {
                    $stmt->bindValue(":$name", $value, PDO::PARAM_STR);
                }
            }
            
            $stmt->execute();
            
            // Obtener parámetros de salida
            $outputParams = [];
            foreach ($paramNames as $name) {
                if (strpos($name, 'p_') === 0) {
                    $outputParams[$name] = $stmt->bindColumn(":$name", $value);
                }
            }
            
            return $outputParams;
            
        } catch (\Exception $e) {
            Log::error("Error ejecutando procedimiento $procedureName: " . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Ejecutar un procedimiento que retorna un cursor usando OCI8 nativo
     */
    public function executeProcedureWithCursor(string $procedureName, array $parameters = [], string $cursorParam = 'p_cursor'): array
    {
        try {
            // Obtener el recurso OCI8 de la forma más compatible
            $oci = null;

            // Usar la propiedad 'pdo' que está disponible
            $reflection = new \ReflectionClass($this->connection);
            $pdoProperty = $reflection->getProperty('pdo');
            $pdoProperty->setAccessible(true);
            $pdo = $pdoProperty->getValue($this->connection);
            
            // Acceder al recurso OCI8 desde la propiedad 'dbh' del PDO
            $pdoReflection = new \ReflectionClass($pdo);
            $dbhProperty = $pdoReflection->getProperty('dbh');
            $dbhProperty->setAccessible(true);
            $oci = $dbhProperty->getValue($pdo);

            if (!$oci || !is_resource($oci)) {
                throw new \Exception('No se pudo acceder al recurso OCI8 desde dbh.');
            }

            // Construir la llamada al procedimiento
            $paramPlaceholders = [];
            foreach ($parameters as $name => $value) {
                $paramPlaceholders[] = ":$name";
            }
            $paramPlaceholders[] = ":$cursorParam";
            $sql = "BEGIN $procedureName(" . implode(', ', $paramPlaceholders) . "); END;";

            // Preparar la sentencia usando OCI8
            $stmt = oci_parse($oci, $sql);

            // Bind de parámetros de entrada
            foreach ($parameters as $name => &$value) {
                oci_bind_by_name($stmt, ":$name", $value);
            }

            // Bind del cursor de salida
            $refCursor = oci_new_cursor($oci);
            oci_bind_by_name($stmt, ":$cursorParam", $refCursor, -1, OCI_B_CURSOR);

            // Ejecutar la sentencia y el cursor
            oci_execute($stmt);
            oci_execute($refCursor);

            // Obtener los resultados
            $rows = [];
            while (($row = oci_fetch_assoc($refCursor)) != false) {
                $rows[] = $row;
            }
            
            // Liberar recursos
            oci_free_statement($stmt);
            oci_free_statement($refCursor);

            return $rows;
        } catch (\Exception $e) {
            Log::error("Error ejecutando procedimiento con cursor $procedureName: " . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Ejecutar una consulta SQL directa
     */
    public function executeQuery(string $sql, array $parameters = []): array
    {
        try {
            $pdo = $this->connection->getPdo();
            $stmt = $pdo->prepare($sql);
            
            foreach ($parameters as $name => $value) {
                if ($value === null) {
                    $stmt->bindValue(":$name", null, PDO::PARAM_NULL);
                } elseif (is_bool($value)) {
                    $stmt->bindValue(":$name", $value ? 1 : 0, PDO::PARAM_INT);
                } elseif (is_int($value)) {
                    $stmt->bindValue(":$name", $value, PDO::PARAM_INT);
                } else {
                    $stmt->bindValue(":$name", $value, PDO::PARAM_STR);
                }
            }
            
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
            
        } catch (\Exception $e) {
            Log::error("Error ejecutando consulta SQL: " . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Verificar conexión a Oracle
     */
    public function testConnection(): bool
    {
        try {
            $pdo = $this->connection->getPdo();
            $stmt = $pdo->query('SELECT 1 FROM DUAL');
            return $stmt->fetch() !== false;
        } catch (\Exception $e) {
            Log::error("Error de conexión a Oracle: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Obtener información de la base de datos
     */
    public function getDatabaseInfo(): array
    {
        try {
            $pdo = $this->connection->getPdo();
            
            $info = [];
            
            // Versión de Oracle
            $stmt = $pdo->query('SELECT * FROM V$VERSION WHERE ROWNUM = 1');
            $version = $stmt->fetch(PDO::FETCH_ASSOC);
            $info['version'] = $version['BANNER'] ?? 'Desconocida';
            
            // Usuario actual
            $stmt = $pdo->query('SELECT USER FROM DUAL');
            $user = $stmt->fetch(PDO::FETCH_ASSOC);
            $info['current_user'] = $user['USER'] ?? 'Desconocido';
            
            // Instancia
            $stmt = $pdo->query('SELECT INSTANCE_NAME FROM V$INSTANCE');
            $instance = $stmt->fetch(PDO::FETCH_ASSOC);
            $info['instance_name'] = $instance['INSTANCE_NAME'] ?? 'Desconocida';
            
            return $info;
            
        } catch (\Exception $e) {
            Log::error("Error obteniendo información de Oracle: " . $e->getMessage());
            return ['error' => $e->getMessage()];
        }
    }
} 