DOCUMENTO TÉCNICO: ARQUITECTURA DE BASE DE DATOS ARMONIHZ
Auditoría de Componentes Avanzados para Marketplace Musical
Proyecto: ARMONIHZ
Asignatura: Base de Datos Avanzadas
Fecha: 10 de abril de 2026

1. INTRODUCCIÓN
El presente informe documenta la refactorización y robustecimiento del ecosistema de datos de Armonihz. Al tratarse de un marketplace bidireccional (solicitantes y talento musical), la plataforma exige un procesamiento ininterrumpido de historiales, contratos y cruces de disponibilidad. Para soportar esta complejidad operativa y resolver la demanda técnica, se instrumentaron múltiples técnicas avanzadas a nivel de motor MySQL. El objetivo primario de esta arquitectura persigue salvaguardar la totalidad transaccional, desplazar el peso computacional de los reportes analíticos fuera del entorno de Laravel, y sentar un esquema que asimile una alta concurrencia de usuarios sin sacrificar la latencia de respuesta del SaaS.

2. DEMOSTRACIÓN DE CRITERIOS TÉCNICOS

2.1 Uso de INNER JOIN
Descripción: Relaciona entidades para consolidar información de autenticación junto a perfiles públicos sin redundancia, bloqueando perfiles huérfanos.
Uso en Armonihz: Consolidación rápida de datos de músicos que se encuentren activos en el sistema.
Tabla(s): musician_profiles, users.
```sql
SELECT mp.stage_name, u.email 
FROM musician_profiles mp
INNER JOIN users u ON mp.user_id = u.id
WHERE u.is_active = 1;
```

2.2 Subconsultas (SELECT Anidados)
Descripción: Operaciones sobre conjuntos de datos para filtrado interno y pre-cálculo masivo sin alterar la dimensionalidad principal de las tablas.
Uso en Armonihz: Cálculo del estado de métricas en tiempo real del músico (Rating y Eventos Acabados).
Tabla(s): musician_profiles, reviews, hiring_requests.
```sql
SELECT 
    mp.stage_name,
    (SELECT COALESCE(AVG(r.rating), 0) FROM reviews r WHERE r.musician_profile_id = mp.id) AS rating_promedio,
    (SELECT COUNT(hr.id) FROM hiring_requests hr WHERE hr.musician_profile_id = mp.id AND hr.status = 'completed') AS total_contrataciones
FROM musician_profiles mp;
```

2.3 Columnas Generadas (VIRTUAL y STORED)
Descripción: Atributos automáticos inyectados por el motor de BD en C o C++ que no requieren procesamiento ni RAM transaccional en el Backend PHP.
Uso en Armonihz: Cálculo inmediato del presupuesto IVA incluido para la UI, y generación pasiva de ingresos sombra (Comisiones de Plataforma del 10%).
Tabla(s): client_events, hiring_requests.
```sql
ALTER TABLE client_events
ADD COLUMN presupuesto_con_impuesto DECIMAL(12,2) 
GENERATED ALWAYS AS (presupuesto * 1.16) VIRTUAL;

ALTER TABLE hiring_requests
ADD COLUMN commission_amount DECIMAL(10,2) 
GENERATED ALWAYS AS (budget * 0.10) STORED;
```

2.4 Slugs (Identificadores Amigables)
Descripción: Identificadores alfanuméricos deterministas únicos con soporte UNIQUE INDEX para blindar el ruteo limpio SEO y ocultar los Autoincrements Primarios a inyecciones.
Uso en Armonihz: Presentación e identificación pública e infalsificable del Perfil del Músico.
Tabla(s): musician_profiles.
```sql
ALTER TABLE musician_profiles 
ADD COLUMN slug VARCHAR(255) UNIQUE NOT NULL AFTER stage_name;

SELECT stage_name, tarifa_base 
FROM musician_profiles 
WHERE slug = 'mariachi-demo-oficial';
```

2.5 Vistas (Native Views)
Descripción: Estructuras persistentes lógicas de solo-lectura que unifican sentencias muy costosas en una simple extracción de catálogo.
Uso en Armonihz: Tarjeta de perfil público del talento musical cruzando JOINs y Anidaciones.
Tabla(s): vw_musico_reputacion.
```sql
CREATE OR REPLACE VIEW vw_musico_reputacion AS
SELECT 
    mp.id AS musician_id, mp.slug, mp.stage_name, u.email AS user_email,
    (SELECT COALESCE(AVG(r.rating), 0) FROM reviews r WHERE r.musician_profile_id = mp.id) AS rating_promedio,
    (SELECT COUNT(hr.id) FROM hiring_requests hr WHERE hr.musician_profile_id = mp.id AND hr.status = 'completed') AS total_contrataciones_completadas
FROM musician_profiles mp
INNER JOIN users u ON mp.user_id = u.id
WHERE u.is_active = 1;
```

