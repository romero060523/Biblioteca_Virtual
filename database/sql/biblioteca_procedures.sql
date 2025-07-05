-- PROCEDIMIENTOS ALMACENADOS PARA BIBLIOTECA VIRTUAL
-- PAQUETE PARA GESTIÓN DE LIBROS
CREATE OR REPLACE PACKAGE PKG_LIBROS AS
    -- Procedimientos para CRUD de libros
    PROCEDURE INSERTAR_LIBRO(
        p_titulo IN VARCHAR2,
        p_autor IN VARCHAR2,
        p_categoria IN VARCHAR2,
        p_stock IN NUMBER,
        p_libro_id OUT NUMBER
    );
    
    PROCEDURE ACTUALIZAR_LIBRO(
        p_libro_id IN NUMBER,
        p_titulo IN VARCHAR2,
        p_autor IN VARCHAR2,
        p_categoria IN VARCHAR2,
        p_stock IN NUMBER
    );
    
    PROCEDURE ELIMINAR_LIBRO(
        p_libro_id IN NUMBER
    );
    
    PROCEDURE OBTENER_LIBRO(
        p_libro_id IN NUMBER,
        p_cursor OUT SYS_REFCURSOR
    );
    
    PROCEDURE LISTAR_LIBROS(
        p_categoria IN VARCHAR2 DEFAULT NULL,
        p_autor IN VARCHAR2 DEFAULT NULL,
        p_cursor OUT SYS_REFCURSOR
    );
    
    PROCEDURE BUSCAR_LIBROS(
        p_termino IN VARCHAR2,
        p_cursor OUT SYS_REFCURSOR
    );
    
    PROCEDURE OBTENER_LIBROS_DISPONIBLES(
        p_cursor OUT SYS_REFCURSOR
    );
    
    PROCEDURE ACTUALIZAR_STOCK(
        p_libro_id IN NUMBER,
        p_cantidad IN NUMBER
    );
    
    -- Obtener los 5 libros más recientes
    PROCEDURE OBTENER_LIBROS_RECIENTES(
        p_cursor OUT SYS_REFCURSOR
    );
    
END PKG_LIBROS;
/

