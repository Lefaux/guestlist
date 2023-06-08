<?php

namespace Deployer;

require 'recipe/common.php';
require 'contrib/rsync.php';
require 'contrib/cachetool.php';

// Project name
set('application', 'guestlist');
set('application_path', '~/html/application/{{application}}');
set('application_public', '~/html/application/{{application}}/data');
set('rsync', [
    'exclude' => [
        '/.ddev',
        '/.github',
        '/.git',
        '/assets',
        '/data',
        '/ssh',
        '/var',
        '/.editorconfig',
        '/.gitattributes',
        '/.gitignore',
        '/.env.local',
        '/.php-cs-fixer.cache',
        '/.php-cs-fixer.dist.php',
        '/deploy.php',
        '/deployer.phar',
        '/internal_accounts',
        '/README.md',
    ],
    'exclude-file' => false,
    'include'      => [],
    'include-file' => false,
    'filter'       => [],
    'filter-file'  => false,
    'filter-perdir' => false,
    'flags'        => 'az',
    'options'      => ['delete', 'delete-after', 'force'],
    'timeout'      => 3600,
]);
set('shared_files', [
    '.env.local'
]);
set('shared_dirs', [
    'var/lock',
    'var/log',
    'public/uploads'
]);

set('bin/php', 'php_cli');

set(
    'bin/console',
    '{{bin/php}} {{release_or_current_path}}/bin/console'
);

// Hosts
host(getenv('SSH_HOST'))
    ->set('remote_user', getenv('SSH_USER'))
    ->set('keep_releases', '1')
    ->set('deploy_path', '{{application_path}}/site')
    ->set('rsync_src', __DIR__)
    ->set('rsync_dest','{{release_path}}')
    ->set('ssh_arguments', ['-o UserKnownHostsFile=/dev/null']);

// TYPO3 Tasks
task('symfony:migrate', function () { run("{{bin/console}} doctrine:migrations:migrate --no-interaction"); });
task('symfony:assets:install', function () { run("{{bin/console}} assets:install"); });
task('symfony:cache:clear', function () { run("{{bin/console}} cache:clear"); });
task('symfony:cache:warmup', function () { run("{{bin/console}} cache:warmup"); });
task('symfony', [
    'symfony:migrate',
    'symfony:assets:install',
    'symfony:cache:clear',
    'symfony:cache:warmup',
]);




// Task to only deploy code
task('deploy:data', [
    'deploy:info',
    'deploy:setup',
    'deploy:lock',
    'deploy:release',
    'rsync',
    'deploy:shared',
    'deploy:writable',
    'deploy:symlink',
    'deploy:unlock',
    'deploy:cleanup',
    'deploy:success',
]);

// Main Task
task('deploy', [
    'deploy:info',
    'deploy:setup',
    'deploy:lock',
    'deploy:release',
    'rsync',
    'deploy:shared',
    'deploy:writable',
    'deploy:symlink',
//    'cachetool:clear:opcache',
//    'cachetool:clear:apcu',
    'symfony',
    'deploy:unlock',
    'deploy:cleanup',
    'deploy:success',
])->desc('Deploy your project');

// Unlock after failed
after(
    'deploy:failed',
    'deploy:unlock'
);