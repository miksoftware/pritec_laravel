# Memoria del Proyecto - Pritec

## 1. Stack Tecnológico
- **Backend:** PHP 8.2+, Laravel 12.0 (Framework MVC, Artisan, Eloquent ORM).
- **Base de Datos:** MySQL / MariaDB (con llaves foráneas en cascada y campos estructurados en JSON).
- **Frontend:** Laravel Blade, Bootstrap 5.3, SweetAlert2, FontAwesome 6, jQuery 3.7, Tailwind CSS / Vite.
- **Procesamiento de Imágenes:** PHP GD para redimensionamiento y gestión de coordenadas de planos vehiculares.

## 2. Arquitectura y Módulos del Sistema
- **Autenticación y Usuarios (`UserController`, `LoginController`, `RegisterController`):**
  - Autenticación con sesión tradicional y protección de rutas con middleware `auth` y `guest`.
  - Roles de usuario (`is_admin`, normal) y estados (`active`, `inactive`).
  - Auditoría de accesos vía `user_login_logs` (IP, User-Agent, fecha/hora).
- **Instalador Web Inicial (`InstallController`, middleware `CheckInstalled`):**
  - Detección del archivo centinela `storage/installed`.
  - Configuración paso a paso de base de datos MySQL con validación AJAX previa, reescritura de `.env`, generación de claves y ejecución de migraciones.
- **Dashboard (`DashboardController`):**
  - Resumen ejecutivo con KPIs: total de peritajes (completados, en progreso), clientes, tipos de vehículo, citas y actividad reciente.
- **Módulo de Peritajes / Inspección Técnica (`ExpertiseController`):**
  - Wizard guiado en 12 pasos (11 para motocicletas al omitir carrocería):
    - *Paso 1:* Datos de servicio, cliente y generación de código único (`PRT-YYYYMMDD-XXXXXX`).
    - *Paso 2:* Identificación vehicular (placa, marca, línea, modelo, motor, chasis, VIN, fasecolda, etc.).
    - *Pasos 3, 4 y 5:* Inspección gráfica de Carrocería, Estructura y Chasis asociando piezas a conceptos de daño (`inspection_concepts`).
    - *Paso 6:* Diagnóstico y labrado de llantas (medición milimétrica en 4 ruedas).
    - *Paso 7:* Inspección de amortiguadores y porcentajes de vida útil.
    - *Paso 8:* Pruebas de sistema eléctrico, arranque y batería.
    - *Paso 9:* Motor, tren motriz e interiores (almacenamiento en JSON).
    - *Paso 10:* Fugas, niveles de fluidos y prueba de ruta (almacenamiento en JSON).
    - *Paso 11:* Registro y galería fotográfica de evidencias.
    - *Paso 12:* Resumen final, validación y cierre de peritaje.
  - Generación de informe imprimible / PDF con membrete corporativo, planos con ubicación de piezas y conceptos técnicos.
- **Tipos de Vehículos y Mapeo de Piezas (`VehicleTypeController`, `VehicleSectionController`):**
  - Definición de tipos de vehículos (carros, motos, etc.).
  - Configuración de secciones (Carrocería, Estructura, Chasis) con planos gráficos y editor interactivo para posicionar piezas mediante coordenadas relativas (`position_x`, `position_y`).
- **Gestión de Clientes (`ClientController`):**
  - Directorio de clientes con búsqueda AJAX en tiempo real, exportación de datos, creación inline desde modales de citas y peritajes.
- **Gestión de Citas (`AppointmentController`):**
  - Agendamiento de citas con filtro por próximas, hoy, completadas o canceladas.
- **Configuración del Sistema (`SettingsController`):**
  - Datos de contacto y membrete de la empresa (razón social, teléfonos, WhatsApp predeterminado, dirección, web).
- **Herramienta de Migración (`MigrationController`):**
  - Importación y conversión masiva de datos desde archivos SQL heredados para clientes, tipos de vehículos, secciones y piezas.

## 3. Modelos y Base de Datos
- `User`: Administradores y peritos técnicos.
- `UserLoginLog`: Historial de accesos al sistema.
- `Client`: Datos de clientes propietarios o solicitantes.
- `VehicleType`: Tipos de vehículos soportados.
- `VehicleSection`: Secciones del vehículo asociadas a planos gráficos (400x400 px).
- `VehiclePiece`: Piezas vehiculares con coordenadas interactivas relativas en el plano.
- `InspectionConcept`: Catálogo de estados y fallas técnicas (Bueno, Sumido, Deformidad, etc.).
- `Expertise`: Registro principal del peritaje vehicular con estados (`draft`, `in_progress`, `completed`).
- `ExpertiseInspection`: Relación entre peritaje, sección, pieza y concepto de daño detectado.
- `ExpertisePhoto`: Evidencias fotográficas asociadas al peritaje.
- `Appointment`: Citas agendadas para servicios de peritaje.
- `SystemSetting`: Parámetros configurables globales de la aplicación y empresa.

## 4. Reglas de Negocio Clave
- Si un peritaje corresponde a tipo motocicleta, se salta automáticamente el paso de carrocería (paso 3).
- Un peritaje no puede marcarse como completado si no ha llegado al paso final (paso 11 o superior).
- El código de peritaje es autogenerado con el formato `PRT-YYYYMMDD-XXXXXX`.
- La eliminación de peritajes purga en cascada sus registros de inspección y los archivos físicos de fotografías en el servidor.
- La migración de datos heredados requiere permisos de administrador (`admin@pritec.com`).
- En Carrocería y Estructura, la calificación porcentual se calcula internamente por concepto técnico (escala: Bueno 90-100%, Aceptable 60-89%, Malo 0-59%). En Estructura, cualquier pieza con Fisurado (F), Deformidad Fuerte (DF) o Mala Reparación (MR) clasifica automáticamente la sección como MALO.

## Changelog
- 2026-11-21: Análisis general de la arquitectura, módulos y modelos del sistema; creación de la memoria técnica docs/MEMORY.md.
- 2026-11-21: Modernización de los pasos del peritaje (1-12) con modern-web-guidance (replaceChildren, SRI en CDN, accesibilidad ARIA, inputs optimizados y prevención de doble submit).
- 2026-11-21: Optimización del backend en peritajes y ajustes (transacciones atómicas DB, agregaciones SQL consolidadas, carga masiva de configuraciones/piezas, validaciones estrictas y corrección de búsqueda de clientes por identificación).
- 2026-11-21: Optimización y blindaje de deploy.sh (control de errores pipefail, orden anti-502, permisos en uploads, storage:link, purga de OPcache y autodetección de contenedor).
- 2026-11-21: Incorporación de cálculo de promedios e indicador visual en las barras de convenciones de llantas, amortiguadores y batería (PDF y vista de resumen).
- 2026-11-22: Mapeo interno de porcentajes en Carrocería y Estructura según escala técnica oficial; adición de barra indicadora estilo Llantas y desglose de piezas en PDF y resumen web.
