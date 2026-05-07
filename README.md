# Floristeria Backend

## Instalación

Se debe instalar playwright para generar los PDFs

Cuidado al instalar nodejs, mirar la version
```
sudo apt update
sudo apt install nginx php-fpm php-sqlite3 php-mbstring php-xml php-curl php-zip unzip git nodejs npm composer

composer i --optimize-autoloader
npm i
npm install playwright
npx playwright install chromium

cp .env.example .env
php artisan key:generate
```

Database, Storage y bootstrap/cache deben estar disponibles para el usuario www-data 

## Despliegue con Nginx

El frontend compila en producción con `VITE_API_URL=/api`, así que Nginx debe reenviar `/api/*` al backend Laravel.

Hay una plantilla completa en [deploy/nginx.front-back.example.conf](/home/rober/Proyectos/tfg/floristeria/deploy/nginx.front-back.example.conf).

Comprobaciones utiles en el servidor:

```bash
php artisan optimize:clear
php artisan route:list --path=contratos
```

La segunda linea debe mostrar al menos:

- `GET|HEAD api/contratos/entregas`
- `GET|HEAD api/contratos/reservas`

Si no aparecen, el problema no esta en React: el servidor esta ejecutando codigo antiguo o tiene cache de rutas/configuracion sin limpiar.

## Si el listado funciona pero crear o editar no

El flujo de crear/editar usa mas endpoints que el listado:

- El listado usa `GET /api/entregas` o `GET /api/reservas`.
- El formulario usa `GET /api/contratos/entregas` o `GET /api/contratos/reservas`.
- Guardar usa `POST /api/entregas` o `POST /api/reservas`.
- Editar usa `PUT /api/entregas/{id}` o `PUT /api/reservas/{id}`.

En este repositorio, esas rutas existen y el backend responde bien a `OPTIONS`, `POST` y `PUT`, asi que si en otro ordenador falla normalmente es un problema de despliegue:

```bash
php artisan optimize:clear
php artisan route:list --path=api
```

Si faltan las rutas de `contratos` o las de `POST`/`PUT`, el backend desplegado no coincide con este codigo.

Si las rutas si existen pero guardar falla, revisa permisos de escritura del usuario de PHP-FPM o Nginx sobre:

- `database.sqlite`
- el directorio que contiene `database.sqlite`
- `storage/`
- `bootstrap/cache/`

Con SQLite es muy comun que `GET` funcione y `POST`/`PUT` fallen si la base es legible pero no escribible.

## Cambio en limite de desbordamiento en pdf
App\Http\Requests\BaseRequest


## Recuerda que para añadir un nuevo campo se debe tocar
migración -> model -> controller -> pedidoService (si añades a pedido) -> request -> test -> pdf -> factory
