<?php

use Castor\Attribute\AsTask;

use function Castor\capture;
use function Castor\io;
use function Castor\run;

// ========================================================
//                      WELCOME
// ========================================================

#[AsTask(description: 'Welcome to Castor!')]
function hello(): void
{
    $currentUser = capture('whoami');

    io()->title(sprintf('Hello %s!', $currentUser));
}

// ========================================================
//                      DOCKER
// ========================================================

#[AsTask(description: 'Build the dockerized app', namespace: 'app', aliases: ['build'])]
function build(): void
{
    run('docker compose build php');
}

#[AsTask(description: 'Stop and remove dockerized app', namespace: 'app', aliases: ['down', 'stop'])]
function down(): void
{
    run('docker compose down');
}

#[AsTask(description: 'Start dockerized app', namespace: 'app', aliases: ['up', 'start'])]
function up(): void
{
    run('docker compose up --remove-orphans -d');
}

#[AsTask(description: 'Restart the dockerized app', namespace: 'app', aliases: ['restart'])]
function restart(): void
{
    down();
    build();
    up();
}

// ========================================================
//                  SETUP & CONFIGURATION
// ========================================================

#[AsTask(description: 'Install the application for the first time', namespace: 'app')]
function install(): void
{
    copy_env();
    run('composer install --no-interaction');
    generate_fixtures();
}

#[AsTask(description: 'Copy env variables', namespace: 'app')]
function copy_env(): void
{
    run("php -r \"file_exists('.env.dev.local') || copy('.env', '.env.dev.local');\"");
    run("php -r \"file_exists('.env.test.local') || copy('.env.test', '.env.test.local');\"");
}

#[AsTask(description: 'Generate an optimized .env.local.php file', namespace: 'app')]
function dump_env(): void
{
    run('composer dump-env prod');
}

// ========================================================
//                  DATABASE & MIGRATIONS
// ========================================================

#[AsTask(description: 'Create a migration file', namespace: 'app', aliases: ['make-migration', 'mm'])]
function make_migration(): void
{
    docker_compose_run('make:migration');
}

#[AsTask(description: 'Execute all the migrations in database', namespace: 'app', aliases: ['migrate-migrations', 'migrate', 'dmm'])]
function migrate_migrations(): void
{
    symfony_console('doctrine:migrations:migrate --no-interaction');
}

#[AsTask(description: 'Update schema of database', namespace: 'app', aliases: ['dsu', 'db-schema-update'])]
function db_schema_update(): void
{
    symfony_console('doctrine:schema:update --force');
}

#[AsTask(description: 'Create database, migrate migrations and load fixtures', namespace: 'app', aliases: ['generate-fixtures'])]
function generate_fixtures(): void
{
    symfony_console('doctrine:database:drop --force --if-exists');
    symfony_console('doctrine:database:create --if-not-exists');
    symfony_console('doctrine:migrations:migrate --no-interaction');
    symfony_console('doctrine:fixtures:load --no-interaction');
    cache_clear();
}

// ========================================================
//                       CACHE
// ========================================================

#[AsTask(description: 'Clear the application cache', namespace: 'app', aliases: ['cache-clear', 'cc'])]
function cache_clear(?string $env = null): void
{
    symfony_console('cache:clear'.($env ? " --env={$env}" : ''));
}

#[AsTask(description: 'Clear the application cache for test environment', namespace: 'app', aliases: ['cache-clear-test', 'cc-test'])]
function cc_test(): void
{
    symfony_console('cache:clear --env=test');
}

#[AsTask(description: 'Clear the application cache for prod environment', namespace: 'app', aliases: ['cache-clear-prod', 'cc-prod'])]
function cc_prod(): void
{
    symfony_console('cache:clear');
}

#[AsTask(description: 'Warms the application cache', namespace: 'app', aliases: ['cache-warmup', 'cw'])]
function cache_warmup(): void
{
    symfony_console('cache:warmup');
}

// ========================================================
//                     DEPLOYMENT
// ========================================================

#[AsTask(description: 'Deploy application for production', namespace: 'app', aliases: ['deploy'])]
function deploy(): void
{
    run('git pull origin main');
    migrate_migrations();
    cc_prod();
}

// ========================================================
//                   CODE QUALITY & LINTING
// ========================================================

#[AsTask(description: 'Run the quality code standards', namespace: 'app', aliases: ['quality-checks', 'quality', 'qa', 'cs'])]
function quality_check(): void
{
    phpstan();
    rector();
    php_cs_fixer();
    phpcs();
    debug_translation();
    lint_trans();
    lint_schema();
    lint_yaml();
    lint_twig();
    twigcs_fix();
    // lint_js_fix();
}

#[AsTask(description: 'Run PHP Coding Standards Fixer', namespace: 'app', aliases: ['php-cs-fixer'])]
function php_cs_fixer(): void
{
    docker_compose_run('./vendor/bin/php-cs-fixer fix --verbose');
}

#[AsTask(description: 'Run PHP Coding Standards Fixer in dry-run mode', namespace: 'app', aliases: ['php-cs-fixer-dry'])]
function php_cs_fixer_dry(): void
{
    docker_compose_run('./vendor/bin/php-cs-fixer fix --dry-run --verbose');
}

#[AsTask(description: 'Run PHP Code Sniffer', namespace: 'app', aliases: ['phpcs'])]
function phpcs(): void
{
    docker_compose_run('./vendor/bin/phpcs --runtime-set ignore_warnings_on_exit 1');
}

