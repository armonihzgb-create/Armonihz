#!/bin/sh
# Patch /nginx.conf to raise the upload/body size limit to 100M
NGINX_CONF="/nginx.conf"

if [ -f "$NGINX_CONF" ]; then
    if grep -q "client_max_body_size" "$NGINX_CONF"; then
        # Replace existing value
        sed -i 's/client_max_body_size[^;]*;/client_max_body_size 100M;/g' "$NGINX_CONF"
    else
        # Inject into the first 'server {' block
        sed -i 's/server {/server {\n    client_max_body_size 100M;/' "$NGINX_CONF"
    fi
    echo "[upload-fix] Patched $NGINX_CONF → client_max_body_size 100M"
else
    echo "[upload-fix] WARNING: $NGINX_CONF not found — limit not set"
fi
