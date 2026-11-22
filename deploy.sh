#!/bin/bash
# ============================================
# Script de Deploy Automático para Laravel
# ============================================

set -o pipefail

PROJECT_NAME="pritec"

GREEN='\033[0;32m'
RED='\033[0;31m'
YELLOW='\033[1;33m'
BLUE='\033[0;34m'
NC='\033[0m'

START_TIME=$(date +%s)
SCRIPT_DIR=$(cd "$(dirname "${BASH_SOURCE[0]}")" && pwd)
PROJECT_DIR="$SCRIPT_DIR"

# Detección flexible del directorio del código (soporta raíz o subdirectorio src/)
if [ -d "$PROJECT_DIR/src" ]; then
    SRC_DIR="$PROJECT_DIR/src"
else
    SRC_DIR="$PROJECT_DIR"
fi

CONTAINER="${PROJECT_NAME}_php"

# Detección inteligente del nombre del contenedor PHP si no coincide exactamente
if ! docker ps --format '{{.Names}}' 2>/dev/null | grep -q "^${CONTAINER}\$"; then
    DETECTED_CONTAINER=$(docker ps --format '{{.Names}}' 2>/dev/null | grep -E "${PROJECT_NAME}[-_]php" | head -n 1)
    if [ -n "$DETECTED_CONTAINER" ]; then
        CONTAINER="$DETECTED_CONTAINER"
    fi
fi

# Trap de recuperación: si ocurre cualquier error no controlado, se reactiva la aplicación
cleanup() {
    EXIT_CODE=$?
    if [ $EXIT_CODE -ne 0 ]; then
        echo -e "\n${RED}❌ Error durante el deploy (Código de salida: $EXIT_CODE).${NC}"
        echo -e "${YELLOW}Reactivando la aplicación para evitar que quede en mantenimiento...${NC}"
        docker exec -w /var/www/html "$CONTAINER" php artisan up 2>/dev/null || true
    fi
}
trap cleanup EXIT

echo -e "${BLUE}=========================================="
echo "  🚀 Deploy Laravel: $PROJECT_NAME"
echo "==========================================${NC}"
echo ""

# Verificación de que Docker y el contenedor estén activos
if ! docker ps --format '{{.Names}}' 2>/dev/null | grep -q "^${CONTAINER}\$"; then
    echo -e "${RED}✗ Error: El contenedor '$CONTAINER' no está en ejecución.${NC}"
    echo -e "${YELLOW}Contenedores activos:${NC}"
    docker ps --format 'table {{.Names}}\t{{.Status}}' 2>/dev/null || true
    exit 1
fi

# [0/9] Mantenimiento
echo -e "${YELLOW}[0/9] 🚧 Activando modo mantenimiento...${NC}"
docker exec -w /var/www/html "$CONTAINER" php artisan down --retry=60 --refresh=5 2>/dev/null || true

cd "$SRC_DIR" || { echo -e "${RED}✗ Error: No se puede acceder a $SRC_DIR${NC}"; exit 1; }

# [1/9] Git pull
echo ""
echo -e "${YELLOW}[1/9] ⬇️  Descargando cambios desde Git...${NC}"
git config core.autocrlf false 2>/dev/null || true
git stash --quiet 2>/dev/null || true

BRANCH=$(git rev-parse --abbrev-ref HEAD 2>/dev/null || echo "main")
if git pull origin "$BRANCH"; then
    echo -e "${GREEN}✓ Cambios descargados desde rama '$BRANCH'${NC}"
else
    echo -e "${RED}✗ Error al descargar cambios desde Git${NC}"
    exit 1
fi

LAST_COMMIT=$(git log -1 --pretty=format:'%h - %s (%ar) por %an' 2>/dev/null || echo "N/A")
echo -e "${BLUE}    📝 Último commit: $LAST_COMMIT${NC}"

# [2/9] Composer
echo ""
echo -e "${YELLOW}[2/9] 📦 Actualizando dependencias de Composer...${NC}"
if docker exec -w /var/www/html "$CONTAINER" composer install --no-dev --optimize-autoloader --no-interaction --prefer-dist; then
    echo -e "${GREEN}✓ Dependencias actualizadas${NC}"
else
    echo -e "${RED}✗ Error durante composer install${NC}"
    exit 1
fi

# [3/9] Migraciones y Seeders
echo ""
echo -e "${YELLOW}[3/9] 🗄️  Ejecutando migraciones de base de datos...${NC}"
if docker exec -w /var/www/html "$CONTAINER" php artisan migrate --force; then
    echo -e "${GREEN}✓ Migraciones ejecutadas exitosamente${NC}"
else
    echo -e "${RED}✗ Error en las migraciones de base de datos${NC}"
    exit 1
fi

# Ejecutar semillas pendientes sólo si el comando existe
if docker exec -w /var/www/html "$CONTAINER" php artisan list 2>/dev/null | grep -q "db:seed-pending"; then
    docker exec -w /var/www/html "$CONTAINER" php artisan db:seed-pending --force
    echo -e "${GREEN}✓ Semillas pendientes ejecutadas${NC}"
