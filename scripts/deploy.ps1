Param(
  [string]$Env = "production",
  [switch]$BuildAssets,
  [switch]$DryRun,
  [string]$AppUrl,
  [switch]$NoMigrate,
  [string]$DbConnection,
  [string]$DbHost,
  [string]$DbPort,
  [string]$DbDatabase,
  [string]$DbUsername,
  [string]$DbPassword
)

$ErrorActionPreference = "Stop"

function RunCmd([string]$cmd) {
  Write-Host ">> $cmd"
  if (-not $DryRun) {
    Invoke-Expression $cmd
    if ($LASTEXITCODE -ne $null -and $LASTEXITCODE -ne 0) { throw "Command failed: $cmd (exit $LASTEXITCODE)" }
  }
}

$root = (Resolve-Path (Join-Path $PSScriptRoot '..')).Path
Set-Location $root

if (-not (Test-Path "$root/composer.json")) { throw "composer.json not found" }
if (-not (Test-Path "$root/artisan")) { throw "artisan not found" }

if (-not (Test-Path "$root/.env") -and (Test-Path "$root/.env.example")) {
  Copy-Item -Force "$root/.env.example" "$root/.env"
}

$envPath = "$root/.env"
if (Test-Path $envPath) {
  $envText = Get-Content $envPath -Raw
  $envText = [Regex]::Replace($envText, "(?m)^APP_ENV=.*$", "APP_ENV=" + $Env)
  $envText = [Regex]::Replace($envText, "(?m)^APP_DEBUG=.*$", "APP_DEBUG=false")
  if ($AppUrl) { $envText = [Regex]::Replace($envText, "(?m)^APP_URL=.*$", "APP_URL=" + $AppUrl) }
  $envText = [Regex]::Replace($envText, "(?m)^CACHE_DRIVER=.*$", "CACHE_DRIVER=file")
  $envText = [Regex]::Replace($envText, "(?m)^SESSION_DRIVER=.*$", "SESSION_DRIVER=file")
  $envText = [Regex]::Replace($envText, "(?m)^QUEUE_CONNECTION=.*$", "QUEUE_CONNECTION=sync")
  if ($DbConnection) { $envText = [Regex]::Replace($envText, "(?m)^DB_CONNECTION=.*$", "DB_CONNECTION=" + $DbConnection) }
  if ($DbHost) { $envText = [Regex]::Replace($envText, "(?m)^DB_HOST=.*$", "DB_HOST=" + $DbHost) }
  if ($DbPort) { $envText = [Regex]::Replace($envText, "(?m)^DB_PORT=.*$", "DB_PORT=" + $DbPort) }
  if ($DbDatabase) { $envText = [Regex]::Replace($envText, "(?m)^DB_DATABASE=.*$", "DB_DATABASE=" + $DbDatabase) }
  if ($DbUsername) { $envText = [Regex]::Replace($envText, "(?m)^DB_USERNAME=.*$", "DB_USERNAME=" + $DbUsername) }
  if ($DbPassword) { $envText = [Regex]::Replace($envText, "(?m)^DB_PASSWORD=.*$", "DB_PASSWORD=" + $DbPassword) }
  Set-Content -Path $envPath -Value $envText -Encoding UTF8
}

$needKey = $false
if (Test-Path $envPath) {
  $lines = Get-Content $envPath
  $line = $lines | Where-Object { $_ -match '^APP_KEY=' }
  if ($line -and ($line -replace '^APP_KEY=', '') -eq '') { $needKey = $true }
  if (-not $line) { $needKey = $true }
}
if ($needKey) { RunCmd "php artisan key:generate" }

RunCmd "composer install --no-dev --prefer-dist --no-interaction --optimize-autoloader"

if ($BuildAssets) {
  RunCmd "npm install"
  RunCmd "npm run build"
}

RunCmd "php artisan storage:link"

if (-not $NoMigrate) { RunCmd "php artisan migrate --force" }

RunCmd "php artisan config:cache"
RunCmd "php artisan route:cache"
RunCmd "php artisan view:cache"

New-Item -ItemType Directory -Force -Path "$root/storage" | Out-Null
New-Item -ItemType Directory -Force -Path "$root/bootstrap/cache" | Out-Null

try { RunCmd "icacls storage /grant Users:(OI)(CI)(M) /T" } catch {}
try { RunCmd "icacls bootstrap\cache /grant Users:(OI)(CI)(M) /T" } catch {}

Write-Host "Deployment tasks completed."