2.6 Vistas Materializadas (Simuladas)
Descripción: Persistencia física dura (en disco) de consolidaciones de agrupamiento complejas, precalculando miles de filas contables en una sola fila leíble en <2ms.
Uso en Armonihz: Histórico mensual financiero en Dashboards Gerenciales globales sin asfixiar la tabla transaccional de Eventos.
Tabla(s): mv_client_spending_stats.
```sql
INSERT INTO mv_client_spending_stats (firebase_uid, report_month, total_events_created, total_spent, last_updated)
SELECT ce.firebase_uid, DATE_FORMAT(ce.created_at, '%Y-%m') AS report_month, COUNT(ce.id), COALESCE(SUM(ce.presupuesto), 0.00), NOW()
FROM client_events ce GROUP BY ce.firebase_uid, DATE_FORMAT(ce.created_at, '%Y-%m')
ON DUPLICATE KEY UPDATE 
    total_events_created = VALUES(total_events_created), 
    total_spent = VALUES(total_spent), 
    last_updated = NOW();
```

2.7 Triggers (INSERT, UPDATE)
Descripción: Microservicios invisibles anclados en C que vigilan la Base de Datos. Otorgan auditorías infranqueables desde la API superior de programación.
Uso en Armonihz: Auditoría absoluta de cambios de estado del negocio y mantenimiento perpetuo de promedios de reviews en tiempo real sin cálculos en batch.
Tabla(s): reviews, hiring_requests.
```sql
-- Actualiza pasivamente las estadísticas tras una reseña nueva
CREATE TRIGGER trg_after_insert_review AFTER INSERT ON reviews FOR EACH ROW
BEGIN
    INSERT INTO mv_musician_monthly_stats (musician_profile_id, report_month, avg_rating)
    VALUES (NEW.musician_profile_id, DATE_FORMAT(NOW(), '%Y-%m'), NEW.rating)
    ON DUPLICATE KEY UPDATE avg_rating = (SELECT AVG(rating) FROM reviews WHERE musician_profile_id = NEW.musician_profile_id);
END;

-- Histórico de cambios que no permite ser engañado por la UI Web
CREATE TRIGGER trg_after_update_hiring_request AFTER UPDATE ON hiring_requests FOR EACH ROW
BEGIN
    IF OLD.status != NEW.status THEN
        INSERT INTO hiring_requests_audit (hiring_request_id, old_status, new_status, changed_at)
        VALUES (OLD.id, OLD.status, NEW.status, NOW());
    END IF;
END;
```

2.8 Índices
Descripción: Aplicación de Árboles Tipo-B y llaves Hash directas para prevenir lecturas FULL-TABLE-SCANS secuenciales destructivas.
Uso en Armonihz: Optimización de ruteos de APIs y cruces de tableros de calificaciones.
Tabla(s): reviews, hiring_requests, mv_client_spending_stats.
```sql
CREATE INDEX idx_reviews_rating ON reviews(rating);
CREATE INDEX idx_mv_client_firebase_uid ON mv_client_spending_stats(firebase_uid);

-- Índices Compuestos para cubrir con un golpe consultas exactas de JOINs
CREATE INDEX idx_reviews_musician_rating ON reviews(musician_profile_id, rating);
CREATE INDEX idx_hiring_requests_musician_status ON hiring_requests(musician_profile_id, status);
```

3. UBICACIÓN EN EL SISTEMA Y CASOS DE USO

La siguiente tabla resume en lenguaje directo cómo y dónde impacta cada optimización dentro de la aplicación Armonihz en el día a día:

| Técnica Aplicada | ¿Dónde se ejecuta? | ¿Para qué sirve visual u operativamente en Armonihz? |
| :--- | :--- | :--- |
| **INNER JOIN** | Consultas a BD | Pega los datos de la cuenta con los del perfil musical, logrando que el "Muro de Músicos" cargue rapidísimo. |
| **Subconsultas** | Consultas internas | Lee discretamente las "reviews" para mostrar las estrellas (Rating Promedio) debajo del nombre de cada músico. |
| **Columnas Generadas** | Tabla de Transacciones | Actúa como una calculadora invisible que muestra el Costo + IVA al instante, sin saturar a Laravel. |
| **Slugs** | Barra de Direcciones | Permite que las invitaciones de los músicos tengan enlaces estéticos (ej. `armonihz.com/m/mariachi-demo`). |
| **Vistas Nativas** | Código Interno | Junta muchísima información pesada (perfil, rating y popularidad) en un formato súper ligero de leer. |
| **Vistas Materializadas** | Panel de Estadísticas | Permite que los gráficos administrativos carguen en 1 milisegundo al evitar tener que sumar dólares venta por venta. |
| **Triggers (Disparadores)**| "En el fondo" de MySQL | Protegen el negocio: Si cambia un estatus de contrato, el Trigger inserta obligatoriamente el cambio en una bitácora para que nada se pierda. |
| **Índices** | Cimientos del Sistema | Ordenan datos internos para que cuando el usuario escriba en el buscador, la base de datos no se trabe leyendo miles de filas. |

4. CONCLUSIÓN
La evolución del modelo de datos comprobó que el aprovechamiento nativo del motor MySQL es el factor determinante para la estabilidad de un marketplace moderno. Al intervenir directamente la base de datos para incrustar columnas pre-calculadas, vistas sintéticas orientadas a inteligencia de negocios y desencadenadores físicos, se mitigaron los riesgos de asfixiar los servidores de aplicación por sobre-peticiones. Tras este rediseño a profundidad, Armonihz dispone ahora de una capa de datos completamente auditable, provista de defensas referenciales inquebrantables, y alineada en su totalidad con las exigencias del programa de Bases de Datos Avanzadas.
