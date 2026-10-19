# Ejecutar desde la raíz del proyecto (ejercicio-7) para generar un historial incremental.
git init
git add .gitignore composer.json composer.lock artisan 2>$null
git commit -m "chore: proyecto Laravel base"

git add database/migrations
git commit -m "feat(db): migraciones de choferes, talleres, repuestos, vehiculos, mantenimientos, pivote y viajes"

git add app/Models
git commit -m "feat(models): modelos Eloquent con relaciones y reglas de negocio"

git add database/factories
git commit -m "feat(factories): factories con datos realistas para todos los modelos"

git add database/seeders
git commit -m "feat(seeders): FlotaSeeder con flota de 10 buses, mantenimientos y viajes"

git add app/Console
git commit -m "feat(console): comando flota:alertas (alerta de 10.000 km)"

git add GUIA_TINKER.md
git commit -m "docs: guia con las 10 consultas de Tinker"

git add .
git commit -m "docs: README, instalacion y scripts"
