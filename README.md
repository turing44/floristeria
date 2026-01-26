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

## Cambio en limite de desbordamiento en pdf
App\Http\Requests\BaseRequest