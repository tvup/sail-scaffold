#!/bin/bash

APP_NAME="<?php echo e($appName); ?>"
SERVICES="<?php echo e($servicesString); ?>"
DOCKER_IMAGE="<?php echo e($dockerImage); ?>"

# Colors
RED='\033[0;31m'
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
CYAN='\033[0;36m'
NC='\033[0m'

# Resilience helpers
WARNINGS=()

warn() {
    echo -e "${YELLOW}⚠ $1${NC}"
    WARNINGS+=("$1")
}

retry() {
    local max=$1; shift
    local delay=$1; shift
    local attempt=1
    while [ $attempt -le $max ]; do
        if "$@"; then return 0; fi
        echo -e "${YELLOW}  Attempt $attempt/$max failed. Retrying in ${delay}s...${NC}"
        sleep $delay
        attempt=$((attempt + 1))
    done
    return 1
}

echo -e "${CYAN}⟦*⟧ Creating Laravel project: ${APP_NAME}${NC}"
echo ""

# Docker preflight
if ! docker info > /dev/null 2>&1; then
    echo -e "${RED}Docker is not running. Please start Docker and try again.${NC}"
    exit 1
fi

echo -e "${GREEN}✓ Docker is running${NC}"

# Check directory doesn't exist
if [ -d "$APP_NAME" ]; then
    echo -e "${RED}Directory ${APP_NAME} already exists.${NC}"
    exit 1
fi

# ⟦1/5⟧ Critical: scaffold project + install Sail
echo -e "${YELLOW}⟦1/5⟧ Creating Laravel project via Sail...${NC}"

if ! docker run --rm \
    -v "$(pwd)":/opt \
    -w /opt \
    "$DOCKER_IMAGE" \
    bash -c "
        laravel new ${APP_NAME} --no-interaction && \
        cd ${APP_NAME} && \
        php artisan sail:install --with=${SERVICES} --no-interaction && \
        echo 'done'
    "; then
    echo -e "${RED}Failed to scaffold project. Aborting.${NC}"
    exit 1
fi

echo -e "${GREEN}✓ Project scaffolded${NC}"

# ⟦2/5⟧ Optional: install extra packages (fault-tolerant)
<?php
$devComposer = $composerPackages->where('dev', true);
$prodComposer = $composerPackages->where('dev', false);
$devNpm = $npmPackages->where('dev', true);
$prodNpm = $npmPackages->where('dev', false);
$hasPackages = $composerPackages->isNotEmpty() || $npmPackages->isNotEmpty();
?>
<?php if ($hasPackages): ?>
echo -e "${YELLOW}⟦2/5⟧ Installing additional packages...${NC}"

