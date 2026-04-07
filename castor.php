<?php

declare(strict_types=1);

use Castor\Attribute\AsArgument;
use Castor\Attribute\AsOption;
use Castor\Attribute\AsTask;
use Symfony\Component\Process\Process;

use function Castor\run;

// ========================================================
//                      DOCKER
// ========================================================

#[AsTask(description: 'Build the dockerized app', namespace: 'app', aliases: ['build'])]
function build(): void
{
    run_docker_compose('build --pull --no-cache');
}

#[AsTask(description: 'Stop and remove dockerized app', namespace: 'app', aliases: ['down', 'stop'])]
function down(): void
{
    run_docker_compose('down --remove-orphans');
}

#[AsTask(description: 'Show live logs of the dockerized app', namespace: 'app', aliases: ['logs'])]
function logs(): void
{
    run_docker_compose('logs --tail=0 --follow');
}

#[AsTask(description: 'Start dockerized app', namespace: 'app', aliases: ['up', 'start'])]
function up(): void
{
    run_docker_compose('up --wait');
}

#[AsTask(description: 'Restart the dockerized app', namespace: 'app', aliases: ['restart'])]
function restart(): void
{
    down();
    run_docker_compose('up --build --wait');
}

// ========================================================
//                  SETUP & CONFIGURATION
// ========================================================

#[AsTask(description: 'Install the application for the first time', namespace: 'app', aliases: ['install'])]
function install(): void
{
    copy_env();
    run_php('composer install --no-interaction');
    generate_fixtures();
}

#[AsTask(description: 'Copy env variables', namespace: 'app', aliases: ['copy-env'])]
function copy_env(): void
{
    if (!file_exists('.env.dev.local')) {
        copy('.env', '.env.dev.local');
    }

    if (!file_exists('.env.test.local')) {
        copy('.env.test', '.env.test.local');
    }
}

#[AsTask(description: 'Generate an optimized .env.local.php file', namespace: 'app', aliases: ['dump-env'])]
function dump_env(
    #[AsArgument(name: 'env', autocomplete: ['dev', 'test', 'prod'])]
    string $env = 'prod'
): void {
    run_php(sprintf('composer dump-env %s', $env));
}

// ========================================================
//                  DATABASE & MIGRATIONS
// ========================================================

#[AsTask(description: 'Create a migration file', namespace: 'app', aliases: ['make-migration', 'mm'])]
function make_migration(): void
{
    run_symfony_console('make:migration');
}

#[AsTask(description: 'Execute all the migrations in database', namespace: 'app', aliases: ['migrate-migrations', 'migrate', 'dmm'])]
function migrate_migrations(): void
{
    run_symfony_console('doctrine:migrations:migrate --no-interaction');
}

#[AsTask(description: 'Update schema of database', namespace: 'app', aliases: ['dsu', 'db-schema-update'])]
function db_schema_update(): void
{
    if ('prod' === get_app_env()) {
        throw new \RuntimeException('Refusing to update database schema in prod environment. Please create a migration instead.');
    }

    run_symfony_console('doctrine:schema:update --force');
}

#[AsTask(description: 'Create database, migrate migrations and load fixtures', namespace: 'app', aliases: ['generate-fixtures', 'fixtures'])]
function generate_fixtures(): void
{
    if ('prod' === get_app_env()) {
        throw new \RuntimeException('Refusing to reset database in prod environment.');
    }

    run_symfony_console('doctrine:database:drop --force --if-exists');
    run_symfony_console('doctrine:database:create --if-not-exists');
    run_symfony_console('doctrine:migrations:migrate --no-interaction');
    run_symfony_console('doctrine:fixtures:load --no-interaction');
    clear_cache();
}

// ========================================================
//                       CACHE
// ========================================================

#[AsTask(description: 'Clear the application cache', namespace: 'app', aliases: ['clear-cache', 'cc'])]
function clear_cache(
    #[AsArgument(name: 'env', autocomplete: ['dev', 'test', 'prod'])]
    ?string $env = 'dev'
): void {
    $envOption = null !== $env ? " --env={$env}" : '';

    run_symfony_console('cache:clear'.$envOption);

    if ('prod' !== $env) {
        clear_redis_cache(env: $env);
    }
}

#[AsTask(description: 'Clear the Redis cache', namespace: 'app', aliases: ['clear-cache-redis', 'cc-redis'])]
function clear_redis_cache(
    #[AsArgument(name: 'pool')]
    ?string $pool = 'cache.app',
    #[AsOption(name: 'env', autocomplete: ['dev', 'test', 'prod'])]
    ?string $env = 'dev'
): void {
    run_symfony_console("cache:pool:clear {$pool}".(null !== $env ? " --env={$env}" : ''));
}

#[AsTask(description: 'Warms the application cache', namespace: 'app', aliases: ['cache-warmup', 'cw'])]
function cache_warmup(): void
{
    run_symfony_console('cache:warmup');
}

// ========================================================
//                     DEPLOYMENT
// ========================================================

#[AsTask(description: 'Deploy application for production', namespace: 'app', aliases: ['deploy'])]
function deploy(string $branch = 'main'): void
{
    run("git pull origin {$branch}");
    migrate_migrations();
    clear_cache('prod');
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
    run_php('./vendor/bin/php-cs-fixer fix --verbose');
}

