# Plan de Implementación y Auditoría de Base de Datos (Armonihz)

Este documento contiene la auditoría completa de la base de datos de la plataforma Armonihz, evaluada contra la rúbrica del curso de "Base de Datos Avanzadas", además de las instrucciones técnicas y sentencias SQL necesarias para cumplir con los requerimientos faltantes.

## Fase 1 — Auditoría de la base de datos actual

A continuación, se presenta la lista de verificación del esquema actual versus los requerimientos de la rúbrica:

| Criterio de Evaluación | Estado | Detalle en el esquema actual |
| --- | --- | --- |
| 1. Consultas INNER JOIN | ❌ Faltante | Se realizan mediante Eloquent/Query Builder a nivel backend, pero no están documentadas como sentencias SQL nativas ni integradas en vistas en la base de datos. |
| 2. Consultas SELECT anidadas | ❌ Faltante | Mismo caso que las uniones; manejado por ORM. Se requieren scripts SQL explícitos. |
| 3. Columnas generadas (2 tablas) | ❌ Faltante | No se detectan columnas VIRTUAL_AS o STORED_AS en las migraciones (ej. `virtualAs()`). |
| 4. Implementación de slugs | ❌ Faltante | La tabla `musician_profiles` no cuenta con una columna `slug` para URLs amigables. |
| 5. Uso de al menos una vista | ❌ Faltante | No existen migraciones que implementen vistas (ej. `DB::statement('CREATE VIEW...')`). |
| 6. Vistas materializadas (o simulación en 2 tablas) | ❌ Faltante | No existen tablas de agregación o snapshots utilizadas para cachear consultas pesadas (común en MySQL). |
| 7. Disparadores (Triggers en 2 tablas) | ❌ Faltante | Ausencia de triggers de auditoría o de agregación automática en eventos operativos. |
| 8. Índices individuales (2 tablas) | ⚠️ Parcial | Existen en `users.email` y en foreign keys, pero es ideal agregar índices sobre campos de consulta frecuentes (ej. `reviews.rating` o fechas). |
| 9. Índices compuestos (2 tablas) | ✅ Completo | Implementados en tablas como `casting_applications` (`client_event_id`, `musician_profile_id`) y `client_musician_favorites`. |
| 10. Documentación técnica y SQL | ❌ Faltante | (Se proporcionará mediante la creación de este documento y su Fase 4). |

---

## Fase 2 — Plan de funcionalidades faltantes

Para resolver los rubros faltantes de forma coherente con el negocio de Armonihz sin afectar la estabilidad actual, se proponen las siguientes integraciones:

1. **Slugs (URLs amigables):**
   * **Lugares (Tablas):** `musician_profiles` y `event_types`.
   * **Contexto:** Armonihz requiere URLs limpias para el SEO de músicos (ej. `armonihz.com/m/juan-perez-band`).
2. **Columnas Generadas:**
   * **Lugar 1:** `client_events` -> Generar columna `presupuesto_total` incluyendo el IVA (virtual).
   * **Lugar 2:** `hiring_requests` -> Generar columna `comision_plataforma` calculando el 10% del `budget` fijado (virtual).
3. **Vistas Tradicionales y Consultas:**
   * Crear la vista `vw_musico_reputacion` que involucre un `INNER JOIN` (perfil + usuario) y una subconsulta para conocer su rating promedio de la tabla `reviews`.
4. **Vistas Materializadas (Simulación):**
   * Ya que MySQL/MariaDB no soporta `MATERIALIZED VIEW` nativamente, se simularán usando tablas "resumen" que se alimentan periódicamente.
   * **Lugar 1:** Tabla `mv_musician_monthly_stats` (estadísticas mensuales de ganancias y contrataciones).
   * **Lugar 2:** Tabla `mv_client_spending_stats` (gastos de clientes por periodo).
5. **Triggers (Disparadores):**
   * **Lugar 1:** Tabla `hiring_requests` -> Trigger `AFTER UPDATE` para guardar el registro de cambios de estados (historial) en `hiring_requests_audit`.
   * **Lugar 2:** Tabla `reviews` -> Trigger `AFTER INSERT` para actualizar un contador de "resumen" numérico de reseñas directamente.
6. **Índices Individuales:**
   * Indexar `firebase_uid` en `client_events` (si no lo está como index simple nativo) y el `rating` de la tabla `reviews` para agilizar filtros en el Dashboard.

---

## Fase 3 — Implementación SQL

> [!TIP]
> **Integración Backend:** Estos comandos pueden empaquetarse en nuevas migraciones dentro de Laravel utilizando `DB::unprepared()` o `DB::statement()`, ejecutándose sin romper el flujo actual de Eloquent.