CREATE OR REPLACE PACKAGE BODY PKG_LIBROS AS

    PROCEDURE INSERTAR_LIBRO(
        p_titulo IN VARCHAR2,
        p_autor IN VARCHAR2,
        p_categoria IN VARCHAR2,
        p_stock IN NUMBER,
        p_libro_id OUT NUMBER
    ) AS
    BEGIN
        -- Validar stock
        IF p_stock < 0 THEN
            RAISE_APPLICATION_ERROR(-20001, 'El stock no puede ser negativo');
        END IF;
        
        -- Insertar el libro
        INSERT INTO libros (titulo, autor, categoria, stock)
        VALUES (p_titulo, p_autor, p_categoria, p_stock)
        RETURNING id INTO p_libro_id;
        
        COMMIT;
    EXCEPTION
        WHEN OTHERS THEN
            ROLLBACK;
            RAISE;
    END INSERTAR_LIBRO;

    PROCEDURE ACTUALIZAR_LIBRO(
        p_libro_id IN NUMBER,
        p_titulo IN VARCHAR2,
        p_autor IN VARCHAR2,
        p_categoria IN VARCHAR2,
        p_stock IN NUMBER
    ) AS
    BEGIN
        -- Validar stock
        IF p_stock < 0 THEN
            RAISE_APPLICATION_ERROR(-20002, 'El stock no puede ser negativo');
        END IF;
        
        -- Actualizar el libro
        UPDATE libros SET
            titulo = p_titulo,
            autor = p_autor,
            categoria = p_categoria,
            stock = p_stock
        WHERE id = p_libro_id;
        
        IF SQL%ROWCOUNT = 0 THEN
            RAISE_APPLICATION_ERROR(-20003, 'Libro no encontrado');
        END IF;
        
        COMMIT;
    EXCEPTION
        WHEN OTHERS THEN
            ROLLBACK;
            RAISE;
    END ACTUALIZAR_LIBRO;

    PROCEDURE ELIMINAR_LIBRO(p_libro_id IN NUMBER) AS
        v_prestamos_activos NUMBER;
    BEGIN
        -- Verificar si hay préstamos activos
        SELECT COUNT(*) INTO v_prestamos_activos
        FROM prestamos 
        WHERE id_libro = p_libro_id AND estado = 'ACTIVO';
        
        IF v_prestamos_activos > 0 THEN
            RAISE_APPLICATION_ERROR(-20004, 'No se puede eliminar el libro porque tiene préstamos activos');
        END IF;
        
        DELETE FROM libros WHERE id = p_libro_id;
        
        IF SQL%ROWCOUNT = 0 THEN
            RAISE_APPLICATION_ERROR(-20003, 'Libro no encontrado');
        END IF;
        
        COMMIT;
    EXCEPTION
        WHEN OTHERS THEN
            ROLLBACK;
            RAISE;
    END ELIMINAR_LIBRO;

    PROCEDURE OBTENER_LIBRO(p_libro_id IN NUMBER, p_cursor OUT SYS_REFCURSOR) AS
    BEGIN
        OPEN p_cursor FOR
            SELECT id, titulo, autor, categoria, stock
            FROM libros
            WHERE id = p_libro_id;
    END OBTENER_LIBRO;

    PROCEDURE LISTAR_LIBROS(
        p_categoria IN VARCHAR2 DEFAULT NULL,
        p_autor IN VARCHAR2 DEFAULT NULL,
        p_cursor OUT SYS_REFCURSOR
    ) AS
        v_sql VARCHAR2(4000);
        v_params SYS_REFCURSOR;
    BEGIN
        v_sql := 'SELECT id, titulo, autor, categoria, stock
                  FROM libros WHERE 1=1';
        
        IF p_categoria IS NOT NULL THEN
            v_sql := v_sql || ' AND categoria = :categoria';
        END IF;
        
        IF p_autor IS NOT NULL THEN
            v_sql := v_sql || ' AND autor LIKE ''%'' || :autor || ''%''';
        END IF;
        
        v_sql := v_sql || ' ORDER BY titulo';
        
        -- Usar EXECUTE IMMEDIATE para manejar parámetros dinámicos
        IF p_categoria IS NOT NULL AND p_autor IS NOT NULL THEN
            OPEN p_cursor FOR v_sql USING p_categoria, p_autor;
        ELSIF p_categoria IS NOT NULL THEN
            OPEN p_cursor FOR v_sql USING p_categoria;
        ELSIF p_autor IS NOT NULL THEN
            OPEN p_cursor FOR v_sql USING p_autor;
        ELSE
            OPEN p_cursor FOR v_sql;
        END IF;
    END LISTAR_LIBROS;

    PROCEDURE BUSCAR_LIBROS(p_termino IN VARCHAR2, p_cursor OUT SYS_REFCURSOR) AS
    BEGIN
        OPEN p_cursor FOR
            SELECT id, titulo, autor, categoria, stock
            FROM libros
            WHERE LOWER(titulo) LIKE '%' || LOWER(p_termino) || '%'
               OR LOWER(autor) LIKE '%' || LOWER(p_termino) || '%'
               OR LOWER(categoria) LIKE '%' || LOWER(p_termino) || '%'
            ORDER BY titulo;
    END BUSCAR_LIBROS;

    PROCEDURE OBTENER_LIBROS_DISPONIBLES(p_cursor OUT SYS_REFCURSOR) AS
    BEGIN
        OPEN p_cursor FOR
            SELECT id, titulo, autor, categoria, stock
            FROM libros
            WHERE stock > 0
            ORDER BY titulo;
    END OBTENER_LIBROS_DISPONIBLES;

    PROCEDURE ACTUALIZAR_STOCK(p_libro_id IN NUMBER, p_cantidad IN NUMBER) AS
    BEGIN
        UPDATE libros 
        SET stock = stock + p_cantidad
        WHERE id = p_libro_id;
        
        IF SQL%ROWCOUNT = 0 THEN
            RAISE_APPLICATION_ERROR(-20003, 'Libro no encontrado');
        END IF;
        
        COMMIT;
    EXCEPTION
        WHEN OTHERS THEN
            ROLLBACK;
            RAISE;
    END ACTUALIZAR_STOCK;

    -- Obtener los 5 libros más recientes
    PROCEDURE OBTENER_LIBROS_RECIENTES(
        p_cursor OUT SYS_REFCURSOR
    ) AS
    BEGIN
        OPEN p_cursor FOR
            SELECT * FROM (
                SELECT id, titulo, autor, categoria, stock, fecha_creacion
                FROM libros
                ORDER BY fecha_creacion DESC, id DESC
            )
            WHERE ROWNUM <= 3;
    END OBTENER_LIBROS_RECIENTES;

