# TPV Estética y SPA

Software de gestión TPV para centros de estética, salones de belleza y SPA, construido con **Laravel 11**, **MySQL** y **Bootstrap 5**.

## Requisitos

- PHP >= 8.2 con extensiones: `pdo_mysql`, `mbstring`, `openssl`, `tokenizer`, `xml`, `ctype`, `json`, `bcmath`, `fileinfo`, `gd`
- Composer 2.x
- MySQL 8.x (o MariaDB 10.4+)
- Servidor web (Apache/Nginx) o `php artisan serve` para desarrollo

## Configuración de la base de datos

Crea la base de datos `tvp_estetica_spa` antes de instalar:

```sql
CREATE DATABASE tvp_estetica_spa CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
```

Configuración por defecto (en `.env`):

```
DB_HOST=localhost
DB_PORT=3306
DB_DATABASE=tvp_estetica_spa
DB_USERNAME=root
DB_PASSWORD=
```

## Instalación paso a paso

```bash
# 1. Instalar dependencias
composer install

# 2. Copiar el archivo de variables de entorno
cp .env.example .env

# 3. Generar la APP_KEY
php artisan key:generate

# 4. Ejecutar migraciones y seeders
php artisan migrate --seed

# 5. Crear el enlace simbólico para storage (logo de empresa)
php artisan storage:link

# 6. Levantar el servidor de desarrollo
php artisan serve
```

Abre el navegador en: **http://localhost:8000**

## Credenciales de acceso (seed)

| Rol            | Email                          | Contraseña       |
| -------------- | ------------------------------ | ---------------- |
| Administrador  | admin@estetica.local           | admin123         |
| Recepcionista  | recepcion@estetica.local       | recepcion123     |
| Profesional    | maria@estetica.local           | profesional123   |

> **Cambia estas contraseñas** en producción.

## Funcionalidades implementadas (Fase 1)

- Sistema de autenticación con roles (admin, recepcionista, profesional, cajero)
- Middleware `role` para proteger rutas según el rol del usuario
- Dashboard principal con saludo dinámico, tarjetas de estadísticas, accesos rápidos y panel de bienvenida
- Módulo de Configuración con pestañas:
  - **Empresa**: nombre, razón social, NIT/RFC, contacto, dirección
  - **Marca y Logo**: subida de logo, colores primario y secundario
  - **Moneda e Impuestos**: símbolo, código ISO, formato, IVA configurable
  - **Horario**: zona horaria, apertura/cierre, días laborales, intervalo de citas
  - **Otros**: mensaje del recibo, términos y condiciones
- Layout responsive (escritorio, tablet, móvil) con sidebar colapsable
- Tema visual de Estética/SPA con paleta rosa polvo / lavanda / dorado
- Páginas de error 403 y 404 personalizadas

## Roadmap (próximas fases)

- [ ] Módulo de **Clientes** (ficha, historial, fotos antes/después)
- [ ] Módulo de **Empleados** (horarios, comisiones, especialidades)
- [ ] Módulo de **Tratamientos** y **Servicios** (catálogo con duración y precio)
- [ ] Módulo de **Cabinas** y disponibilidad
- [ ] Módulo de **Agenda** con vista calendario (día/semana/mes) y drag & drop
- [ ] Módulo de **Citas** con confirmación por SMS/WhatsApp
- [ ] Módulo de **Bonos** (paquetes prepagados con consumos)
- [ ] **Punto de Venta (TPV)** con apertura/cierre de caja
- [ ] Módulo de **Productos y Stock** con alertas de mínimos
- [ ] **Reportes** (ventas por profesional, por servicio, ranking de clientes)
- [ ] **Comunicación** con clientes (recordatorios automáticos)

## Estructura del proyecto

```
tpv-EsteticaySPA/
├── app/
│   ├── Http/
│   │   ├── Controllers/    # Auth, Dashboard, Configuracion
│   │   └── Middleware/     # CheckRole
│   ├── Models/             # User, ConfiguracionEmpresa
│   └── Providers/
├── bootstrap/
├── config/                 # app, auth, database, session, etc.
├── database/
│   ├── migrations/         # users, sessions, configuracion_empresa, cache
│   └── seeders/            # ConfiguracionEmpresaSeeder, UsuariosSeeder
├── public/
│   ├── css/app.css         # Estilos personalizados (tema SPA)
│   ├── js/app.js           # JS (sidebar, tabs, preview de logo)
│   └── index.php
├── resources/
│   └── views/
│       ├── auth/login.blade.php
│       ├── configuracion/edit.blade.php
│       ├── dashboard/index.blade.php
│       ├── errors/{403,404}.blade.php
│       └── layouts/        # app + partials (sidebar, topbar)
├── routes/
│   ├── web.php
│   └── console.php
├── storage/
└── README.md
```

## Personalización

Los colores de la interfaz se generan dinámicamente desde el módulo de Configuración: al cambiar `color_primario` y `color_secundario`, el sistema completo se reestiliza automáticamente (vía variables CSS inyectadas en el layout).

## Soporte

Proyecto desarrollado como TPV específico para centros de estética, belleza y SPA.
Pull requests y sugerencias bienvenidas.
