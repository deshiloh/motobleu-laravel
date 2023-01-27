@servers(['web' => 'pascalgavalda@172.16.78.131'])

@task('prod', ['confirm' => true])
    cd /var/www/vhosts/motobleu-paris.com/
    git checkout master
    git pull

    composer install
    php artisan migrate
@endtask

@task('beta', ['confirm' => true])
    cd /var/www/vhosts/motobleu-paris.com/test.motobleu-paris.com/www
    git checkout develop
    git pull

    composer install
    php artisan migrate:fresh --seed
@endtask