END PKG_LIBROS;
/


-- PAQUETE PARA GESTIÓN DE PRÉSTAMOS
CREATE OR REPLACE PACKAGE PKG_PRESTAMOS AS
    -- Procedimientos para gestión de préstamos
    PROCEDURE REGISTRAR_PRESTAMO(
        p_usuario_id IN NUMBER,
        p_libro_id IN NUMBER,
        p_fecha_prestamo IN VARCHAR2,
        p_fecha_devolucion IN VARCHAR2,
        p_prestamo_id OUT NUMBER
    );
    
    PROCEDURE DEVOLVER_LIBRO(
        p_prestamo_id IN NUMBER
    );
    
    PROCEDURE OBTENER_PRESTAMO(
        p_prestamo_id IN NUMBER,
        p_cursor OUT SYS_REFCURSOR
    );
    
    PROCEDURE LISTAR_PRESTAMOS_USUARIO(
        p_usuario_id IN NUMBER,
        p_estado IN VARCHAR2 DEFAULT NULL,
        p_cursor OUT SYS_REFCURSOR
    );
    
    PROCEDURE LISTAR_PRESTAMOS_LIBRO(
        p_libro_id IN NUMBER,
        p_cursor OUT SYS_REFCURSOR
    );
    
    PROCEDURE OBTENER_PRESTAMOS_VENCIDOS(
        p_cursor OUT SYS_REFCURSOR
    );
    
    PROCEDURE OBTENER_HISTORIAL_PRESTAMOS(
        p_fecha_inicio IN DATE DEFAULT NULL,
        p_fecha_fin IN DATE DEFAULT NULL,
        p_cursor OUT SYS_REFCURSOR
    );
    
    PROCEDURE OBTENER_LIBROS_MAS_PRESTADOS(
        p_limite IN NUMBER DEFAULT 10,
        p_cursor OUT SYS_REFCURSOR
    );
    
    PROCEDURE ACTUALIZAR_ESTADOS_VENCIDOS;
    
    PROCEDURE LISTAR_PRESTAMOS(
        p_estado IN VARCHAR2 DEFAULT NULL,
        p_cursor OUT SYS_REFCURSOR
    );
    
    PROCEDURE ELIMINAR_PRESTAMO(
        p_prestamo_id IN NUMBER
    );
    
    -- Obtener los 5 préstamos más recientes
    PROCEDURE OBTENER_PRESTAMOS_RECIENTES(
        p_cursor OUT SYS_REFCURSOR
    );
    
END PKG_PRESTAMOS;
/

