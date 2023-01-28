@servers(['web' => 'pascalgavalda@172.16.78.131'])

@task('prod', ['confirm' => true])
    cd /var/www/vhosts/motobleu-paris.com/
    git checkout master
    git pull

    composer install --optimize-autoloader --no-dev
    php artisan migrate
    php artisan config:cache
    php artisan route:cache
    php artisan view:cache
@endtask

@task('beta', ['confirm' => true])
    cd /var/www/vhosts/motobleu-paris.com/test.motobleu-paris.com/www
    git checkout develop
    git pull

    /opt/plesk/php/8.1/bin/php /usr/lib/plesk-9.0/composer.phar install --optimize-autoloader
    /opt/plesk/php/8.1/bin/php artisan migrate:fresh --seed --force
    /opt/plesk/php/8.1/bin/php artisan route:cache
    /opt/plesk/php/8.1/bin/php artisan view:cache
    npm install && npm run build
@endtask
