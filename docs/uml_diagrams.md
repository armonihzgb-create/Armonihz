# Diagramas UML por Vista - Armonihz Web

Aquí tienes los diagramas UML detallados y separados por módulos/vistas para mayor claridad. Puedes usar estos códigos en cualquier herramienta compatible con PlantUML o Mermaid.

## 1. Autenticación (Login/Registro)
**Vista:** `auth.login`, `auth.register`
**Descripción:** Flujo de entrada de usuarios a la plataforma.

```plantuml
@startuml
!theme plain
title Autenticación - Armonihz

start
:Usuario accede a la Plataforma;

if (¿Tiene cuenta?) then (Sí)
  :Ingresa Credenciales (Email/Password);
  if (Validación correcta?) then (Sí)
    :Acceso Concedido;
    :Redirigir a Dashboard;
  else (No)
    :Mostrar Error;
    stop
  endif
else (No)
  :Formulario de Registro;
  :Ingresa Datos Personales;
  :Crear Cuenta;
  :Redirigir a Dashboard;
endif

stop
@enduml
```

## 2. Perfil del Músico
**Vista:** `profile`
**Descripción:** Visualización y edición de la información del músico.

```plantuml
@startuml
!theme plain
title Gestión de Perfil - Armonihz

|Usuario|
start
:Accede a "Mi Perfil";

if (¿Quiere Editar?) then (Sí)
  :Clic en "Editar Perfil";
  fork
    :Actualizar Foto;
  fork again
    :Modificar Bio;
  fork again
    :Seleccionar Géneros;
  end fork
  :Guardar Cambios;
else (No)
  :Ver Información Pública;
endif

stop
@enduml
```

## 3. Multimedia (Portafolio)
**Vista:** `multimedia`
**Descripción:** Gestión de fotos y videos del músico.

```plantuml
@startuml
!theme plain
title Modulo Multimedia - Armonihz

|Músico|
start
:Accede a Sección Multimedia;

if (¿Acción?) then (Subir Nuevo)
  :Seleccionar Archivo (Foto/Video);
  :Añadir Descripción/Etiquetas;
  :Confirmar Subida;
  :Sistema Procesa Archivo;
else (Gestionar Existente)
  :Seleccionar Elemento;
  if (¿Acción?) then (Eliminar)
    :Confirmar Eliminación;
  else (Editar)
    :Modificar Detalles;
  endif
endif

:Actualizar Galería;
stop
@enduml
```

## 4. Disponibilidad (Calendario)
**Vista:** `availability`
**Descripción:** Configuración de fechas libres u ocupadas.

```plantuml
@startuml
!theme plain
title Gestión de Disponibilidad - Armonihz

|Músico|
start
:Ver Calendario Mensual;

:Seleccionar Fecha/Rango;

if (¿Estado Actual?) then (Disponible)
  :Marcar como "Ocupado";
  :Añadir Nota (Opcional);
else (Ocupado)
  :Marcar como "Disponible";
endif

:Guardar Estado;
:Sistema Actualiza Calendario Público;

stop
@enduml
```

## 5. Castings (Oportunidades)
**Vista:** `castings.index`, `castings.show`
**Descripción:** El músico busca y aplica a ofertas.

```plantuml
@startuml
!theme plain
title Flujo de Castings - Armonihz

|Músico|
start
:Navegar a "Castings";
:Ver Lista de Oportunidades;

if (¿Interesado en uno?) then (Sí)
  :Ver Detalles del Casting;
  if (¿Cumple Requisitos?) then (Sí)
    :Clic en "Aplicar";
    :Enviar Perfil y Nota;
    :Confirmación de Solicitud;
  else (No)
    :Volver al Listado;
  endif
else (No)
  :Filtrar/Buscar otro;
endif

stop
@enduml
```

## 6. Chat (Mensajería)
**Vista:** `chat`
**Descripción:** Comunicación directa entre usuarios.

```plantuml
@startuml
!theme plain
title Sistema de Chat - Armonihz

|Usuario A|
start
:Abrir Chat;

if (¿Conversación Existente?) then (Sí)
  :Seleccionar Chat;
else (No)
  :Buscar Usuario;
  :Iniciar Nueva Conversación;
endif

:Escribir Mensaje;
:Enviar;

|Sistema|
:Notificar a Usuario B;
:Almacenar Mensaje;

|Usuario B|
:Recibir Mensaje;
:Leer y Responder (Opcional);

stop
@enduml
```

## 7. Panel de Administración
**Vista:** `admin.*`
**Descripción:** Funciones exclusivas del administrador.

```plantuml
@startuml
!theme plain
title Panel Administrativo - Armonihz

|Admin|
start
:Acceder a /admin;

fork
  :Validación de Músicos;
  if (¿Perfil Correcto?) then (Sí)
    :Aprobar Músico;
  else (No)
    :Rechazar/Solicitar Cambios;
  endif
fork again
  :Gestión de Eventos;
  :Moderar Contenido;
fork again
  :Configuración del Sistema;
  :Ajustar Parámetros Globales;
end fork

stop
@enduml
```
