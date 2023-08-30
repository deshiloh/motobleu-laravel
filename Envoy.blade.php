@servers(['web' => 'pascalgavalda@172.16.78.131'])

@task('beta', ['confirm' => true, 'on' => 'web'])
    cd /var/www/vhosts/motobleu-paris.com/test.motobleu-paris.com/
    mkdir temp

    cd /var/www/vhosts/motobleu-paris.com/test.motobleu-paris.com/temp
    git clone -b develop https://github.com/deshiloh/motobleu-laravel ./

    cp ../.env-beta ./.env
    /opt/plesk/php/8.1/bin/php /usr/lib/plesk-9.0/composer.phar install --optimize-autoloader --no-dev
    npm install && npm run build
    /opt/plesk/php/8.1/bin/php artisan key:generate
    /opt/plesk/php/8.1/bin/php artisan migrate --force
    /opt/plesk/php/8.1/bin/php artisan route:cache
    /opt/plesk/php/8.1/bin/php artisan view:cache

    cd /var/www/vhosts/motobleu-paris.com/test.motobleu-paris.com/
    cp -R ./www/storage/app/photos ./photos
    rm -rf www/
    mv ./temp/ ./www/
    mv ./photos ./www/storage/app/photos
    cp -R ./google-calendar ./www/storage/app/

    cd /var/www/vhosts/motobleu-paris.com/test.motobleu-paris.com/www
    /opt/plesk/php/8.1/bin/php artisan storage:link
@endtask

@task('reloadBetaDatabase', ['confirm' => true])
    cd /var/www/vhosts/motobleu-paris.com/test.motobleu-paris.com/www
    /opt/plesk/php/8.1/bin/php artisan migrate:fresh
    /opt/plesk/php/8.1/bin/php artisan app:import
@endtask

@task('prod', ['confirm' => true])
cd /var/www/vhosts/motobleu-paris.com/new/
mkdir temp

cd /var/www/vhosts/motobleu-paris.com/new/temp
git clone -b develop https://github.com/deshiloh/motobleu-laravel ./

cp ../.env-prod ./.env
/opt/plesk/php/8.1/bin/php /usr/lib/plesk-9.0/composer.phar install --optimize-autoloader --no-dev
npm install && npm run build
/opt/plesk/php/8.1/bin/php artisan key:generate
/opt/plesk/php/8.1/bin/php artisan migrate --force
/opt/plesk/php/8.1/bin/php artisan route:cache
/opt/plesk/php/8.1/bin/php artisan view:cache

cd /var/www/vhosts/motobleu-paris.com/new/
cp -R ./www/storage/app/photos ./photos
cp -R ./www/storage/app/google-calendar ./google-calendar
rm -rf www/
mv ./temp/ ./www/
mv ./photos ./www/storage/app/photos
cp -R ./google-calendar ./www/storage/app/

cd /var/www/vhosts/motobleu-paris.com/new/www
/opt/plesk/php/8.1/bin/php artisan storage:link
@endtask
