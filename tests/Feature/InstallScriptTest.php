<?php

namespace Tests\Feature;

use App\Models\BoilerplateCommand;
use App\Models\BoilerplateComposerPackage;
use App\Models\BoilerplateDockerService;
use App\Models\BoilerplateFile;
use App\Models\BoilerplateNpmPackage;
use App\Models\BoilerplateSailService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class InstallScriptTest extends TestCase
{
    use RefreshDatabase;

    public function test_install_script_returns_bash_script(): void
    {
        BoilerplateSailService::factory()->create(['name' => 'mysql', 'enabled' => true]);

        $response = $this->get('/my-test-app');

        $response->assertStatus(200);
        $response->assertHeader('Content-Type', 'text/plain; charset=UTF-8');
        $response->assertSee('APP_NAME="my-test-app"', false);
        $response->assertSee('DOCKER_IMAGE=', false);
    }

    public function test_install_script_uses_configurable_docker_image(): void
    {
        BoilerplateSailService::factory()->create(['name' => 'mysql', 'enabled' => true]);

        $response = $this->get('/my-app');

        $response->assertStatus(200);
        $response->assertSee('DOCKER_IMAGE="laravelsail/php85-composer:latest"', false);
    }

    public function test_install_script_ensures_sail_is_installed_before_sail_install(): void
    {
        BoilerplateSailService::factory()->create(['name' => 'mysql', 'enabled' => true]);

        $response = $this->get('/my-app');

        $response->assertStatus(200);
        $response->assertSee('composer show laravel/sail 2>/dev/null || composer require laravel/sail --no-interaction', false);
    }

    public function test_install_script_pulls_image_before_docker_run(): void
    {
        BoilerplateSailService::factory()->create(['name' => 'mysql', 'enabled' => true]);

        $response = $this->get('/my-app');

        $response->assertStatus(200);
        $response->assertSee('docker pull "$DOCKER_IMAGE"', false);
    }

    public function test_install_script_does_not_use_set_e(): void
    {
        BoilerplateSailService::factory()->create(['name' => 'mysql', 'enabled' => true]);

        $response = $this->get('/my-app');
        $content = $response->getContent();

        $response->assertStatus(200);
        $this->assertStringNotContainsString("\nset -e\n", $content);
    }

    public function test_install_script_includes_resilience_helpers(): void
    {
        BoilerplateSailService::factory()->create(['name' => 'mysql', 'enabled' => true]);

        $response = $this->get('/my-app');

        $response->assertStatus(200);
        $response->assertSee('warn()', false);
        $response->assertSee('retry()', false);
        $response->assertSee('WARNINGS=()', false);
    }

    public function test_install_script_rejects_invalid_app_names(): void
    {
        $this->get('/-invalid')->assertStatus(404);
        $this->get('/../../etc')->assertStatus(404);
    }

    public function test_install_script_includes_db_services(): void
    {
        BoilerplateSailService::factory()->create(['name' => 'mysql', 'enabled' => true]);
        BoilerplateSailService::factory()->create(['name' => 'redis', 'enabled' => true]);
        BoilerplateSailService::factory()->create(['name' => 'mailpit', 'enabled' => false]);

        $response = $this->get('/my-app');

        $response->assertStatus(200);
        $response->assertSee('mysql');
        $response->assertSee('redis');
    }

    public function test_install_script_with_parameter_overrides_defaults(): void
    {
        BoilerplateSailService::factory()->create(['name' => 'mysql', 'enabled' => true]);

        $response = $this->get('/my-app?with=pgsql');

        $response->assertStatus(200);
        $response->assertSee('SERVICES="pgsql"', false);
        $response->assertDontSee('mysql');
    }

    public function test_install_script_filters_invalid_url_services(): void
    {
        BoilerplateSailService::factory()->create(['name' => 'mysql', 'enabled' => true]);

        $response = $this->get('/my-app?with=invalid-service');

        $response->assertStatus(200);
        $response->assertDontSee('invalid-service');
    }

    public function test_install_script_includes_composer_packages(): void
    {
        BoilerplateSailService::factory()->create(['name' => 'mysql', 'enabled' => true]);
        BoilerplateComposerPackage::factory()->create(['package' => 'laravel/pail', 'dev' => true, 'enabled' => true]);

        $response = $this->get('/my-app');

        $response->assertStatus(200);
        $response->assertSee('laravel/pail');
    }

    public function test_install_script_includes_npm_packages(): void
    {
        BoilerplateSailService::factory()->create(['name' => 'mysql', 'enabled' => true]);
        BoilerplateNpmPackage::factory()->create(['package' => 'tailwindcss@^4.0', 'dev' => true, 'enabled' => true]);

        $response = $this->get('/my-app');

        $response->assertStatus(200);
        $response->assertSee('tailwindcss@^4.0');
    }

    public function test_install_script_includes_custom_files(): void
    {
        BoilerplateSailService::factory()->create(['name' => 'mysql', 'enabled' => true]);
        BoilerplateFile::factory()->create(['path' => 'custom/test.txt', 'content' => 'hello world', 'enabled' => true]);

        $response = $this->get('/my-app');

        $response->assertStatus(200);
        $response->assertSee('custom/test.txt');
        $response->assertSee('hello world');
    }

    public function test_install_script_replaces_placeholders_in_custom_files(): void
    {
        BoilerplateSailService::factory()->create(['name' => 'mysql', 'enabled' => true]);
        BoilerplateFile::factory()->create([
            'path' => 'README.md',
            'content' => 'Welcome to :APP_NAME: project',
            'enabled' => true,
        ]);

        $response = $this->get('/my-app');

        $response->assertStatus(200);
        $response->assertSee('Welcome to my-app project', false);
        $response->assertDontSee(':APP_NAME:', false);
    }

    public function test_install_script_replaces_services_placeholder_in_custom_files(): void
    {
        BoilerplateSailService::factory()->create(['name' => 'mysql', 'enabled' => true]);
        BoilerplateSailService::factory()->create(['name' => 'redis', 'enabled' => true]);
        BoilerplateFile::factory()->create([
            'path' => 'docs/services.md',
            'content' => 'Services: :SERVICES:',
            'enabled' => true,
        ]);

        $response = $this->get('/my-app');

        $response->assertStatus(200);
        $response->assertSee('Services: mysql,redis', false);
        $response->assertDontSee(':SERVICES:', false);
    }

    public function test_install_script_includes_docker_services(): void
    {
        BoilerplateSailService::factory()->create(['name' => 'mysql', 'enabled' => true]);
        BoilerplateDockerService::factory()->create([
            'name' => 'elasticsearch',
            'config' => "        image: elasticsearch:8\n        ports:\n            - '9200:9200'",
            'enabled' => true,
        ]);

        $response = $this->get('/my-app');

        $response->assertStatus(200);
        $response->assertSee('elasticsearch');
    }

    public function test_install_script_generates_vite_config_when_tailwind_vite_present(): void
    {
        BoilerplateSailService::factory()->create(['name' => 'mysql', 'enabled' => true]);
        BoilerplateNpmPackage::factory()->create(['package' => '@tailwindcss/vite', 'dev' => true, 'enabled' => true]);

        $response = $this->get('/my-app');

        $response->assertStatus(200);
        $response->assertSee('vite.config.js', false);
        $response->assertSee("tailwindcss from '@tailwindcss/vite'", false);
    }

    public function test_install_script_generates_override_file_for_sail_services_with_config(): void
    {
        BoilerplateSailService::factory()->create(['name' => 'mysql', 'enabled' => true, 'config' => "        ports:\n            - '3307:3306'"]);

        $response = $this->get('/my-app');

        $response->assertStatus(200);
        $response->assertSee('compose.override.yml', false);
        $response->assertSee('ports:', false);
        $response->assertSee("'3307:3306'", false);
    }

    public function test_install_script_skips_override_file_when_no_sail_services_have_config(): void
    {
        BoilerplateSailService::factory()->create(['name' => 'mysql', 'enabled' => true, 'config' => null]);

        $response = $this->get('/my-app');

        $response->assertStatus(200);
        $response->assertDontSee('compose.override.yml', false);
    }

    public function test_install_script_includes_sail_services_with_config_in_sail_install(): void
    {
        BoilerplateSailService::factory()->create(['name' => 'mysql', 'enabled' => true, 'config' => "        ports:\n            - '3307:3306'"]);
        BoilerplateSailService::factory()->create(['name' => 'redis', 'enabled' => true]);

        $response = $this->get('/my-app');

        $response->assertStatus(200);
        $response->assertSee('SERVICES="mysql,redis"', false);
        $response->assertSee('sail:install --with=', false);
        $response->assertSee('compose.override.yml', false);
    }

    public function test_install_script_skips_disabled_items(): void
    {
        BoilerplateSailService::factory()->create(['name' => 'mysql', 'enabled' => true]);
        BoilerplateComposerPackage::factory()->create(['package' => 'disabled/package', 'dev' => true, 'enabled' => false]);
        BoilerplateNpmPackage::factory()->create(['package' => 'disabled-npm', 'dev' => true, 'enabled' => false]);

        $response = $this->get('/my-app');

        $response->assertStatus(200);
        $response->assertDontSee('disabled/package');
        $response->assertDontSee('disabled-npm');
    }

    public function test_install_script_uses_retry_for_sail_operations(): void
    {
        BoilerplateSailService::factory()->create(['name' => 'mysql', 'enabled' => true]);

        $response = $this->get('/my-app');

        $response->assertStatus(200);
        $response->assertSee('retry 3 5 ./vendor/bin/sail pull ${SERVICES//,/ }', false);
        $response->assertSee('retry 2 5 ./vendor/bin/sail build', false);
    }

    public function test_install_script_includes_warning_summary(): void
    {
        BoilerplateSailService::factory()->create(['name' => 'mysql', 'enabled' => true]);

        $response = $this->get('/my-app');

        $response->assertStatus(200);
        $response->assertSee('${#WARNINGS[@]}', false);
    }

    public function test_install_script_fixes_permissions_before_local_file_operations(): void
    {
        BoilerplateSailService::factory()->create(['name' => 'mysql', 'enabled' => true]);
        BoilerplateNpmPackage::factory()->create(['package' => '@tailwindcss/vite', 'dev' => true, 'enabled' => true]);

        $response = $this->get('/my-app');
        $content = $response->getContent();

        $response->assertStatus(200);
        $chownPos = strpos($content, 'chown -R');
        $vitePos = strpos($content, 'vite.config.js');
        $this->assertNotFalse($chownPos, 'chown -R should be present in the script');
        $this->assertNotFalse($vitePos, 'vite.config.js should be present in the script');
        $this->assertLessThan($vitePos, $chownPos, 'Permission fix must run before local file operations');
    }

    public function test_install_script_uses_single_docker_run_for_scaffold_and_packages(): void
    {
        BoilerplateSailService::factory()->create(['name' => 'mysql', 'enabled' => true]);
        BoilerplateComposerPackage::factory()->create(['package' => 'laravel/pail', 'dev' => true, 'enabled' => true]);

        $response = $this->get('/my-app');
        $content = $response->getContent();

        $response->assertStatus(200);
        // Single docker run for scaffold + packages
        $this->assertEquals(1, substr_count($content, 'docker run'));
        // Packages and PARTIAL_FAIL pattern inside the same docker run
        $response->assertSee('laravel/pail', false);
        $response->assertSee('PARTIAL_FAIL:', false);
    }

    public function test_install_script_streams_docker_output_via_tee(): void
    {
        BoilerplateSailService::factory()->create(['name' => 'mysql', 'enabled' => true]);

        $response = $this->get('/my-app');
        $content = $response->getContent();

        $response->assertStatus(200);
        // Output is streamed via tee to a temp file
        $response->assertSee('SCAFFOLD_TMPFILE=$(mktemp)', false);
        $response->assertSee('| tee "$SCAFFOLD_TMPFILE"', false);
        $response->assertSee('SCAFFOLD_OUTPUT=$(cat "$SCAFFOLD_TMPFILE")', false);
        // Temp file is cleaned up
        $response->assertSee('trap "rm -f $SCAFFOLD_TMPFILE" EXIT', false);
        // Output is NOT captured via command substitution
        $this->assertStringNotContainsString('SCAFFOLD_OUTPUT=$(docker run', $content);
    }

    public function test_install_script_includes_post_install_commands(): void
    {
        BoilerplateSailService::factory()->create(['name' => 'mysql', 'enabled' => true]);
        BoilerplateCommand::factory()->create(['name' => 'Assign ports', 'command' => 'assign_forward_instance_ports', 'enabled' => true]);

        $response = $this->get('/my-app');

        $response->assertStatus(200);
        $response->assertSee('assign_forward_instance_ports', false);
        $response->assertSee('Running: Assign ports', false);
    }

    public function test_install_script_skips_disabled_commands(): void
    {
        BoilerplateSailService::factory()->create(['name' => 'mysql', 'enabled' => true]);
        BoilerplateCommand::factory()->create(['name' => 'Disabled cmd', 'command' => 'should_not_appear', 'enabled' => false]);

        $response = $this->get('/my-app');

        $response->assertStatus(200);
        $response->assertDontSee('should_not_appear', false);
    }

    public function test_install_script_runs_commands_in_sort_order(): void
    {
        BoilerplateSailService::factory()->create(['name' => 'mysql', 'enabled' => true]);
        BoilerplateCommand::factory()->create(['name' => 'Second', 'command' => 'run_second', 'sort_order' => 10, 'enabled' => true]);
        BoilerplateCommand::factory()->create(['name' => 'First', 'command' => 'run_first', 'sort_order' => 1, 'enabled' => true]);

        $response = $this->get('/my-app');
        $content = $response->getContent();

        $response->assertStatus(200);
        $firstPos = strpos($content, 'run_first');
        $secondPos = strpos($content, 'run_second');
        $this->assertLessThan($secondPos, $firstPos, 'Commands must run in sort_order');
    }

    public function test_install_script_replaces_placeholders_in_commands(): void
    {
        BoilerplateSailService::factory()->create(['name' => 'mysql', 'enabled' => true]);
        BoilerplateCommand::factory()->create([
            'name' => 'Setup ports',
            'command' => 'assign_ports :APP_NAME: :SERVICES:',
            'enabled' => true,
        ]);

        $response = $this->get('/my-app');

        $response->assertStatus(200);
        $response->assertSee('assign_ports my-app mysql', false);
        $response->assertDontSee(':APP_NAME:', false);
    }

    public function test_install_script_commands_use_warn_on_failure(): void
    {
        BoilerplateSailService::factory()->create(['name' => 'mysql', 'enabled' => true]);
        BoilerplateCommand::factory()->create(['name' => 'My cmd', 'command' => 'do_something', 'enabled' => true]);

        $response = $this->get('/my-app');

        $response->assertStatus(200);
        $response->assertSee(') < /dev/null || warn "Command failed: My cmd"', false);
    }

    public function test_install_script_ends_with_exec_shell(): void
    {
        BoilerplateSailService::factory()->create(['name' => 'mysql', 'enabled' => true]);

        $response = $this->get('/my-app');

        $response->assertStatus(200);
        $response->assertSee('exec $SHELL < /dev/tty', false);
    }
}
