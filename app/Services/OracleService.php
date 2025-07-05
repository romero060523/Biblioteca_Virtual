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
     * Ejecutar un procedimiento que retorna un cursor
     */
    public function executeProcedureWithCursor(string $procedureName, array $parameters = [], string $cursorParam = 'p_cursor'): array
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
                if ($name === $cursorParam) {
                    $stmt->bindParam(":$name", $cursor, PDO::PARAM_STMT);
                } elseif ($value === null) {
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
            
            // Obtener datos del cursor
            $results = [];
            if (isset($cursor) && $cursor) {
                while ($row = oci_fetch_array($cursor, OCI_ASSOC + OCI_RETURN_NULLS)) {
                    $results[] = $row;
                }
                oci_free_statement($cursor);
            }
            
            return $results;
            
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