A continuación, los scripts SQL completamente comentados listos para tu informe técnico.

### 1. Consultas INNER JOIN y 2. Subconsultas y 5. Vistas

```sql
-- Creación de una vista que relaciona perfiles, usuarios y usa subconsultas
CREATE OR REPLACE VIEW vw_musico_reputacion AS
SELECT 
    mp.id AS musician_id,
    mp.stage_name,
    u.email,
    mp.location,
    -- Subconsulta anidada para calcular el rating promedio del músico
    (SELECT COALESCE(AVG(rating), 0) FROM reviews r WHERE r.musician_profile_id = mp.id) AS average_rating,
    -- Subconsulta anidada para contar el total de reseñas
    (SELECT COUNT(id) FROM reviews r WHERE r.musician_profile_id = mp.id) AS total_reviews
FROM 
    musician_profiles mp
-- INNER JOIN para relacionar al músico con la tabla de credenciales (users)
INNER JOIN 
    users u ON mp.user_id = u.id;
```

### 3. Columnas Generadas en al menos 2 tablas

```sql
-- TABLA 1: Agregar columna generada a client_events para calcular el presupuesto final con IVA (16%)
ALTER TABLE client_events
ADD COLUMN presupuesto_con_impuesto DECIMAL(10,2) 
GENERATED ALWAYS AS (presupuesto * 1.16) VIRTUAL;

-- TABLA 2: Agregar columna generada a hiring_requests para calcular el profit (margen) de plataforma (10%)
ALTER TABLE hiring_requests
ADD COLUMN commission_amount DECIMAL(10,2) 
GENERATED ALWAYS AS (budget * 0.10) STORED;
```

### 4. Implementación de Slugs

```sql
-- Agregar columna slug para permitir URLs limpias e identificación SEO
ALTER TABLE musician_profiles
ADD COLUMN slug VARCHAR(255) NULL UNIQUE AFTER stage_name;

ALTER TABLE event_types
ADD COLUMN slug VARCHAR(255) NULL UNIQUE AFTER name;
```

### 6. Uso o simulación de Vistas Materializadas (2 tablas)

```sql
-- TABLA_MATERIALIZADA_1: Estadísticas Mensuales de Músicos
CREATE TABLE mv_musician_monthly_stats (
    id INT AUTO_INCREMENT PRIMARY KEY,
    musician_profile_id BIGINT UNSIGNED,
    report_month VARCHAR(7), -- YYYY-MM
    total_hired INT DEFAULT 0,
    total_earned DECIMAL(12,2) DEFAULT 0.00,
    last_updated TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (musician_profile_id) REFERENCES musician_profiles(id) ON DELETE CASCADE,
    UNIQUE(musician_profile_id, report_month)
);

-- TABLA_MATERIALIZADA_2: Estadísticas Mensuales de Clientes
CREATE TABLE mv_client_spending_stats (
    id INT AUTO_INCREMENT PRIMARY KEY,
    client_id BIGINT UNSIGNED,
    report_month VARCHAR(7), -- YYYY-MM
    total_events_created INT DEFAULT 0,
    total_spent DECIMAL(12,2) DEFAULT 0.00,
    last_updated TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (client_id) REFERENCES users(id) ON DELETE CASCADE,
    UNIQUE(client_id, report_month)
);

-- (La simulación se completa poblando estas tablas con Stored Procedures que pueden correrse con Laravel Task Scheduler / CRON)
```

### 7. Disparadores (Triggers) en al menos 2 tablas

```sql
-- Crear tabla de auditoría para Hiring Requests
CREATE TABLE hiring_requests_audit (
    id INT AUTO_INCREMENT PRIMARY KEY,
    hiring_request_id BIGINT UNSIGNED,
    old_status VARCHAR(50),
    new_status VARCHAR(50),
    changed_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- TRIGGER_1: Disparador AFTER UPDATE para rastrear el cambio de estatus en contrataciones
DELIMITER //
CREATE TRIGGER trg_after_update_hiring_request
AFTER UPDATE ON hiring_requests
FOR EACH ROW
BEGIN
    -- Validamos si la columna 'status' fue la modificada
    IF OLD.status != NEW.status THEN
        INSERT INTO hiring_requests_audit (hiring_request_id, old_status, new_status, changed_at)
        VALUES (OLD.id, OLD.status, NEW.status, NOW());
    END IF;
END;
//
DELIMITER ;

-- TRIGGER_2: Disparador AFTER INSERT en reseñas (reviews)
-- Permite un sistema escalable para enviar notificaciones automáticas asíncronas
DELIMITER //
CREATE TRIGGER trg_after_insert_review
AFTER INSERT ON reviews
FOR EACH ROW
BEGIN
    -- Al insertarse una review, insertamos un registro en notifications (tabla nativa) avisando al músico
    -- Supongamos una notificación básica referenciando a quien lo califica.
    INSERT INTO notifications (id, type, notifiable_type, notifiable_id, data, created_at, updated_at)
    VALUES (
        UUID(), 
        'App\\Notifications\\NewReview', 
        'App\\Models\\MusicianProfile', 
        NEW.musician_profile_id, 
        CONCAT('{"rating":', NEW.rating, '}'), 
        NOW(), NOW()
    );
END;
//
DELIMITER ;
```

