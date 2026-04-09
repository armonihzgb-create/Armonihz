# Informe Técnico: Optimización y Auditoría de Base de Datos Armonihz

Este documento resume la implementación técnica realizada sobre la base de datos de Armonihz. Se ha diseñado y aplicado un conjunto de mejoras a nivel de esquema (DDL) y motor para cumplir de forma orgánica con la rúbrica de **Bases de Datos Avanzadas**, sin romper la robustez de Eloquent/Laravel ni el funcionamiento de la aplicación móvil.

## 1. Arquitectura de la Implementación de Base de Datos

Se han añadido 6 nuevas migraciones estructuradas que inyectan funcionalidades nativas del motor en lugar de saturar el backend con código PHP:

1. **`add_slugs_to_musician_profiles_and_event_types`**: Inyecta índices UNIQUE y reestructura los datos antiguos para soportar URLs amigables (SEO).
2. **`add_generated_columns_to_events_and_requests`**: Mueve la carga o cálculo constante de impuestos y comisiones al Motor de Base de Datos a través de columnas `VIRTUAL` y `STORED`.
3. **`create_view_vw_musico_reputacion`**: Sintetiza en una sola llamada el rating del músico, uniones de perfil y recuento de subconsultas usando vistas nativas.
4. **`create_materialized_view_simulation_tables`**: Implementa el patrón *Snapshot Table* como sustituto elegante a las **Vistas Materializadas** para el Dashboard gerencial.
5. **`create_triggers_and_audit_table`**: Introduce rastreo absoluto (Auditoría) para eventos de contratación garantizando inmutabilidad sobre reportes financieros.
6. **`add_indexes_for_rubrica`**: Implementa aceleración manual en llaves secundarias usadas por el controlador.

---

## Cumplimiento de la Rúbrica (Pruebas Exitosas)

A continuación, los requerimientos satisfechos de la base de datos con su explicación directa:

### 1. Consultas INNER JOIN y 2. Subconsultas (Anidadas) y 3. Vistas
> Estas operaciones se agruparon en la creación lógica de la **Vista `vw_musico_reputacion`**, protegiendo las sentencias densas del ORM.

**Script Ejecutado (Disponible en Migración 100003):**
```sql
CREATE OR REPLACE VIEW vw_musico_reputacion AS
SELECT
    mp.id AS musician_id,
    mp.slug,
    mp.stage_name,
    u.email AS user_email,
    -- Subconsulta Anidada 1 (Ratings Promedio via Subquery)
    (
        SELECT COALESCE(AVG(r.rating), 0)
        FROM reviews r
        WHERE r.musician_profile_id = mp.id
    ) AS rating_promedio,
    -- Subconsulta Anidada 2 (Total)
    (
        SELECT COUNT(hr.id)
        FROM hiring_requests hr
        WHERE hr.musician_profile_id = mp.id AND hr.status = 'completed'
    ) AS total_contrataciones_completadas

FROM musician_profiles mp
-- Operador INNER JOIN 
INNER JOIN users u ON mp.user_id = u.id
WHERE u.is_active = 1;
```
* **Justificación técnica:** Este `VIEW` resuelve 3 rubros a la vez. El INNER JOIN erradica inconsistencias si hay perfiles "fantasma" huérfanos de su usuario principal, y las *Correlated Subqueries* proveen un pre-casting en O(N) para la UI de búsqueda.

### 4. Columnas generadas en 2 tablas
Se alteraron las tablas `client_events` y `hiring_requests`:

```sql
-- TABLA 1 (Tipo Virtual = Calculado en tiempo de lectura, sin uso de disco)
ALTER TABLE client_events
ADD COLUMN presupuesto_con_impuesto DECIMAL(12,2) 
GENERATED ALWAYS AS (presupuesto * 1.16) VIRTUAL;

-- TABLA 2 (Tipo Stored = Calculado y persistido al insertar, apto para índices)
ALTER TABLE hiring_requests
ADD COLUMN commission_amount DECIMAL(10,2) 
GENERATED ALWAYS AS (budget * 0.10) STORED;
```

