<#
 Instalador automático. Uso (desde la carpeta donde descomprimiste el zip):

   .\ejercicio-7-flota\scripts\instalar.ps1                 # SQLite (recomendado)
   .\ejercicio-7-flota\scripts\instalar.ps1 -Motor mysql    # MySQL (requiere servidor encendido)

 Crea el proyecto Laravel "ejercicio-7" junto a esta carpeta, copia los archivos,
 configura el .env, crea la base de datos y ejecuta migraciones + seeders.
#>
param(
    [string]$Nombre = "ejercicio-7",
    [ValidateSet("sqlite", "mysql")][string]$Motor = "sqlite",
    [string]$DbUser = "root",
    [string]$DbPass = ""
)

$ErrorActionPreference = "Stop"
$origen  = Split-Path $PSScriptRoot -Parent
$destino = Join-Path (Split-Path $origen -Parent) $Nombre

function Set-EnvValue($archivo, $clave, $valor) {
    $lineas = Get-Content $archivo
    $patron = "^\s*#?\s*$clave="
    if ($lineas -match $patron) {
        $lineas = $lineas | ForEach-Object { if ($_ -match $patron) { "$clave=$valor" } else { $_ } }
    } else { $lineas += "$clave=$valor" }
    Set-Content $archivo $lineas
}

# 1. Proyecto Laravel base
if (-not (Test-Path $destino)) {
    Write-Host ">> Creando proyecto Laravel en $destino ..." -ForegroundColor Cyan
    composer create-project laravel/laravel $destino
}

# 2. Copiar archivos del caso de estudio
Write-Host ">> Copiando migraciones, modelos, factories, seeders..." -ForegroundColor Cyan
foreach ($c in "app", "database", "scripts") {
    New-Item -ItemType Directory -Force -Path (Join-Path $destino $c) | Out-Null
    Copy-Item "$origen\$c\*" (Join-Path $destino $c) -Recurse -Force
}
Copy-Item "$origen\*.md" $destino -Force
Copy-Item "$origen\.env.*.example" $destino -Force

Set-Location $destino
if (-not (Test-Path ".env")) { Copy-Item ".env.example" ".env"; php artisan key:generate }

# 3. Configurar base de datos
if ($Motor -eq "sqlite") {
    Set-EnvValue ".env" "DB_CONNECTION" "sqlite"
    if (-not (Test-Path "database\database.sqlite")) { New-Item "database\database.sqlite" -ItemType File | Out-Null }
    Write-Host ">> Base de datos: SQLite (database\database.sqlite)" -ForegroundColor Green
} else {
    Write-Host ">> Creando base de datos MySQL flota_vehicular ..." -ForegroundColor Cyan
    $pass = if ($DbPass) { "-p$DbPass" } else { "" }
    & mysql -u $DbUser $pass -e "CREATE DATABASE IF NOT EXISTS flota_vehicular CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;"
    Set-EnvValue ".env" "DB_CONNECTION" "mysql"
    Set-EnvValue ".env" "DB_HOST" "127.0.0.1"
    Set-EnvValue ".env" "DB_PORT" "3306"
    Set-EnvValue ".env" "DB_DATABASE" "flota_vehicular"
    Set-EnvValue ".env" "DB_USERNAME" $DbUser
    Set-EnvValue ".env" "DB_PASSWORD" $DbPass
}

# 4. Migraciones + seeders
php artisan config:clear
php artisan migrate:fresh --seed --force
php artisan flota:alertas

Write-Host "`nListo. Entra a la carpeta y abre Tinker:" -ForegroundColor Green
Write-Host "  cd $destino"
Write-Host "  php artisan tinker"