### 8. Índices individuales & 9. Índices compuestos

```sql
-- Índices Individuales (Agilización de búsquedas por campos comunes)
CREATE INDEX idx_client_events_firebase_uid ON client_events(firebase_uid);
CREATE INDEX idx_reviews_rating ON reviews(rating);

-- Índices Compuestos (Ya existentes en el sistema, ejemplo para doc)
-- CREATE UNIQUE INDEX event_type_profile_unique ON event_type_musician_profile (event_type_id, musician_profile_id);
-- CREATE UNIQUE INDEX client_musician_favorites_unique ON client_musician_favorites (client_id, musician_profile_id);
```

---

## Fase 4 — Estructura de la Documentación Técnica (Propuesta de Informe)

Para la redacción del reporte entregable de la materia, se sugiere la siguiente estructura:

### 1. Introducción
*   **Propósito del Documento:** Detallar las optimizaciones aplicadas a la base de datos de "Armonihz".
*   **Público Objetivo:** Usuarios de contratación de músicos y perfiles de gestión musical.

### 2. Descripción General de la Arquitectura de la Base de Datos
*   Explicar la relación principal entre `users`, `clients`, `musician_profiles`, `client_events`, y `hiring_requests`.
*   Mención obligatoria al uso de motor InnoDB para control de integridad referencial e índices.

### 3. Explicación de las Uniones (JOIN)
*   **Sección:** Presentar el script de `vw_musico_reputacion` y explicar porqué usar *INNER JOIN* permite descartar músicos "huérfanos" (sin perfil `user` mapeado).

### 4. Explicación de las Subconsultas
*   **Sección:** Continuar sobre `vw_musico_reputacion`, señalando cómo las tablas anidadas en el ciclo `SELECT` actúan para agrupar (agg) calificaciones al vuelo por cada registro.

### 5. Explicación de las Columnas Generadas
*   Mostrar las integraciones hechas en `client_events` (IVA) y `hiring_requests` (comisiones).
*   **Justificación de Rendimiento:** Explicar las diferencias entre `VIRTUAL` (cálculo al vuelo) vs `STORED` (creado en disco), aplicándolo a la comisión constante.

### 6. Implementación de Slugs
*   Mostrar las sentencias `ALTER TABLE` que integran `slugs`.
*   Explicar que la meta es el mejoramiento de SEO y ocultamiento del ID en las URIs (seguridad indirecta).

### 7. Vistas y Vistas Materializadas
*   **Vistas Regulares:** Justificación de abstracción al construir capas de acceso para resguardar las sentencias complejas en backend.
*   **Vistas Materializadas (Simulación):** Explicar como la falta de soporte de MySQL fuerza el uso de tablas agregadas en `mv_musician_monthly_stats`, beneficiante para la carga rápida de un Dashboard.

### 8. Lógica de los Disparadores (Triggers)
*   Explicar detalladamente el trigger de `hiring_requests_audit` como herramienta de supervisión de logs.
*   Describir el papel automático del trigger de `reviews` en notificaciones para deslindar el backend de estas micro-tareas transaccionales nativas.

### 9. Estrategia de Índices
*   Mostrar el índice sobre `reviews.rating` para búsquedas "Top Músicos".
*   Mostrar el valor de índices compuestos preexistentes para prevenir registros fotocopia (doble aplicación a casting/favoritos).

### 10. Conclusión
*   Concluir demostrando cómo el conjunto de las prácticas SQL avanzadas robustecen "Armonihz", asegurando que el backend Laravel actúe ágil y la base de datos no sea únicamente un medio de almacén estático.

## Próximos Pasos (Opcionales)
Si requieres que transforme temporalmente estos comandos SQL en **archivos de Migración de Laravel** (para que entren oficialmente a tu sistema versionado y en producción) házmelo saber para generarlos mediante `DB::unprepared(...)`; en caso contrario tienes todo aquí para el reporte.
