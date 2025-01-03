@servers(['web' => 'pascalgavalda@51.38.226.127'])

@task('reloadBetaDatabase', ['confirm' => true])
    cd /var/www/vhosts/motobleu-paris.com/test.motobleu-paris.com/www
    /opt/plesk/php/8.1/bin/php artisan migrate:fresh
    /opt/plesk/php/8.1/bin/php artisan app:import
@endtask

@story('deploy')
    install-temp-project
    install-dependencies
    backup
    restore-backup
    set-current
    clean
@endstory

@task('install-temp-project')
    cd /var/www/vhosts/motobleu-paris.com/www/

    git clone -b develop https://github.com/deshiloh/motobleu-laravel ./temp
    cp ./.env-prod temp/.env
@endtask

@task('install-dependencies')
    cd /var/www/vhosts/motobleu-paris.com/www/temp

    source /etc/profile

    /opt/plesk/php/8.1/bin/php /usr/lib/plesk-9.0/composer.phar install --optimize-autoloader --no-dev
    /opt/plesk/node/23/bin/npm install && /opt/plesk/node/23/bin/npm run build
    /opt/plesk/php/8.1/bin/php artisan key:generate
    /opt/plesk/php/8.1/bin/php artisan migrate --force
    /opt/plesk/php/8.1/bin/php artisan route:cache
    /opt/plesk/php/8.1/bin/php artisan view:cache
@endtask

@task('backup')
    cd /var/www/vhosts/motobleu-paris.com/www/

    if [ -d "/var/www/vhosts/motobleu-paris.com/www/current" ]; then
        cp -R ./current/storage/app/photos ./photos
        cp -R ./current/storage/app/google-calendar ./google-calendar
    fi
@endtask

@task('restore-backup')
    cd /var/www/vhosts/motobleu-paris.com/www/

    if [ -d "/var/www/vhosts/motobleu-paris.com/www/photos" ]; then
        mv ./photos ./temp/storage/app/photos
    fi

    cp -R ./google-calendar ./temp/storage/app/
@endtask

@task('set-current')
    cd /var/www/vhosts/motobleu-paris.com/www/

    rm -rf current/
    mv ./temp/ ./current/

    cd /var/www/vhosts/motobleu-paris.com/www/current

    /opt/plesk/php/8.1/bin/php artisan storage:link
@endtask

@task('clean')
    cd /var/www/vhosts/motobleu-paris.com/www/

    rm -rf ./photos
@endtask
