<?php
namespace Deployer;
use Symfony\Component\Dotenv\Dotenv;

require 'recipe/common.php';
require 'recipe/rsync.php';
require 'recipe/cachetool.php';
require './vendor/autoload.php';

(new Dotenv(true))->loadEnv(dirname(__DIR__).'/html/.env.local');

// Configuration
set('application', 'guestlist');
set('hostname', getenv('DOMAIN'));
set('repository', getenv('REPO'));
set('ssh_type', 'native');
set('keep_releases', '3');
set('allow_anonymous_stats', false);
set('default_timeout', 360);

// Shared files/dirs between deploys
add('shared_files', ['.env.local', 'var/data.db']);
add('shared_dirs', ['public/userfiles']);

// Writable dirs by web server
set('allow_anonymous_stats', false);

set('rsync',[
    'timeout' => 3600,
    'exclude'      => [
        '.git',
        'deploy.php',
        '.ddev',
        'node_modules',
        '.npmrc',
        '.idea',
        'var/data.db'
    ],
    'exclude-file' => false,
    'include'      => [],
    'include-file' => false,
    'filter'       => [],
    'filter-file'  => false,
    'filter-perdir'=> false,
    'flags'        => 'rz', // Recursive, with compress
    'options'      => ['delete']
]);
set('rsync_src', __DIR__);
set('rsync_dest','{{release_path}}');

set('cachetool_args', '--web --web-path=./public --web-url=https://{{hostname}}');
// Hosts

host(getenv('HOST'))
    ->user(getenv('USER'))
    ->port('22')
    ->addSshOption('StrictHostKeyChecking', 'no')
    ->set('bin/php', 'php')
    ->set('bin/composer', 'composer')
    ->set('deploy_path', '~/html/application/guestlist');


/**
 * This was in, but shouldn't be here
 */
task('yarn', function () {
    runLocally('composer install');
    runLocally('yarn install');
    runLocally('yarn build');
})->local();

// Tasks
task('deploy', [
    'yarn',
    'deploy:info',
    'deploy:prepare',
    'deploy:lock',
    'deploy:release',
    'rsync',
    'deploy:shared',
    'deploy:symlink',
    'deploy:unlock',
    'cleanup',
])->desc('Deploy your project');
after('deploy', 'success');
after('deploy:symlink', 'cachetool:clear:opcache');

// [Optional] if deploy fails automatically unlock.
after('deploy:failed', 'deploy:unlock');