CREATE OR REPLACE PACKAGE BODY PKG_PRESTAMOS AS

    PROCEDURE REGISTRAR_PRESTAMO(
        p_usuario_id IN NUMBER,
        p_libro_id IN NUMBER,
        p_fecha_prestamo IN VARCHAR2,
        p_fecha_devolucion IN VARCHAR2,
        p_prestamo_id OUT NUMBER
    ) AS
        v_stock_disponible NUMBER;
        v_prestamos_activos NUMBER;
        v_rol_usuario VARCHAR2(20);
        v_fecha_prestamo DATE;
        v_fecha_devolucion DATE;
    BEGIN
        -- Convertir las fechas recibidas como string a tipo DATE
        v_fecha_prestamo := TO_DATE(p_fecha_prestamo, 'YYYY-MM-DD');
        v_fecha_devolucion := TO_DATE(p_fecha_devolucion, 'YYYY-MM-DD');

        -- Verificar que el libro esté disponible
        SELECT stock INTO v_stock_disponible
        FROM libros WHERE id = p_libro_id;
        
        IF v_stock_disponible <= 0 THEN
            RAISE_APPLICATION_ERROR(-20005, 'El libro no está disponible para préstamo');
        END IF;
        
        -- Verificar rol del usuario
        SELECT rol INTO v_rol_usuario
        FROM usuarios WHERE id = p_usuario_id;
        
        -- Solo usuarios normales tienen límite de préstamos
        IF v_rol_usuario = 'USUARIO' THEN
            -- Verificar que el usuario no tenga demasiados préstamos activos (máximo 3)
            SELECT COUNT(*) INTO v_prestamos_activos
            FROM prestamos 
            WHERE id_usuario = p_usuario_id AND estado = 'ACTIVO';
            
            IF v_prestamos_activos >= 3 THEN
                RAISE_APPLICATION_ERROR(-20006, 'El usuario ya tiene el máximo de préstamos activos (3)');
            END IF;
        END IF;
        
        -- Registrar el préstamo con fechas personalizadas
        INSERT INTO prestamos (
            id_usuario, id_libro, fecha_prestamo, 
            fecha_devolucion, estado
        ) VALUES (
            p_usuario_id, p_libro_id, v_fecha_prestamo,
            v_fecha_devolucion, 'ACTIVO'
        ) RETURNING id INTO p_prestamo_id;
        
        -- Actualizar stock del libro
        UPDATE libros 
        SET stock = stock - 1
        WHERE id = p_libro_id;
        
        COMMIT;
    EXCEPTION
        WHEN OTHERS THEN
            ROLLBACK;
            RAISE;
    END REGISTRAR_PRESTAMO;

    PROCEDURE DEVOLVER_LIBRO(p_prestamo_id IN NUMBER) AS
        v_libro_id NUMBER;
        v_estado VARCHAR2(20);
    BEGIN
        -- Obtener información del préstamo
        SELECT id_libro, estado INTO v_libro_id, v_estado
        FROM prestamos WHERE id = p_prestamo_id;
        
        IF v_estado = 'DEVUELTO' THEN
            RAISE_APPLICATION_ERROR(-20007, 'El libro ya fue devuelto');
        END IF;
        
        -- Marcar como devuelto
        UPDATE prestamos SET
            estado = 'DEVUELTO'
        WHERE id = p_prestamo_id;
        
        -- Actualizar stock del libro
        UPDATE libros 
        SET stock = stock + 1
        WHERE id = v_libro_id;
        
        COMMIT;
    EXCEPTION
        WHEN OTHERS THEN
            ROLLBACK;
            RAISE;
    END DEVOLVER_LIBRO;

    PROCEDURE OBTENER_PRESTAMO(p_prestamo_id IN NUMBER, p_cursor OUT SYS_REFCURSOR) AS
    BEGIN
        OPEN p_cursor FOR
            SELECT p.id, p.id_usuario, p.id_libro, p.fecha_prestamo,
                   p.fecha_devolucion, p.estado,
                   l.titulo as libro_titulo, l.autor as libro_autor, l.categoria as libro_categoria,
                   u.nombre as usuario_nombre, u.correo as usuario_correo, u.rol as usuario_rol
            FROM prestamos p
            JOIN libros l ON p.id_libro = l.id
            JOIN usuarios u ON p.id_usuario = u.id
            WHERE p.id = p_prestamo_id;
    END OBTENER_PRESTAMO;

    PROCEDURE LISTAR_PRESTAMOS_USUARIO(
        p_usuario_id IN NUMBER,
        p_estado IN VARCHAR2 DEFAULT NULL,
        p_cursor OUT SYS_REFCURSOR
    ) AS
        v_sql VARCHAR2(4000);
    BEGIN
        v_sql := 'SELECT p.id, p.id_usuario, p.id_libro, p.fecha_prestamo,
                         p.fecha_devolucion, p.estado,
                         l.titulo as libro_titulo, l.autor as libro_autor, l.categoria as libro_categoria,
                         u.nombre as usuario_nombre, u.correo as usuario_correo, u.rol as usuario_rol
                  FROM prestamos p
                  JOIN libros l ON p.id_libro = l.id
                  JOIN usuarios u ON p.id_usuario = u.id
                  WHERE p.id_usuario = :usuario_id';
        
        IF p_estado IS NOT NULL THEN
            v_sql := v_sql || ' AND p.estado = :estado';
        END IF;
        
        v_sql := v_sql || ' ORDER BY p.fecha_prestamo DESC';
        
        OPEN p_cursor FOR v_sql USING p_usuario_id, p_estado;
    END LISTAR_PRESTAMOS_USUARIO;

    PROCEDURE LISTAR_PRESTAMOS_LIBRO(p_libro_id IN NUMBER, p_cursor OUT SYS_REFCURSOR) AS
    BEGIN
        OPEN p_cursor FOR
            SELECT p.id, p.id_usuario, p.id_libro, p.fecha_prestamo,
                   p.fecha_devolucion, p.estado,
                   l.titulo as libro_titulo, l.autor as libro_autor, l.categoria as libro_categoria,
                   u.nombre as usuario_nombre, u.correo as usuario_correo, u.rol as usuario_rol
            FROM prestamos p
            JOIN libros l ON p.id_libro = l.id
            JOIN usuarios u ON p.id_usuario = u.id
            WHERE p.id_libro = p_libro_id
            ORDER BY p.fecha_prestamo DESC;
    END LISTAR_PRESTAMOS_LIBRO;

    PROCEDURE OBTENER_PRESTAMOS_VENCIDOS(p_cursor OUT SYS_REFCURSOR) AS
    BEGIN
        OPEN p_cursor FOR
            SELECT p.id, p.id_usuario, p.id_libro, p.fecha_prestamo,
                   p.fecha_devolucion, p.estado,
                   l.titulo as libro_titulo, l.autor as libro_autor, l.categoria as libro_categoria,
                   u.nombre as usuario_nombre, u.correo as usuario_correo, u.rol as usuario_rol,
                   SYSDATE - p.fecha_devolucion as dias_vencido
            FROM prestamos p
            JOIN libros l ON p.id_libro = l.id
            JOIN usuarios u ON p.id_usuario = u.id
            WHERE p.estado = 'ACTIVO' AND p.fecha_devolucion < SYSDATE
            ORDER BY p.fecha_devolucion;
    END OBTENER_PRESTAMOS_VENCIDOS;

    PROCEDURE OBTENER_HISTORIAL_PRESTAMOS(
        p_fecha_inicio IN DATE DEFAULT NULL,
        p_fecha_fin IN DATE DEFAULT NULL,
        p_cursor OUT SYS_REFCURSOR
    ) AS
        v_sql VARCHAR2(4000);
    BEGIN
        v_sql := 'SELECT p.id, p.id_usuario, p.id_libro, p.fecha_prestamo,
                         p.fecha_devolucion, p.estado,
                         l.titulo as libro_titulo, l.autor as libro_autor, l.categoria as libro_categoria,
                         u.nombre as usuario_nombre, u.correo as usuario_correo, u.rol as usuario_rol
                  FROM prestamos p
                  JOIN libros l ON p.id_libro = l.id
                  JOIN usuarios u ON p.id_usuario = u.id
                  WHERE 1=1';
        
        IF p_fecha_inicio IS NOT NULL THEN
            v_sql := v_sql || ' AND p.fecha_prestamo >= :fecha_inicio';
        END IF;
        
        IF p_fecha_fin IS NOT NULL THEN
            v_sql := v_sql || ' AND p.fecha_prestamo <= :fecha_fin';
        END IF;
        
        v_sql := v_sql || ' ORDER BY p.fecha_prestamo DESC';
        
        OPEN p_cursor FOR v_sql USING p_fecha_inicio, p_fecha_fin;
    END OBTENER_HISTORIAL_PRESTAMOS;

    PROCEDURE OBTENER_LIBROS_MAS_PRESTADOS(
        p_limite IN NUMBER DEFAULT 10,
        p_cursor OUT SYS_REFCURSOR
    ) AS
    BEGIN
        OPEN p_cursor FOR
            SELECT l.id, l.titulo, l.autor, l.categoria,
                   COUNT(p.id) as total_prestamos,
                   COUNT(CASE WHEN p.estado = 'DEVUELTO' THEN 1 END) as prestamos_devueltos,
                   COUNT(CASE WHEN p.estado = 'ACTIVO' THEN 1 END) as prestamos_activos,
                   l.stock
            FROM libros l
            LEFT JOIN prestamos p ON l.id = p.id_libro
            GROUP BY l.id, l.titulo, l.autor, l.categoria, l.stock
            ORDER BY total_prestamos DESC
            FETCH FIRST p_limite ROWS ONLY;
    END OBTENER_LIBROS_MAS_PRESTADOS;

    PROCEDURE ACTUALIZAR_ESTADOS_VENCIDOS AS
    BEGIN
        UPDATE prestamos 
        SET estado = 'VENCIDO'
        WHERE estado = 'ACTIVO' AND fecha_devolucion < SYSDATE;
        
        COMMIT;
    EXCEPTION
        WHEN OTHERS THEN
            ROLLBACK;
            RAISE;
    END ACTUALIZAR_ESTADOS_VENCIDOS;

    PROCEDURE LISTAR_PRESTAMOS(
        p_estado IN VARCHAR2 DEFAULT NULL,
        p_cursor OUT SYS_REFCURSOR
    ) AS
        v_sql VARCHAR2(4000);
    BEGIN
        v_sql := 'SELECT p.id, p.id_usuario, p.id_libro, p.fecha_prestamo,
                         p.fecha_devolucion, p.estado,
                         l.titulo as libro_titulo, l.autor as libro_autor, l.categoria as libro_categoria,
                         u.nombre as usuario_nombre, u.correo as usuario_correo, u.rol as usuario_rol
                  FROM prestamos p
                  JOIN libros l ON p.id_libro = l.id
                  JOIN usuarios u ON p.id_usuario = u.id
                  WHERE 1=1';
        
        IF p_estado IS NOT NULL THEN
            v_sql := v_sql || ' AND p.estado = :estado';
        END IF;
        
        v_sql := v_sql || ' ORDER BY p.fecha_prestamo DESC';
        
        IF p_estado IS NOT NULL THEN
            OPEN p_cursor FOR v_sql USING p_estado;
        ELSE
            OPEN p_cursor FOR v_sql;
        END IF;
    END LISTAR_PRESTAMOS;

    PROCEDURE ELIMINAR_PRESTAMO(
        p_prestamo_id IN NUMBER
    ) AS
    BEGIN
        DELETE FROM prestamos WHERE id = p_prestamo_id;
    END ELIMINAR_PRESTAMO;

    -- Obtener los 5 préstamos más recientes
    PROCEDURE OBTENER_PRESTAMOS_RECIENTES(
        p_cursor OUT SYS_REFCURSOR
    ) AS
    BEGIN
        OPEN p_cursor FOR
            SELECT * FROM (
                SELECT p.id, p.id_libro, p.id_usuario, p.fecha_prestamo, p.fecha_devolucion, p.estado,
                       l.titulo as libro_titulo, u.nombre as usuario_nombre
                FROM prestamos p
                JOIN libros l ON p.id_libro = l.id
                JOIN usuarios u ON p.id_usuario = u.id
                ORDER BY p.fecha_prestamo DESC, p.id DESC
            )
            WHERE ROWNUM <= 3;
    END OBTENER_PRESTAMOS_RECIENTES;

