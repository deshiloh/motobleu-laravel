@include('vendor/autoload.php')
@servers(['web' => 'pascalgavalda@172.16.78.131'])

@story('deploy')
    get-project
@endstory

@task('get-project')
    cd /var/www/vhosts/motobleu-paris.com/test.motobleu-paris.com/www
    git checkout master
    git pull
@endtask