fi

# [4/9] Compilar assets frontend
echo ""
echo -e "${YELLOW}[4/9] 📦 Compilando assets frontend (Vite)...${NC}"
if [ -f "$SRC_DIR/package.json" ]; then
    if docker exec -w /var/www/html "$CONTAINER" which npm >/dev/null 2>&1; then
        docker exec -w /var/www/html "$CONTAINER" npm ci --prefer-offline 2>/dev/null || docker exec -w /var/www/html "$CONTAINER" npm install --no-audit
        docker exec -w /var/www/html "$CONTAINER" npm run build
        echo -e "${GREEN}✓ Assets compilados (en contenedor)${NC}"
    elif command -v npm >/dev/null 2>&1; then
        npm ci --prefer-offline 2>/dev/null || npm install --no-audit
        npm run build
        echo -e "${GREEN}✓ Assets compilados (en host)${NC}"
    else
        echo -e "${BLUE}⏭️  npm no disponible; se asumen assets precompilados${NC}"
    fi
else
    echo -e "${BLUE}⏭️  Sin package.json, saltando${NC}"
fi

# [5/9] Permisos y Almacenamiento
echo ""
echo -e "${YELLOW}[5/9] 🔐 Ajustando permisos y almacenamiento...${NC}"
docker exec -w /var/www/html "$CONTAINER" php artisan storage:link 2>/dev/null || true
docker exec "$CONTAINER" mkdir -p /var/www/html/public/uploads /var/www/html/storage /var/www/html/bootstrap/cache
docker exec "$CONTAINER" chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache /var/www/html/public/uploads
docker exec "$CONTAINER" chmod -R 775 /var/www/html/storage /var/www/html/bootstrap/cache /var/www/html/public/uploads
docker exec "$CONTAINER" chmod 664 /var/www/html/.env 2>/dev/null || true
echo -e "${GREEN}✓ Permisos y almacenamiento ajustados${NC}"

# [6/9] Publicar assets de Laravel y paquetes
echo ""
echo -e "${YELLOW}[6/9] 📦 Publicando assets de Laravel y paquetes...${NC}"
docker exec -w /var/www/html "$CONTAINER" php artisan vendor:publish --tag=laravel-assets --ansi --force 2>/dev/null || true
if docker exec -w /var/www/html "$CONTAINER" php artisan list 2>/dev/null | grep -q "livewire:publish"; then
    docker exec -w /var/www/html "$CONTAINER" php artisan livewire:publish --assets 2>/dev/null || true
fi
echo -e "${GREEN}✓ Assets publicados${NC}"

# [7/9] Limpiar y recachear
echo ""
echo -e "${YELLOW}[7/9] ⚡ Optimizando y reconstruyendo caché...${NC}"
docker exec -w /var/www/html "$CONTAINER" php artisan optimize:clear
docker exec -w /var/www/html "$CONTAINER" php artisan config:cache
docker exec -w /var/www/html "$CONTAINER" php artisan route:cache
docker exec -w /var/www/html "$CONTAINER" php artisan view:cache
docker exec -w /var/www/html "$CONTAINER" php artisan event:cache 2>/dev/null || true
# Limpieza de OPcache en memoria si aplica
docker exec -w /var/www/html "$CONTAINER" php -r "if(function_exists('opcache_reset')) { opcache_reset(); }" 2>/dev/null || true
echo -e "${GREEN}✓ Caché reconstruida y optimizada${NC}"

# [8/9] Workers
echo ""
echo -e "${YELLOW}[8/9] 🔄 Reiniciando workers de colas...${NC}"
docker exec -w /var/www/html "$CONTAINER" php artisan queue:restart 2>/dev/null || true
echo -e "${GREEN}✓ Señal de reinicio de colas enviada${NC}"

# [9/9] Reiniciar servicios y reactivar aplicación
echo ""
echo -e "${YELLOW}[9/9] 🔓 Reiniciando servicios y reactivando aplicación...${NC}"
cd "$PROJECT_DIR" || exit 1
# Se reinician los servicios ANTES de salir de mantenimiento para prevenir errores 502 a los usuarios
docker compose restart php nginx 2>/dev/null || docker restart "${PROJECT_NAME}_php" "${PROJECT_NAME}_nginx" 2>/dev/null || true
sleep 2

# Reactivar aplicación
docker exec -w /var/www/html "$CONTAINER" php artisan up
echo -e "${GREEN}✓ Servicios listos y aplicación activa${NC}"

END_TIME=$(date +%s)
DURATION=$((END_TIME - START_TIME))

echo ""
echo -e "${GREEN}=========================================="
echo "  ✅ Deploy completado: $PROJECT_NAME"
echo "==========================================${NC}"
echo -e "${BLUE}⏱️  Tiempo: ${DURATION}s | 🔀 Rama: $BRANCH${NC}"
echo ""