END PKG_PRESTAMOS;
/



-- TRIGGERS
-- Trigger para validar fechas de préstamo
CREATE OR REPLACE TRIGGER TRG_PRESTAMO_FECHAS
BEFORE INSERT OR UPDATE ON prestamos
FOR EACH ROW
BEGIN
    -- Validar que la fecha de devolución sea posterior a la fecha de préstamo
    IF :NEW.fecha_devolucion <= :NEW.fecha_prestamo THEN
        RAISE_APPLICATION_ERROR(-20008, 'La fecha de devolución debe ser posterior a la fecha de préstamo');
    END IF;
END;
/



-- FUNCIONES UTILITARIAS
-- Función para calcular días de retraso
CREATE OR REPLACE FUNCTION CALCULAR_DIAS_RETRASO(
    p_fecha_devolucion IN DATE
) RETURN NUMBER AS
BEGIN
    IF p_fecha_devolucion < SYSDATE THEN
        RETURN SYSDATE - p_fecha_devolucion;
    ELSE
        RETURN 0;
    END IF;
END;
/

-- Función para verificar si un libro está disponible
CREATE OR REPLACE FUNCTION LIBRO_DISPONIBLE(
    p_libro_id IN NUMBER
) RETURN BOOLEAN AS
    v_stock NUMBER;
