#!/bin/sh
# Set client_max_body_size in every nginx config file found
for f in /etc/nginx/nginx.conf /etc/nginx/sites-enabled/default /etc/nginx/conf.d/default.conf /nginx.conf; do
    if [ -f "$f" ]; then
        # If client_max_body_size already exists, replace it; else add it inside the http or server block
        if grep -q "client_max_body_size" "$f"; then
            sed -i 's/client_max_body_size[^;]*;/client_max_body_size 100M;/g' "$f"
        else
            sed -i 's/http {/http {\n    client_max_body_size 100M;/' "$f"
            sed -i 's/server {/server {\n    client_max_body_size 100M;/' "$f"
        fi
    fi
done

# Also set PHP limits via .htaccess-style ini if php-fpm pool config exists
for f in /etc/php*/fpm/pool.d/www.conf /etc/php-fpm.d/www.conf; do
    if [ -f "$f" ]; then
        grep -q "upload_max_filesize" "$f" \
            && sed -i 's/php_admin_value\[upload_max_filesize\].*/php_admin_value[upload_max_filesize] = 100M/' "$f" \
            || echo "php_admin_value[upload_max_filesize] = 100M" >> "$f"
        grep -q "post_max_size" "$f" \
            && sed -i 's/php_admin_value\[post_max_size\].*/php_admin_value[post_max_size] = 100M/' "$f" \
            || echo "php_admin_value[post_max_size] = 100M" >> "$f"
    fi
done

echo "[upload-fix] nginx client_max_body_size set to 100M"