#[AsTask(description: 'Run PHP Code Beautifier and Fixer', namespace: 'app', aliases: ['phpcbf'])]
function phpcbf(): void
{
    docker_compose_run('./vendor/bin/phpcbf --runtime-set ignore_warnings_on_exit 1');
}

#[AsTask(description: 'Run PHPStan static analysis', namespace: 'app', aliases: ['phpstan', 'ps'])]
function phpstan(): void
{
    docker_compose_run('./vendor/bin/phpstan analyse -c phpstan.dist.neon');
}

#[AsTask(description: 'Run Rector to automatically refactor code', namespace: 'app', aliases: ['rector'])]
function rector(?string $option = null): void
{
    docker_compose_run('./vendor/bin/rector process'.($option ? " {$option}" : ''));
}

#[AsTask(description: 'Run Rector with dry mode', namespace: 'app', aliases: ['rector-dry'])]
function rector_dry(): void
{
    docker_compose_run('./vendor/bin/rector process --dry-run');
}

#[AsTask(description: 'Run Rector Swiss Knife tools', namespace: 'app', aliases: ['rector-swiss-knife'])]
function rector_swiss_knife(): void
{
    docker_compose_run('./vendor/bin/swiss-knife check-commented-code src tests');
    docker_compose_run('./vendor/bin/swiss-knife finalize-classes src tests');
    docker_compose_run('./vendor/bin/swiss-knife privatize-constants src tests');
}

#[AsTask(description: 'Debug translation files to find missing translations', namespace: 'app', aliases: ['debug-trans'])]
function debug_translation(): void
{
    symfony_console('debug:translation fr --only-missing');
}

#[AsTask(description: 'Lint translation messages', namespace: 'app', aliases: ['lint-trans'])]
function lint_trans(): void
{
    symfony_console('lint:translations --locale=en --locale=fr');
}

#[AsTask(description: 'Lint Doctrine schema', namespace: 'app', aliases: ['lint-schema'])]
function lint_schema(): void
{
    symfony_console('doctrine:schema:validate --skip-sync -vvv --no-interaction');
}

#[AsTask(description: 'Lint yaml files', namespace: 'app', aliases: ['lint-yaml'])]
function lint_yaml(): void
{
    symfony_console('lint:yaml ./config --parse-tags');
}

#[AsTask(description: 'Lint twig files', namespace: 'app', aliases: ['lint-twig'])]
function lint_twig(): void
{
    symfony_console('lint:twig ./templates');
}

#[AsTask(description: 'Check Twig Code Standards', namespace: 'app', aliases: ['twigcs'])]
function twigcs(): void
{
    docker_compose_run('./vendor/bin/twig-cs-fixer lint');
}

#[AsTask(description: 'Fix Twig Code Standards', namespace: 'app', aliases: ['twig-cs-fixer', 'twigcs-fix'])]
function twigcs_fix(): void
{
    docker_compose_run('./vendor/bin/twig-cs-fixer lint --fix');
}

#[AsTask(description: 'Lint JavaScript files', namespace: 'app', aliases: ['lint-js'])]
function lint_js(): void
{
    run('yarn eslint ./assets');
}

#[AsTask(description: 'Fix JavaScript files', namespace: 'app', aliases: ['lint-js-fix'])]
function lint_js_fix(): void
{
    run('yarn eslint ./assets --fix');
}

// ========================================================
//                       TESTING
// ========================================================

#[AsTask(description: 'Run all the tests', namespace: 'app', aliases: ['tests'])]
function tests(): void
{
    check_security();
    phpstan();
    rector_dry();
    php_cs_fixer_dry();
    phpcs();
    debug_translation();
    lint_trans();
    lint_schema();
    lint_yaml();
    lint_twig();
    twigcs();
    lint_js();
    cc_test();
    run('symfony php bin/phpunit tests --exclude-group=api');
}

#[AsTask(description: 'Run tests for production environment', namespace: 'app', aliases: ['tests-prod'])]
function tests_prod(): void
{
    symfony_console('cache:clear --env=test');
    run('symfony php bin/phpunit tests --exclude-group=api');
}

#[AsTask(description: 'Run tests with coverage', namespace: 'app', aliases: ['tests-coverage'])]
function tests_coverage(): void
{
    run('XDEBUG_MODE=coverage symfony php bin/phpunit tests --coverage-html var/coverage --exclude-group=api');
}

// ========================================================
//                      SECURITY
// ========================================================

#[AsTask(description: 'Run symfony check-security', namespace: 'app', aliases: ['security-check', 'security'])]
function check_security(): void
{
    run('symfony check:security');
}

// ========================================================
//                    TOOLS & UTILITIES
// ========================================================

#[AsTask(description: 'Execute Composer install for production', namespace: 'app', aliases: ['composer-install'])]
function composer_install(): void
{
    run('php composer.phar install');
}

#[AsTask(description: 'Generate static error pages', namespace: 'app', aliases: ['dump-error'])]
function dump_error(): void
{
    symfony_console('error:dump var/cache/prod/error_pages/ --env=prod');
}

// ========================================================
//                       HELPERS
// ========================================================

function symfony_console(string $command): void
{
    docker_compose_run(sprintf('symfony console %s', $command));
}

function docker_compose_run(string $command): void
{
    run(sprintf('docker-compose exec php %s', $command));
}