PACKAGE_OUTPUT=$(docker run --rm \
    -v "$(pwd)":/opt \
    -w /opt/<?php echo e($appName); ?> \
    "$DOCKER_IMAGE" \
    bash -c '
        FAILED=""
<?php if ($prodComposer->isNotEmpty()): ?>
<?php foreach ($prodComposer as $pkg): ?>
        composer require <?php echo e($pkg->package); ?> --no-interaction 2>&1 || FAILED="$FAILED composer:<?php echo e($pkg->package); ?>"
<?php endforeach; ?>
<?php endif; ?>
<?php if ($devComposer->isNotEmpty()): ?>
<?php foreach ($devComposer as $pkg): ?>
        composer require <?php echo e($pkg->package); ?> --dev --no-interaction 2>&1 || FAILED="$FAILED composer:<?php echo e($pkg->package); ?>"
<?php endforeach; ?>
<?php endif; ?>
<?php if ($prodNpm->isNotEmpty()): ?>
<?php foreach ($prodNpm as $pkg): ?>
        npm install <?php echo e($pkg->package); ?> 2>&1 || FAILED="$FAILED npm:<?php echo e($pkg->package); ?>"
<?php endforeach; ?>
<?php endif; ?>
<?php if ($devNpm->isNotEmpty()): ?>
<?php foreach ($devNpm as $pkg): ?>
        npm install --save-dev <?php echo e($pkg->package); ?> 2>&1 || FAILED="$FAILED npm:<?php echo e($pkg->package); ?>"
<?php endforeach; ?>
<?php endif; ?>
        if [ -n "$FAILED" ]; then echo "PARTIAL_FAIL:$FAILED"; fi
    ' 2>&1)

if echo "$PACKAGE_OUTPUT" | grep -q "PARTIAL_FAIL:"; then
    FAILED_PKGS=$(echo "$PACKAGE_OUTPUT" | grep "PARTIAL_FAIL:" | sed 's/PARTIAL_FAIL://')
    for pkg in $FAILED_PKGS; do
        warn "Failed to install $pkg"
    done
else
    echo -e "${GREEN}✓ Additional packages installed${NC}"
fi
<?php else: ?>
echo -e "${YELLOW}⟦2/5⟧ No additional packages to install${NC}"
<?php endif; ?>

cd "$APP_NAME"

# Local file operations (no Docker needed)
<?php if ($hasVitePlugin): ?>
cat > vite.config.js << 'VITEEOF'
import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';
import tailwindcss from '@tailwindcss/vite';

export default defineConfig({
    plugins: [
        laravel({
            input: ['resources/css/app.css', 'resources/js/app.js'],
            refresh: true,
        }),
        tailwindcss(),
    ],
});
VITEEOF
echo -e "${GREEN}✓ Vite config generated${NC}"
<?php endif; ?>

<?php foreach ($files as $file): ?>
<?php if ($file->content === null || $file->content === ''): ?>
mkdir -p '<?php echo e($file->path); ?>'
<?php else: ?>
mkdir -p "$(dirname '<?php echo e($file->path); ?>')"
cat > '<?php echo e($file->path); ?>' << 'FILEEOF'
<?php echo str_replace(array_keys($placeholders), array_values($placeholders), $file->content); ?>

FILEEOF
<?php endif; ?>
<?php endforeach; ?>

<?php if ($sailServiceOverrides->isNotEmpty()): ?>
echo -e "${YELLOW}Creating compose.override.yml...${NC}"
cat > compose.override.yml << 'OVERRIDEEOF'
services:
<?php foreach ($sailServiceOverrides as $override): ?>
    <?php echo e($override->name); ?>:
<?php echo $override->config; ?>

<?php endforeach; ?>
OVERRIDEEOF
echo -e "${GREEN}✓ Sail service overrides applied${NC}"
<?php endif; ?>

<?php if ($dockerServices->isNotEmpty()): ?>
echo -e "${YELLOW}⟦3/5⟧ Adding custom Docker services...${NC}"

# Append custom services to compose.yml
cat >> compose.yml << 'DOCKEREOF'
<?php foreach ($dockerServices as $service): ?>
    <?php echo e($service->name); ?>:
<?php echo $service->config; ?>

<?php endforeach; ?>
DOCKEREOF

echo -e "${GREEN}✓ Custom Docker services added${NC}"
<?php else: ?>
echo -e "${YELLOW}⟦3/5⟧ No custom Docker services to add${NC}"
<?php endif; ?>

# ⟦4/5⟧ Pull + build (with retry, non-fatal)
echo -e "${YELLOW}⟦4/5⟧ Pulling Sail images...${NC}"
retry 3 5 ./vendor/bin/sail pull || warn "Some Sail images could not be pulled"

echo -e "${YELLOW}⟦5/5⟧ Building containers...${NC}"
retry 2 5 ./vendor/bin/sail build || warn "Sail build had errors — run manually with: ./vendor/bin/sail build"

# Fix permissions
if command -v doas > /dev/null 2>&1; then
    doas chown -R $USER: .
elif command -v sudo > /dev/null 2>&1; then
    sudo chown -R $USER: .
fi

# Summary
echo ""
if [ ${#WARNINGS[@]} -eq 0 ]; then
    echo -e "${GREEN}⟦✓⟧ ${APP_NAME} is ready!${NC}"
else
    echo -e "${YELLOW}⟦!⟧ ${APP_NAME} created with ${#WARNINGS[@]} warning(s):${NC}"
    for w in "${WARNINGS[@]}"; do
        echo -e "  ${YELLOW}- $w${NC}"
    done
    echo ""
    echo -e "${CYAN}You can fix these manually after starting the project.${NC}"
fi
echo -e "${CYAN}    cd ${APP_NAME} && ./vendor/bin/sail up${NC}"
echo ""
