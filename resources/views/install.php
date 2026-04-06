#!/bin/bash

APP_NAME="<?php echo e($appName); ?>"
SERVICES="<?php echo e($servicesString); ?>"

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

# ⟦1/4⟧ Critical: scaffold project + install Sail + packages
<?php
$devComposer = $composerPackages->where('dev', true);
$prodComposer = $composerPackages->where('dev', false);
$devNpm = $npmPackages->where('dev', true);
$prodNpm = $npmPackages->where('dev', false);
$hasPackages = $composerPackages->isNotEmpty() || $npmPackages->isNotEmpty();
?>
echo -e "${YELLOW}⟦1/4⟧ Creating Laravel project via Sail...${NC}"

SCAFFOLD_TMPFILE=$(mktemp)
trap "rm -f $SCAFFOLD_TMPFILE" EXIT

docker run --rm \
    --network host \
    -v "$(pwd)":/opt \
    -w /opt \
    composer:latest \
    bash -c '
        FAILED=""
        export PATH="$PATH:$(composer global config bin-dir --absolute --quiet)" && \
        apk add --no-cache nodejs npm && \
        composer global require laravel/installer && \
        laravel new '"${APP_NAME}"' --no-interaction && \
        cd '"${APP_NAME}"' && \
        composer show laravel/sail 2>/dev/null || composer require laravel/sail --no-interaction && \
        php artisan sail:install --with='"${SERVICES}"' --no-interaction
<?php if ($prodComposer->isNotEmpty()) { ?>
<?php foreach ($prodComposer as $pkg) { ?>
        composer require <?php echo e($pkg->package); ?> --no-interaction 2>&1 || FAILED="$FAILED composer:<?php echo e($pkg->package); ?>"
<?php } ?>
<?php } ?>
<?php if ($devComposer->isNotEmpty()) { ?>
<?php foreach ($devComposer as $pkg) { ?>
        composer require <?php echo e($pkg->package); ?> --dev --no-interaction 2>&1 || FAILED="$FAILED composer:<?php echo e($pkg->package); ?>"
<?php } ?>
<?php } ?>
<?php if ($prodNpm->isNotEmpty()) { ?>
<?php foreach ($prodNpm as $pkg) { ?>
        npm install <?php echo e($pkg->package); ?> 2>&1 || FAILED="$FAILED npm:<?php echo e($pkg->package); ?>"
<?php } ?>
<?php } ?>
<?php if ($devNpm->isNotEmpty()) { ?>
<?php foreach ($devNpm as $pkg) { ?>
        npm install --save-dev <?php echo e($pkg->package); ?> 2>&1 || FAILED="$FAILED npm:<?php echo e($pkg->package); ?>"
<?php } ?>
<?php } ?>
        if [ -n "$FAILED" ]; then echo "PARTIAL_FAIL:$FAILED"; fi
        echo "SCAFFOLD_OK"
    ' 2>&1 | tee "$SCAFFOLD_TMPFILE"

SCAFFOLD_OUTPUT=$(cat "$SCAFFOLD_TMPFILE")

if ! echo "$SCAFFOLD_OUTPUT" | grep -q "SCAFFOLD_OK"; then
    echo -e "${RED}Failed to scaffold project. Aborting.${NC}"
    exit 1
fi

echo -e "${GREEN}✓ Project scaffolded${NC}"

<?php if ($hasPackages) { ?>
if echo "$SCAFFOLD_OUTPUT" | grep -q "PARTIAL_FAIL:"; then
    FAILED_PKGS=$(echo "$SCAFFOLD_OUTPUT" | grep "PARTIAL_FAIL:" | sed 's/PARTIAL_FAIL://')
    for pkg in $FAILED_PKGS; do
        warn "Failed to install $pkg"
    done
else
    echo -e "${GREEN}✓ Additional packages installed${NC}"
fi
<?php } ?>

cd "$APP_NAME"

# Fix permissions (Docker creates files as root)
if command -v doas &>/dev/null; then
    SUDO="doas"
elif command -v sudo &>/dev/null; then
    SUDO="sudo"
