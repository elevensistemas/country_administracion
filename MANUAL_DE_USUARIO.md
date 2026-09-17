# Manual Oficial de Usuario y Administración
## Sistema de Gestión Barrio Cerrado La Ranita Country Club

---

## 📑 Tabla de Contenidos

1. [Introducción y Arquitectura](#1-introducción-y-arquitectura)
   - 1.1 Roles y Permisos en el Sistema
   - 1.2 Flujo de Acceso, Seguridad y Recuperación de Claves
2. [Manual del Propietario / Residente (Mi Portal)](#2-manual-del-propietario--residente-mi-portal)
   - 2.1 Inicio y Resumen de Saldo (`/owner`)
   - 2.2 Mis Expensas y Descarga de PDF (`/owner/expenses`)
   - 2.3 Mi Cuenta Corriente (`/owner/accounting`)
   - 2.4 Informar Pago de Expensas (`/owner/payments/report`)
   - 2.5 Mis Reclamos / Mesa de Ayuda (`/owner/tickets`)
   - 2.6 Espacios Comunes y Reservas (`/owner/reservations`)
   - 2.7 Autorización de Visitas y Pase QR (`/owner/guests`)
   - 2.8 Novedades y Documentos (`/owner/news`, `/owner/documents`)
   - 2.9 Mi Propiedad y Perfil (`/owner/property`, `/owner/profile`)
3. [Manual de Administración y Operaciones](#3-manual-de-administración-y-operaciones)
   - 3.1 Tablero Principal / Dashboard (`/admin`)
   - 3.2 Catastro de Lotes y Unidades Funcionales (`/admin/lots`, `/admin/functional-units`)
   - 3.3 Historial y Seguimientos de Lote (`/admin/history`, `/admin/follow-ups`)
   - 3.4 Padrón de Propietarios y Usuarios (`/admin/owners`, `/admin/users`)
   - 3.5 Facturación y Liquidación de Expensas (`/admin/expenses`)
   - 3.6 Cuentas Corrientes y Ajustes Manuales (`/admin/accounting`)
   - 3.7 Conciliación y Auto-Conciliación de Pagos (`/admin/payments`)
   - 3.8 Proveedores y Facturas de Compra (`/admin/suppliers`, `/admin/supplier-invoices`)
   - 3.9 Mesa de Ayuda / Gestión de Tickets (`/admin/tickets`)
   - 3.10 Administración de Espacios Comunes y Reservas (`/admin/common-areas`, `/admin/reservations`)
   - 3.11 Novedades y Comunicados Masivos (`/admin/news`, `/admin/comms`)
   - 3.12 Repositorio de Documentos (`/admin/documents`)
   - 3.13 Importador Masivo de Datos (`/admin/imports`)
   - 3.14 Módulo de Auditoría y Control de Accesos (`/admin/audit`)
   - 3.15 Reportes y Adopción (`/admin/reports`, `/admin/adoption`)
   - 3.16 Parámetros Globales, SMTP y WhatsApp (`/admin/settings`)

---

## 1. Introducción y Arquitectura

La plataforma web de **La Ranita Administración** centraliza la gestión operativa, contable y administrativa del barrio cerrado.

### 1.1 Roles y Permisos en el Sistema

- **`superadmin` / `admin` (Administrador General):** Acceso integral a todas las funciones financieras, contables, catastrales, configuración de sistema, auditoría y control de usuarios. Destino de inicio: `/admin/dashboard`.
- **`accounting` (Contable):** Acceso a facturación de expensas, movimientos de cuenta corriente, conciliación de cobranzas, proveedores, facturas de compra y reportes financieros. Destino: `/admin/dashboard`.
- **`operator` (Operador / Mantenimiento):** Gestión de tickets/reclamos, aprobación de reservas de espacios comunes, publicación de novedades y catastro. Destino: `/admin/dashboard`.
- **`owner` (Propietario) / `tenant` (Inquilino):** Acceso al portal personal para consultar y descargar expensas en PDF, informar pagos con comprobante, ver el saldo de su cuenta corriente, crear reclamos, reservar amenidades, precargar autorizaciones de visitas y descargar reglamentos. Destino: `/owner/dashboard`.

### 1.2 Flujo de Acceso, Seguridad y Recuperación de Claves

1. **Inicio de Sesión (`/login`):** Se realiza con correo electrónico y contraseña encriptada (BCrypt). El sistema redirige automáticamente al usuario según su rol asignado.
2. **Protección contra Fuerza Bruta (Rate Limiting):** Tras 5 intentos fallidos consecutivos de contraseña, el sistema bloquea temporalmente el acceso por 60 segundos y registra el evento en la bitácora de seguridad.
3. **Primer Acceso (`/password/force-change`):** Los usuarios dados de alta por invitación son redirigidos obligatoriamente a definir su clave definitiva y aceptar los términos de uso.
4. **Recuperación de Clave (`/forgot-password`):** Envía un enlace seguro con token de restablecimiento por correo electrónico.
5. **Tema Visual:** Permite seleccionar Modo Claro, Modo Oscuro o Automático desde el menú lateral o inferior.

---

## 2. Manual del Propietario / Residente (Mi Portal)

### 2.1 Inicio y Resumen de Saldo (`/owner`)
- **Estado de Cuenta:** Indica el saldo consolidado del lote. Si registra deuda se destaca en rojo junto a la fecha de vencimiento. Si no registra deuda se indica como *Saldo a Favor* o *¡Tu cuenta está al día!*.
- **Accesos Rápidos:** Botones directos a Expensas, Invitados, Reservas y Reclamos.
- **Novedades Recientes:** Tarjetas con las últimas noticias y comunicados del country.

### 2.2 Mis Expensas y Descarga de PDF (`/owner/expenses`)
- **Listado Histórico:** Muestra las liquidaciones emitidas por período mensual (ej. *Octubre 2026*), con montos al 1° y 2° vencimiento y estado de pago (`Pagada`, `Pendiente`, `Vencida`).
- **Descarga de PDF:** Botón individual para descargar la liquidación oficial de expensas en PDF.

### 2.3 Mi Cuenta Corriente (`/owner/accounting`)
- **Libro Mayor:** Muestra cada movimiento de la unidad funcional.
- **Débitos (+):** Cargos de expensas emitidas, intereses por mora o ajustes.
- **Créditos (-):** Pagos bancarios recibidos y conciliados.
- **Saldo Evolutivo:** Visualización del saldo tras cada transacción.

### 2.4 Informar Pago de Expensas (`/owner/payments/report`)
Permite al residente declarar un pago efectuado por transferencia o depósito:
1. **Importe Transferido ($):** Monto exacto abonado.
2. **Fecha del Pago:** Día de realización de la transacción.
3. **Medio de Pago:** Transferencia Bancaria, Depósito Bancario u Otro Medio.
4. **Banco de Destino:** Cuenta receptora del consorcio.
5. **N° de Comprobante / Referencia:** Código de transacción bancaria.
6. **Adjunto:** Foto o archivo PDF del comprobante (hasta 5MB).
7. **Observaciones:** Notas aclaratorias opcionales.
- *Efecto:* El pago queda registrado en estado `Pendiente`. Al ser aprobado por Administración, el saldo del lote se actualiza automáticamente y el estado cambia a `Conciliado`.

### 2.5 Mis Reclamos / Mesa de Ayuda (`/owner/tickets`)
- **Nuevo Reclamo (`/owner/tickets/create`):** Selección de Lote, Categoría (Mantenimiento, Seguridad, Administración), Asunto, Prioridad (Baja, Media, Alta), Detalle descriptivo y archivo/foto adjunta.
- **Hilo de Mensajes:** Permite interactuar con el equipo de administración y recibir actualizaciones de estado (`Abierto`, `En Progreso`, `Resuelto`, `Cerrado`).

### 2.6 Espacios Comunes y Reservas (`/owner/reservations`)
- **Catálogo de Espacios:** Muestra las instalaciones habilitadas por la administración con su capacidad, arancel (o sin costo), duración por turno y reglamento.
- **Formulario de Reserva:** Selección de fecha, franja horaria, observaciones y aceptación de normas de uso.
- **Cancelación:** El vecino puede cancelar sus reservas pendientes o futuras desde su panel.

### 2.7 Autorización de Visitas y Pase QR (`/owner/guests`)
- **Tipos de Autorización:**
  - `Individual`: Para una persona puntual (Nombre, Apellido, DNI, Patente y Fecha de visita).
  - `Frecuente`: Para personal o visitas recurrentes.
  - `Lista`: Para eventos o reuniones sociales con nómina de asistentes en el campo de observaciones.
- **Pase Digital:** Genera un comprobante con código QR y datos del ingreso que el residente puede compartir con su invitado.
- **Baja:** Permite revocar o eliminar la autorización en cualquier momento.
> [!NOTE]
> Las autorizaciones se almacenan y consultan en la base de datos de administración web. La integración automatizada con el hardware y lectores de la garita de seguridad física se encuentra en desarrollo posterior.

### 2.8 Novedades y Documentos (`/owner/news`, `/owner/documents`)
- **Novedades:** Lectura de comunicados institucionales y novedades con fotos adjuntas.
- **Documentos:** Descarga de reglamentos, actas de asamblea y estatutos clasificados por categoría.

### 2.9 Mi Propiedad y Perfil (`/owner/property`, `/owner/profile`)
- **Mi Propiedad:** Consulta de residentes y vehículos empadronados para su lote, con botón para solicitar cambios a la administración.
- **Mi Perfil:** Actualización de teléfono, correo alternativo, configuración de canales de notificación (Email / WhatsApp) y cambio de contraseña.

---

## 3. Manual de Administración y Operaciones

### 3.1 Tablero Principal / Dashboard (`/admin`)
- Resumen ejecutivo con métricas de recaudación mensual, deuda global, lotes activos, propietarios y reclamos abiertos.

### 3.2 Catastro de Lotes y Unidades Funcionales (`/admin/lots`, `/admin/functional-units`)
- **Lotes:** Alta y edición de número de lote, código identificador, dirección interna, estado (Baldío, En Construcción, Habitado), propietario titular y saldo.
- **Unidades Funcionales:** Registro de UFs vinculadas a cada lote con su coeficiente de participación para el prorrateo de gastos.

### 3.3 Historial y Seguimientos de Lote (`/admin/history`, `/admin/follow-ups`)
- **Historial General:** Registro de acontecimientos clasificados por lote, categoría y fecha.
- **Seguimientos:** Tareas operativas pendientes asociadas a un lote con estados de cumplimiento.

### 3.4 Padrón de Propietarios y Usuarios (`/admin/owners`, `/admin/users`)
- **Propietarios:** Registro de datos personales y de contacto (DNI, CUIT, Razón Social, teléfonos, correos y canal preferido).
- **Usuarios:** Creación de cuentas, asignación de roles (`admin`, `operator`, `accounting`, `owner`), activación/desactivación y reseteo de claves.

### 3.5 Facturación y Liquidación de Expensas (`/admin/expenses`)
1. **Crear Período (`/admin/expenses/create-period`):** Mes/Año, 1° y 2° vencimiento, porcentaje de recargo e intereses.
2. **Generación de Expensas:** Cálculo masivo aplicando los coeficientes de las unidades funcionales sobre los gastos cargados, o mediante **Importación desde planilla Excel/CSV**.
3. **Publicar y Descargar:** Publicación masiva para impactar en las cuentas corrientes y habilitar la descarga del PDF a los residentes.

### 3.6 Cuentas Corrientes y Ajustes Manuales (`/admin/accounting`)
- Visualización de saldos de todas las unidades funcionales.
- Carga de **Ajustes Contables Manuales** (Débitos por cargos extraordinarios / Créditos por bonificaciones) con motivo documentado.

### 3.7 Conciliación y Auto-Conciliación de Pagos (`/admin/payments`)
- **Indicadores en tiempo real:** Pagos pendientes, conciliados del día, en revisión y sin identificar.
- **Auto-Conciliación:** Algoritmo que cruza automáticamente pagos pendientes con expensas adeudadas por coincidencia de monto y fecha.
- **Conciliación Manual:** Visualización del comprobante bancario, N° de operación y asignación a deuda.
- **Acciones:** Conciliar / Aprobar, Marcar en Revisión, Rechazar (con motivo) y **Revertir Pago** (genera contra-asiento contable automático).

### 3.8 Proveedores y Facturas de Compra (`/admin/suppliers`, `/admin/supplier-invoices`)
- **Proveedores:** CUIT, razón social, rubro, CBU/Alias bancario y contacto.
- **Facturas:** Registro de comprobantes a pagar con importe, fecha de emisión, vencimiento y PDF adjunto.
- **Flujo de Fondos (`/admin/supplier-invoices/print`):** Planilla para planificación de egresos y tesorería.

### 3.9 Mesa de Ayuda / Gestión de Tickets (`/admin/tickets`)
- Tablero de reclamos con filtros por estado, prioridad y categoría.
- Asignación de responsable, respuestas públicas al residente y **Notas Internas Privadas** (visibles únicamente para la administración).

### 3.10 Administración de Espacios Comunes y Reservas (`/admin/common-areas`, `/admin/reservations`)
- **Espacios Comunes:** Parametrización de instalaciones (capacidad, arancel, horarios, duración de turnos, reglas y fotos).
- **Reservas:** Aprobación o rechazo de solicitudes de uso efectuadas por los vecinos.

### 3.11 Novedades y Comunicados Masivos (`/admin/news`, `/admin/comms`)
- **Novedades:** Publicación de noticias y avisos con imágenes y adjuntos.
- **Comunicaciones Masivas:** Despacho de comunicados dirigidos a toda la comunidad o segmentados por lote, con soporte multicanal (Email y WhatsApp).

### 3.12 Repositorio de Documentos (`/admin/documents`)
- Subida de archivos con control de versiones (v1, v2), clasificación por categorías y visibilidad pública o privada.

### 3.13 Importador Masivo de Datos (`/admin/imports`)
- Carga de planillas CSV/Excel para Lotes, Propietarios y Expensas.
- **Validación previa:** Detección de errores fila por fila antes de impactar en la base de datos.

### 3.14 Módulo de Auditoría y Control de Accesos (`/admin/audit`)
- **Auditoría de Cambios y Cargas:** Registro automático de quién creó, modificó o eliminó cualquier registro, con IP, fecha y **Modal Comparativo (Diff)** de valores anteriores vs nuevos.
- **Registro de Inicios de Sesión:** Auditoría de accesos exitosos, fallidos y bloqueados con IP, navegador y sistema operativo.

### 3.15 Reportes y Adopción (`/admin/reports`, `/admin/adoption`)
- Reportes de cobranzas y morosidad con exportación a Excel/CSV.
- Métricas de adopción digital y campañas de invitación a residentes.

### 3.16 Parámetros Globales, SMTP y WhatsApp (`/admin/settings`)
- Configuración institucional, tasas de mora, servidor SMTP para correos y API de WhatsApp con botones para prueba de conexión.