#[AsTask(description: 'Run PHP Coding Standards Fixer in dry-run mode', namespace: 'app', aliases: ['php-cs-fixer-dry'])]
function php_cs_fixer_dry(): void
{
    run_php('./vendor/bin/php-cs-fixer fix --dry-run --verbose');
}

#[AsTask(description: 'Run PHP Code Sniffer', namespace: 'app', aliases: ['phpcs'])]
function phpcs(): void
{
    run_php('./vendor/bin/phpcs --runtime-set ignore_warnings_on_exit 1');
}

#[AsTask(description: 'Run PHP Code Beautifier and Fixer', namespace: 'app', aliases: ['phpcbf'])]
function phpcbf(): void
{
    run_php('./vendor/bin/phpcbf --runtime-set ignore_warnings_on_exit 1');
}

#[AsTask(description: 'Run PHPStan static analysis', namespace: 'app', aliases: ['phpstan', 'ps'])]
function phpstan(): void
{
    run_php('./vendor/bin/phpstan analyse -c phpstan.dist.neon');
}

#[AsTask(description: 'Run Rector to automatically refactor code', namespace: 'app', aliases: ['rector'])]
function rector(
    #[AsArgument(name: 'option', autocomplete: ['--dry-run'])]
    ?string $option = null
): void {
    run_php('./vendor/bin/rector process'.($option ? " {$option}" : ''));
}

#[AsTask(description: 'Run Rector with dry mode', namespace: 'app', aliases: ['rector-dry'])]
function rector_dry(): void
{
    run_php('./vendor/bin/rector process --dry-run');
}

#[AsTask(description: 'Run Rector Swiss Knife tools', namespace: 'app', aliases: ['rector-swiss-knife'])]
function rector_swiss_knife(): void
{
    run_php('./vendor/bin/swiss-knife check-commented-code src tests');
    run_php('./vendor/bin/swiss-knife finalize-classes src tests');
    run_php('./vendor/bin/swiss-knife privatize-constants src tests');
}

#[AsTask(description: 'Debug translation files to find missing translations', namespace: 'app', aliases: ['debug-trans'])]
function debug_translation(): void
{
    run_symfony_console('debug:translation en --only-missing');
    run_symfony_console('debug:translation fr --only-missing');
}

#[AsTask(description: 'Lint translation messages', namespace: 'app', aliases: ['lint-trans'])]
function lint_trans(): void
{
    run_symfony_console('lint:translations --locale=en --locale=fr');
}

#[AsTask(description: 'Lint Doctrine schema', namespace: 'app', aliases: ['lint-schema'])]
function lint_schema(): void
{
    run_symfony_console('doctrine:schema:validate --skip-sync -vvv --no-interaction');
}

#[AsTask(description: 'Lint yaml files', namespace: 'app', aliases: ['lint-yaml'])]
function lint_yaml(): void
{
    run_symfony_console('lint:yaml ./config --parse-tags');
}

#[AsTask(description: 'Lint twig files', namespace: 'app', aliases: ['lint-twig'])]
function lint_twig(): void
{
    run_symfony_console('lint:twig ./templates');
}

#[AsTask(description: 'Check Twig Code Standards', namespace: 'app', aliases: ['twigcs'])]
function twigcs(): void
{
    run_php('./vendor/bin/twig-cs-fixer lint');
}

#[AsTask(description: 'Fix Twig Code Standards', namespace: 'app', aliases: ['twig-cs-fixer', 'twigcs-fix'])]
function twigcs_fix(): void
{
    run_php('./vendor/bin/twig-cs-fixer lint --fix');
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

#[AsTask(description: 'Run all the tests', namespace: 'app', aliases: ['test-all'])]
function test_all(): void
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
    // lint_js();
    test();
}

#[AsTask(description: 'Run tests with Paratest', namespace: 'app', aliases: ['test'])]
function test(string $options = ''): void
{
    run_php('./vendor/bin/paratest tests --runner WrapperRunner '.$options);
}

#[AsTask(description: 'Run tests coverage with Paratest', namespace: 'app', aliases: ['test-coverage'])]
function test_coverage(string $options = ''): void
{
    run_docker_compose('exec -e XDEBUG_MODE=coverage php ./vendor/bin/paratest tests --runner WrapperRunner --coverage-html ./var/coverage '.$options);
}

// ========================================================
//                      SECURITY
// ========================================================

#[AsTask(description: 'Run security checks with Composer Audit', namespace: 'app', aliases: ['security-check', 'security'])]
function check_security(): void
{
    run_php('composer audit');
}

// ========================================================
//                    TOOLS & UTILITIES
// ========================================================

#[AsTask(description: 'Run Symfony console command', namespace: 'app', aliases: ['sf'])]
function sf(string $symfonyCommand): void
{
    run_symfony_console($symfonyCommand);
}

#[AsTask(description: 'Generate static error pages', namespace: 'app', aliases: ['dump-error'])]
function dump_error(): void
{
    run_symfony_console('error:dump var/cache/prod/error_pages/ --env=prod');
}

// ========================================================
//                       HELPERS
// ========================================================

function run_symfony_console(string $command): Process
{
    return run_php(sprintf('bin/console %s', $command));
}

function run_php(string $command): Process
{
    return run_docker_compose(sprintf('exec php %s', $command));
}

function run_docker_compose(string $command): Process
{
    return run(sprintf('docker compose %s', $command));
}

function get_app_env(): string
{
    return run_php('printenv APP_ENV')
        ->getOutput()
        |> trim(...)
    ;
}
