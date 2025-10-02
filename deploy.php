<?php
namespace Deployer;

require 'recipe/laravel.php';

// Config
set('application', 'motobleu');
set('repository', 'https://github.com/deshiloh/motobleu-laravel.git');
set('branch', 'laravel11-update');
set('git_tty', true);
set('keep_releases', 3);

set('php_path', '/opt/plesk/php/8.1/bin/php');
set('composer_path', '/usr/lib/plesk-9.0/composer.phar');
set('node_path', '/opt/plesk/node/23/bin');
set('writable_mode', 'chmod');

// Fichiers/Dossiers persistants
set('shared_files', ['.env']);
set('shared_dirs', [
    'storage/app/photos',
    'storage/app/google-calendar',
]);

set('writable_dirs', [
    'bootstrap/cache',
    'storage',
    'storage/app',
    'storage/framework',
    'storage/logs',
]);

// Hôte
//host('production')
//    ->setHostname('51.38.226.127')
//    ->setRemoteUser('pascalgavalda')
//    ->set('deploy_path', '/var/www/vhosts/motobleu-paris.com/www');

host('local-docker')
    ->setHostname('localhost')
    ->set('remote_user', 'deploy')
    ->set('port', 2222)
    ->set('identity_file', './docker-deployer-test/ssh/id_rsa')
    ->set('http_user', 'deploy')
    ->set('deploy_path', '/var/www/app')
    ->set('php_path', '/usr/bin/php') // adapté à ton image Docker
    ->set('composer_path', '/usr/local/bin/composer')
    ->set('node_path', '/usr/bin'); // à adapter selon ton image

// Hook après installation
after('deploy:vendors', function () {
    run('{{php_path}} artisan key:generate');
    run('{{php_path}} artisan migrate --force');
    run('{{php_path}} artisan route:cache');
    run('{{php_path}} artisan view:cache');
    run('{{php_path}} artisan storage:link');
});

// Hook pour npm
task('build_assets', function () {
    run("cd {{release_path}} && {{node_path}}/npm install && {{node_path}}/npm run build");
});
after('deploy:vendors', 'build_assets');

// Déploiement principal
task('deploy', [
    'deploy:prepare',
    'deploy:vendors',
    'deploy:shared',
    'deploy:writable',
    'deploy:publish',
]);

after('deploy:failed', 'deploy:unlock');
