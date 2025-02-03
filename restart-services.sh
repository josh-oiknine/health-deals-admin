#!/bin/bash

echo "Restarting NGINX and PHP-FPM services..."

# Restart PHP-FPM
echo "Restarting PHP-FPM..."
sudo systemctl restart php8.2-fpm || {
    echo "Failed to restart PHP-FPM"
    exit 1
}

# Restart NGINX
echo "Restarting NGINX..."
sudo systemctl restart nginx || {
    echo "Failed to restart NGINX"
    exit 1
}

echo "Services restarted successfully!"

# Optional: Display service status
echo -e "\nService Status:"
echo "PHP-FPM Status:"
sudo systemctl status php8.2-fpm | head -n 3
echo -e "\nNGINX Status:"
sudo systemctl status nginx | head -n 3 