### 5. Implementación de Slugs
Se utilizó el hook `saving` de Eloquent (en Laravel `MusicianProfile.php` y `EventType.php`) para atrapar y autogenerar slugs en la base de datos de manera atómica con un constraint de motor `UNIQUE`.

```sql
ALTER TABLE musician_profiles ADD COLUMN slug VARCHAR(255) UNIQUE AFTER stage_name;
ALTER TABLE event_types ADD COLUMN slug VARCHAR(255) UNIQUE AFTER name;
```

### 6. Vistas Materializadas en 2 tablas (Simulación y Refresco)
Para solventar la carencia de *Materialized Views* nativas en la familia MySQL, se implementaron **Snapshot Tables** y un job de refresco recurrente usando los features de inserción atómica `ON DUPLICATE KEY UPDATE`.

Se configuraron las tablas:
1. `mv_client_spending_stats` (Dashboard de Clientes usando UID)
2. `mv_musician_monthly_stats` (Dashboard Mensual de Balance/Profit Músicos)

```php
// El comando Artisan stats:refresh-mv ejecutará periódicamente el siguiente modelo de bloque SQL:
INSERT INTO mv_client_spending_stats 
    (firebase_uid, report_month, total_events_created, total_spent, last_updated)
SELECT
    ce.firebase_uid,
    DATE_FORMAT(ce.created_at, '%Y-%m') AS report_month,
    COUNT(ce.id),
    COALESCE(SUM(ce.presupuesto), 0.00),
    NOW()
FROM client_events ce 
GROUP BY ce.firebase_uid, DATE_FORMAT(ce.created_at, '%Y-%m')
ON DUPLICATE KEY UPDATE
    total_events_created = VALUES(total_events_created),
    total_spent          = VALUES(total_spent),
    last_updated         = NOW();
```

### 7. Disparadores (Triggers) en 2 tablas
Se enfocó el uso nativo para prevenir latencias en el backend de Node/PHP:

```sql
-- Trigger en Tabla reviews
-- Mantener viva y atómica la vista materializada simulada con cálculos asíncronos en disco.
CREATE TRIGGER trg_after_insert_review
AFTER INSERT ON reviews FOR EACH ROW
BEGIN
    INSERT INTO mv_musician_monthly_stats (...)
    SELECT ... NEW.rating ...
    ON DUPLICATE KEY UPDATE avg_rating = (...); -- (Version reducida para legibilidad de reporte)
END;

-- Trigger en Tabla hiring_requests
-- Forzar un rastreo de eventos (Auditoría Ciega) que es independiente de la manipulación aplicativa.
CREATE TRIGGER trg_after_update_hiring_request
AFTER UPDATE ON hiring_requests FOR EACH ROW
BEGIN
    IF OLD.status != NEW.status THEN
        INSERT INTO hiring_requests_audit (hiring_request_id, old_status, new_status, changed_at)
        VALUES (OLD.id, OLD.status, NEW.status, NOW());
    END IF;
END;
```

### 8. Índices Individuales y 9. Compuestos en 2 tablas
Se blindó la infraestructura operativa de la tabla aplicando el particionado lógico tradicional:

```sql
-- 1. Indexaciones Categóricas/Filtrado Diario de UI (Individual)
CREATE INDEX idx_reviews_rating ON reviews(rating);
CREATE INDEX idx_mv_client_firebase_uid ON mv_client_spending_stats(firebase_uid);

-- 2. Indexaciones de Cruce Múltiple/Join Predictivo (Compuestos)
CREATE INDEX idx_reviews_musician_rating ON reviews(musician_profile_id, rating);
CREATE INDEX idx_hiring_requests_musician_status ON hiring_requests(musician_profile_id, status);
```

---

## Certificación

✅ **Ejecución Lógica Pura:** Completada con éxito a nivel DDL (Data Definition Language) de MySQL sin dependencias destructivas de ORM.
✅ **Modelado Laravel Complementario:** Modelos Read-Only Eloquent y Comandos Artisan generados sobre la capa de infraestructura para integración fluida sin sobrecarga del código existente.
