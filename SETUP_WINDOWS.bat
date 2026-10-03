@echo off
setlocal
cd /d %~dp0
if not exist .env copy .env.example .env
if not exist database\database.sqlite type nul > database\database.sqlite
where php >nul 2>&1 || (echo PHP is not available in PATH.&pause&exit /b 1)
if not exist vendor\autoload.php (
  where composer >nul 2>&1 || (echo Composer is not available in PATH.&pause&exit /b 1)
  composer install
)
php artisan key:generate
php artisan migrate:fresh --seed
if exist package.json (
  where npm >nul 2>&1 && npm install && npm run build
)
echo.
echo Laraventry Pro is ready.
echo Login: admin@inventori.test
 echo Password: password
pause
