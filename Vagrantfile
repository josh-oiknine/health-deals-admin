# -*- mode: ruby -*-
# vi: set ft=ruby :

Vagrant.configure("2") do |config|
  if Vagrant.has_plugin? "vagrant-vbguest"
    config.vbguest.no_install  = true
    config.vbguest.auto_update = false
    config.vbguest.no_remote   = true
  end
  
  config.vm.box = "generic/ubuntu2204" # 22 LTS
  config.vm.box_download_options = {"ssl-no-revoke" => true}
  
  # Disable default synced folder to prevent issues
  config.vm.synced_folder ".", "/vagrant", disabled: true
  
  # Network settings
  config.vm.network "private_network", ip: "192.168.56.10"
  config.vm.network "forwarded_port", guest: 80, host: 8080 # NGINX
  config.vm.network "forwarded_port", guest: 5432, host: 5433 # PostgreSQL
  config.vm.network "forwarded_port", guest: 6379, host: 6379 # Redis
  
  # Sync folder using basic settings first
  config.vm.synced_folder ".", "/var/www/health-deals-admin",
    owner: "vagrant",
    group: "vagrant",
    mount_options: ["dmode=775,fmode=664"]
  
  # VirtualBox specific settings
  config.vm.provider "virtualbox" do |vb|
    vb.memory = "2048"
    vb.cpus = 2
    vb.name = "health-deals-admin"

    vb.customize [ "guestproperty", "set", :id, "/VirtualBox/GuestAdd/VBoxService/--timesync-set-threshold", 1000 ]
    vb.customize [ "guestproperty", "set", :id, "/VirtualBox/GuestAdd/VBoxService/--timesync-set-start", 1000 ]
    vb.customize [ "guestproperty", "set", :id, "/VirtualBox/GuestAdd/VBoxService/--timesync-set-on-restore", 1000 ]
  end
  
  # Provisioning script
  config.vm.provision "shell", inline: <<-SHELL
    # Update system
    apt-get update
    apt-get upgrade -y
    
    # Install required packages
    apt-get install -y software-properties-common curl zip unzip
    
    # Add PHP repository
    add-apt-repository -y ppa:ondrej/php
    apt-get update
    
    # Install PHP 8.2 and extensions
    apt-get install -y php8.2-fpm php8.2-cli php8.2-common php8.2-pgsql php8.2-curl \
      php8.2-mbstring php8.2-xml php8.2-zip php8.2-bcmath php8.2-intl
    
    # Install NGINX
    apt-get install -y nginx
    
    # When Seting up the server you don't need full PostgreSQL just the cli tools
    # sudo apt install -y postgresql-client

    # Install PostgreSQL
    apt-get install -y postgresql postgresql-contrib
  
    # Configure PostgreSQL
    sudo -u postgres psql -c "CREATE USER postgres WITH PASSWORD 'postgres';"
    sudo -u postgres psql -c "ALTER USER postgres WITH SUPERUSER;"
    sudo -u postgres psql -c "CREATE DATABASE health_deals;"
    
    # Configure PostgreSQL to allow connections from host machine
    sed -i "s/#listen_addresses = 'localhost'/listen_addresses = '*'/" /etc/postgresql/14/main/postgresql.conf
    echo "host all all 0.0.0.0/0 md5" >> /etc/postgresql/14/main/pg_hba.conf
    systemctl restart postgresql
    
    # Install Redis
    apt-get install -y redis-server
    
    # Configure Redis to accept remote connections
    sed -i "s/bind 127.0.0.1/bind 0.0.0.0/" /etc/redis/redis.conf
    sed -i "s/protected-mode yes/protected-mode no/" /etc/redis/redis.conf
    
    # Restart Redis to apply changes
    systemctl restart redis-server
    
    # Install Composer 2
    curl -sS https://getcomposer.org/installer | php
    mv composer.phar /usr/local/bin/composer
    chmod +x /usr/local/bin/composer
    
    # Configure NGINX
    cat > /etc/nginx/sites-available/health-deals-admin << 'EOL'
server {
    listen 80;
    server_name localhost;
    root /var/www/health-deals-admin/public;
    index index.php index.html;

    client_max_body_size 100M;
    
    location / {
        try_files $uri $uri/ /index.php?$query_string;
    }

    location ~ \\.php$ {
        include snippets/fastcgi-php.conf;
        fastcgi_pass unix:/var/run/php/php8.2-fpm.sock;
        fastcgi_param SCRIPT_FILENAME $document_root$fastcgi_script_name;
        include fastcgi_params;
        fastcgi_read_timeout 120;
    }

    location ~ /\\.ht {
        deny all;
    }

    error_log /var/log/nginx/health-deals-admin_error.log;
    access_log /var/log/nginx/health-deals-admin_access.log;
}
EOL
    
    # Enable the site
    ln -sf /etc/nginx/sites-available/health-deals-admin /etc/nginx/sites-enabled/
    rm -f /etc/nginx/sites-enabled/default
    
    # Create required directories
    mkdir -p /var/www/health-deals-admin/storage/logs
    mkdir -p /var/www/health-deals-admin/storage/framework/{cache,sessions,views}
    
    # Set permissions
    chown -R vagrant:vagrant /var/www/health-deals-admin
    chmod -R 775 /var/www/health-deals-admin/storage
    
    # Configure PHP
    sed -i 's/memory_limit = .*/memory_limit = 512M/' /etc/php/8.2/fpm/php.ini
    sed -i 's/upload_max_filesize = .*/upload_max_filesize = 50M/' /etc/php/8.2/fpm/php.ini
    sed -i 's/post_max_size = .*/post_max_size = 50M/' /etc/php/8.2/fpm/php.ini
    sed -i 's/max_execution_time = .*/max_execution_time = 120/' /etc/php/8.2/fpm/php.ini
    
    # Restart services
    systemctl restart nginx
    systemctl restart php8.2-fpm
    
    # Copy environment file if it doesn't exist
    if [ ! -f /var/www/health-deals-admin/.env ]; then
      cp /var/www/health-deals-admin/.env.example /var/www/health-deals-admin/.env
    fi
    
    # Install project dependencies
    cd /var/www/health-deals-admin
    sudo -u vagrant composer install
    
    # Run database migrations and seeds using PHP to execute phinx
    cd /var/www/health-deals-admin
    sudo -u vagrant php vendor/bin/phinx migrate -e development
    sudo -u vagrant php vendor/bin/phinx seed:run -e development

    # Install ntpdate
    # apt-get install -y ntpdate
    # ntpdate -q pool.ntp.org
    
    echo "Installation completed! You can access the site at http://localhost:8080"
  SHELL
end 