else
    echo -e "${RED}Neither sudo nor doas is available. Exiting.${NC}"
    exit 1
fi

if $SUDO -n true 2>/dev/null; then
    $SUDO chown -R $USER: .
else
    echo -e "${YELLOW}Please provide your password so we can adjust your application's permissions.${NC}"
    echo ""
    $SUDO chown -R $USER: .
fi

# Local file operations (no Docker needed)
<?php if ($hasVitePlugin) { ?>
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
<?php } ?>

<?php foreach ($files as $file) { ?>
<?php if ($file->content === null || $file->content === '') { ?>
mkdir -p '<?php echo e($file->path); ?>'
<?php } else { ?>
mkdir -p "$(dirname '<?php echo e($file->path); ?>')"
cat > '<?php echo e($file->path); ?>' << 'FILEEOF'
<?php echo str_replace(array_keys($placeholders), array_values($placeholders), $file->content); ?>

FILEEOF
<?php } ?>
<?php } ?>

<?php if ($sailServiceOverrides->isNotEmpty()) { ?>
echo -e "${YELLOW}Creating compose.override.yaml...${NC}"
cat > compose.override.yaml << 'OVERRIDEEOF'
services:
<?php foreach ($sailServiceOverrides as $override) { ?>
    <?php echo e($override->name); ?>:
<?php echo $override->config; ?>

<?php } ?>
OVERRIDEEOF
echo -e "${GREEN}✓ Sail service overrides applied${NC}"
<?php } ?>

<?php if ($dockerServices->isNotEmpty()) { ?>
echo -e "${YELLOW}⟦2/4⟧ Adding custom Docker services...${NC}"

# Insert custom services before the top-level 'networks:' key
NETWORK_LINE=$(grep -n '^networks:' compose.yaml | head -1 | cut -d: -f1)
if [ -n "$NETWORK_LINE" ]; then
    head -n $((NETWORK_LINE - 1)) compose.yaml > compose.yaml.tmp
    cat >> compose.yaml.tmp << 'DOCKEREOF'
<?php foreach ($dockerServices as $service) { ?>
    <?php echo e($service->name); ?>:
<?php echo $service->config; ?>

<?php } ?>
DOCKEREOF
    tail -n +${NETWORK_LINE} compose.yaml >> compose.yaml.tmp
    mv compose.yaml.tmp compose.yaml
else
    cat >> compose.yaml << 'DOCKEREOF'
<?php foreach ($dockerServices as $service) { ?>
    <?php echo e($service->name); ?>:
<?php echo $service->config; ?>

<?php } ?>
DOCKEREOF
fi

echo -e "${GREEN}✓ Custom Docker services added${NC}"
<?php } else { ?>
echo -e "${YELLOW}⟦2/4⟧ No custom Docker services to add${NC}"
<?php } ?>

# ⟦3/4⟧ Pull + build (with retry, non-fatal)
echo -e "${YELLOW}⟦3/4⟧ Pulling Sail images...${NC}"
if [ -n "$SERVICES" ] && [ "$SERVICES" != "none" ]; then
    retry 3 5 ./vendor/bin/sail pull ${SERVICES//,/ } || warn "Some Sail images could not be pulled"
fi

echo -e "${YELLOW}⟦4/4⟧ Building containers...${NC}"
retry 2 5 ./vendor/bin/sail build || warn "Sail build had errors — run manually with: ./vendor/bin/sail build"

# Fix permissions again (sail build may create files as root)
$SUDO chown -R $USER: .

<?php if ($commands->isNotEmpty()) { ?>
# Post-install commands
<?php foreach ($commands as $cmd) { ?>
echo -e "${YELLOW}Running: <?php echo e($cmd->name); ?>...${NC}"
(<?php echo str_replace(array_keys($placeholders), array_values($placeholders), $cmd->command); ?>) < /dev/null || warn "Command failed: <?php echo e($cmd->name); ?>"
<?php } ?>
<?php } ?>

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

# Drop into the project directory
read -t 0.1 -n 10000 discard < /dev/tty 2>/dev/null || true
exec $SHELL < /dev/tty
