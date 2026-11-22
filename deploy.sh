#!/bin/bash
PROJECT_NAME="pritec"

# ============================================
# Script de Deploy Automático para Laravel
# ============================================

GREEN='\033[0;32m'
RED='\033[0;31m'
YELLOW='\033[1;33m'
BLUE='\033[0;34m'
NC='\033[0m'

START_TIME=$(date +%s)
SCRIPT_DIR=$(cd "$(dirname "${BASH_SOURCE[0]}")" && pwd)
PROJECT_DIR="$SCRIPT_DIR"
SRC_DIR="$PROJECT_DIR/src"
CONTAINER="${PROJECT_NAME}_php"

cleanup() {
    if [ $? -ne 0 ]; then
        echo -e "\n${RED}❌ Error durante el deploy. Reactivando aplicación...${NC}"
        docker exec -w /var/www/html "$CONTAINER" php artisan up 2>/dev/null || true
    fi
}
trap cleanup EXIT

echo -e "${BLUE}=========================================="
echo "  🚀 Deploy Laravel: $PROJECT_NAME"
echo "==========================================${NC}"
echo ""

# [0/9] Mantenimiento
echo -e "${YELLOW}[0/9] 🚧 Activando modo mantenimiento...${NC}"
docker exec -w /var/www/html "$CONTAINER" php artisan down --retry=60 --refresh=5 2>/dev/null || true

cd "$SRC_DIR" || { echo -e "${RED}✗ Error: No existe el directorio $SRC_DIR${NC}"; exit 1; }

# [1/9] Git pull
echo ""
echo -e "${YELLOW}[1/9] ⬇️  Descargando cambios desde Git...${NC}"
git config core.autocrlf false 2>/dev/null || true
git stash --quiet 2>/dev/null || true

BRANCH=$(git rev-parse --abbrev-ref HEAD 2>/dev/null || echo "main")
if git pull origin "$BRANCH" 2>&1; then
    echo -e "${GREEN}✓ Cambios descargados desde rama '$BRANCH'${NC}"
else
    echo -e "${RED}✗ Error al descargar cambios desde Git${NC}"
    exit 1
fi

LAST_COMMIT=$(git log -1 --pretty=format:'%h - %s (%ar) por %an' 2>/dev/null || echo "N/A")
echo -e "${BLUE}    📝 Último commit: $LAST_COMMIT${NC}"

# [2/9] Composer
echo ""
echo -e "${YELLOW}[2/9] 📦 Composer install...${NC}"
docker exec -w /var/www/html "$CONTAINER" composer install --no-dev --optimize-autoloader --no-interaction 2>&1 | tail -5
echo -e "${GREEN}✓ Dependencias actualizadas${NC}"

# [3/9] Migraciones y Seeders (Aquí se sincroniza la IA automáticamente)
echo ""
echo -e "${YELLOW}[3/9] 🗄️  Migraciones y Seeders...${NC}"
docker exec -w /var/www/html "$CONTAINER" php artisan migrate --force 2>&1
docker exec -w /var/www/html "$CONTAINER" php artisan db:seed-pending --force 2>&1
echo -e "${GREEN}✓ Base de datos y semillas ejecutadas${NC}"

# [4/9] Compilar assets
echo ""
echo -e "${YELLOW}[4/9] 📦 Compilando assets...${NC}"
if [ -f "$SRC_DIR/package.json" ]; then
    docker exec -w /var/www/html "$CONTAINER" npm ci --production=false 2>/dev/null || docker exec -w /var/www/html "$CONTAINER" npm install --production=false 2>&1 | tail -3
    docker exec -w /var/www/html "$CONTAINER" npm run build 2>&1 | tail -5
    echo -e "${GREEN}✓ Assets compilados${NC}"
else
    echo -e "${BLUE}⏭️  Sin package.json, saltando${NC}"
fi

# [5/9] Permisos
echo ""
echo -e "${YELLOW}[5/9] 🔐 Ajustando permisos...${NC}"
docker exec "$CONTAINER" chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache
docker exec "$CONTAINER" chmod -R 775 /var/www/html/storage /var/www/html/bootstrap/cache
docker exec "$CONTAINER" chmod 666 /var/www/html/.env 2>/dev/null || true
echo -e "${GREEN}✓ Permisos ajustados${NC}"

# [6/9] Livewire assets
echo ""
echo -e "${YELLOW}[6/9] 📦 Publicando assets (Livewire, etc.)...${NC}"
docker exec -w /var/www/html "$CONTAINER" php artisan vendor:publish --force --tag=livewire:assets 2>/dev/null || true
docker exec -w /var/www/html "$CONTAINER" php artisan livewire:publish --assets 2>/dev/null || true
echo -e "${GREEN}✓ Assets publicados${NC}"

# [7/9] Limpiar y recachear
echo ""
echo -e "${YELLOW}[7/9] ⚡ Limpiando y recacheando...${NC}"
docker exec -w /var/www/html "$CONTAINER" php artisan config:clear
docker exec -w /var/www/html "$CONTAINER" php artisan route:clear
docker exec -w /var/www/html "$CONTAINER" php artisan view:clear
docker exec -w /var/www/html "$CONTAINER" php artisan config:cache
docker exec -w /var/www/html "$CONTAINER" php artisan route:cache
docker exec -w /var/www/html "$CONTAINER" php artisan view:cache
docker exec -w /var/www/html "$CONTAINER" php artisan event:cache 2>/dev/null || true
echo -e "${GREEN}✓ Cache reconstruida${NC}"

# [8/9] Workers
echo ""
echo -e "${YELLOW}[8/9] 🔄 Reiniciando workers...${NC}"
docker exec -w /var/www/html "$CONTAINER" php artisan queue:restart 2>/dev/null || true
echo -e "${GREEN}✓ Colas reiniciadas${NC}"

# [9/9] Reactivar y reiniciar Docker
echo ""
echo -e "${YELLOW}[9/9] 🔓 Finalizando y reiniciando servicios...${NC}"
docker exec -w /var/www/html "$CONTAINER" php artisan up 2>/dev/null || true
cd "$PROJECT_DIR" || exit 1
docker compose restart php nginx 2>/dev/null || docker restart "${PROJECT_NAME}_php" "${PROJECT_NAME}_nginx" 2>/dev/null || true
sleep 2
echo -e "${GREEN}✓ Servicios Docker reiniciados${NC}"

END_TIME=$(date +%s)
DURATION=$((END_TIME - START_TIME))

echo ""
echo -e "${GREEN}=========================================="
echo "  ✅ Deploy completado: $PROJECT_NAME"
echo "==========================================${NC}"
echo -e "${BLUE}⏱️  Tiempo: ${DURATION}s | 🔀 Rama: $BRANCH${NC}"
echo ""