BEGIN
    SELECT stock INTO v_stock
    FROM libros WHERE id = p_libro_id;
    
    RETURN v_stock > 0;
EXCEPTION
    WHEN NO_DATA_FOUND THEN
        RETURN FALSE;
END;
/

-- Función para obtener estadísticas de préstamos
CREATE OR REPLACE FUNCTION OBTENER_ESTADISTICAS_PRESTAMOS(
    p_fecha_inicio IN DATE DEFAULT NULL,
    p_fecha_fin IN DATE DEFAULT NULL
) RETURN SYS_REFCURSOR AS
    v_cursor SYS_REFCURSOR;
    v_sql VARCHAR2(4000);
BEGIN
    v_sql := 'SELECT 
                COUNT(*) as total_prestamos,
                COUNT(CASE WHEN estado = ''ACTIVO'' THEN 1 END) as prestamos_activos,
                COUNT(CASE WHEN estado = ''DEVUELTO'' THEN 1 END) as prestamos_devueltos,
                COUNT(CASE WHEN estado = ''VENCIDO'' THEN 1 END) as prestamos_vencidos,
                COUNT(CASE WHEN fecha_devolucion < SYSDATE AND estado = ''ACTIVO'' THEN 1 END) as prestamos_retrasados
              FROM prestamos WHERE 1=1';
    
    IF p_fecha_inicio IS NOT NULL THEN
        v_sql := v_sql || ' AND fecha_prestamo >= :fecha_inicio';
    END IF;
    
    IF p_fecha_fin IS NOT NULL THEN
        v_sql := v_sql || ' AND fecha_prestamo <= :fecha_fin';
    END IF;
    
    OPEN v_cursor FOR v_sql USING p_fecha_inicio, p_fecha_fin;
    RETURN v_cursor;
END;
/ 