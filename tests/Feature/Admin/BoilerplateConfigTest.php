<?php

namespace Tests\Feature\Admin;

use App\Models\BoilerplateComposerPackage;
use App\Models\BoilerplateDockerService;
use App\Models\BoilerplateFile;
use App\Models\BoilerplateNpmPackage;
use App\Models\BoilerplateSailService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class BoilerplateConfigTest extends TestCase
{
    use RefreshDatabase;

    // --- Index ---

    public function test_admin_index_shows_overview(): void
    {
        $response = $this->get('/admin');

        $response->assertStatus(200);
        $response->assertSee('Configuration Overview');
    }

    // --- Sail Services ---

    public function test_sail_services_page_loads(): void
    {
        $response = $this->get('/admin/sail-services');

        $response->assertStatus(200);
        $response->assertSee('Sail Services');
    }

    public function test_can_add_sail_service(): void
    {
        $response = $this->post('/admin/sail-services', [
            'name' => 'pgsql',
        ]);

        $response->assertRedirect(route('admin.sail-services'));
        $this->assertDatabaseHas('boilerplate_sail_services', ['name' => 'pgsql', 'enabled' => true]);
    }

    public function test_can_toggle_sail_service(): void
    {
        $service = BoilerplateSailService::factory()->create(['name' => 'redis', 'enabled' => true]);

        $this->patch("/admin/sail-services/{$service->id}/toggle");

        $this->assertDatabaseHas('boilerplate_sail_services', ['id' => $service->id, 'enabled' => false]);
    }

    public function test_can_delete_sail_service(): void
    {
        $service = BoilerplateSailService::factory()->create(['name' => 'redis']);

        $this->delete("/admin/sail-services/{$service->id}");

        $this->assertDatabaseMissing('boilerplate_sail_services', ['id' => $service->id]);
    }

    // --- Files ---

    public function test_files_page_loads(): void
    {
        $response = $this->get('/admin/files');

        $response->assertStatus(200);
        $response->assertSee('Custom Files');
    }

    public function test_can_add_file(): void
    {
        $response = $this->post('/admin/files', [
            'path' => 'config/test.php',
            'content' => '<?php return [];',
        ]);

        $response->assertRedirect(route('admin.files'));
        $this->assertDatabaseHas('boilerplate_files', ['path' => 'config/test.php']);
    }

    public function test_can_update_file(): void
    {
        $file = BoilerplateFile::factory()->create(['path' => 'old.txt', 'content' => 'old']);

        $this->put("/admin/files/{$file->id}", [
            'path' => 'new.txt',
            'content' => 'new content',
            'enabled' => '1',
        ]);

        $this->assertDatabaseHas('boilerplate_files', ['id' => $file->id, 'path' => 'new.txt', 'content' => 'new content']);
    }

    public function test_can_delete_file(): void
    {
        $file = BoilerplateFile::factory()->create();

        $this->delete("/admin/files/{$file->id}");

        $this->assertDatabaseMissing('boilerplate_files', ['id' => $file->id]);
    }

    // --- Composer Packages ---

    public function test_composer_packages_page_loads(): void
    {
        $response = $this->get('/admin/composer-packages');

        $response->assertStatus(200);
        $response->assertSee('Composer Packages');
    }

    public function test_can_add_composer_package(): void
    {
        $response = $this->post('/admin/composer-packages', [
            'package' => 'laravel/telescope',
            'dev' => '1',
        ]);

        $response->assertRedirect(route('admin.composer-packages'));
        $this->assertDatabaseHas('boilerplate_composer_packages', ['package' => 'laravel/telescope', 'dev' => true]);
    }

    public function test_can_toggle_composer_package(): void
    {
        $package = BoilerplateComposerPackage::factory()->create(['enabled' => true]);

        $this->patch("/admin/composer-packages/{$package->id}/toggle");

        $this->assertDatabaseHas('boilerplate_composer_packages', ['id' => $package->id, 'enabled' => false]);
    }

    public function test_can_delete_composer_package(): void
    {
        $package = BoilerplateComposerPackage::factory()->create();

        $this->delete("/admin/composer-packages/{$package->id}");

        $this->assertDatabaseMissing('boilerplate_composer_packages', ['id' => $package->id]);
    }

    // --- npm Packages ---

    public function test_npm_packages_page_loads(): void
    {
        $response = $this->get('/admin/npm-packages');

        $response->assertStatus(200);
        $response->assertSee('npm Packages');
    }

    public function test_can_add_npm_package(): void
    {
        $response = $this->post('/admin/npm-packages', [
            'package' => 'vue@^3.0',
            'dev' => '1',
        ]);

        $response->assertRedirect(route('admin.npm-packages'));
        $this->assertDatabaseHas('boilerplate_npm_packages', ['package' => 'vue@^3.0', 'dev' => true]);
    }

    public function test_can_toggle_npm_package(): void
    {
        $package = BoilerplateNpmPackage::factory()->create(['enabled' => true]);

        $this->patch("/admin/npm-packages/{$package->id}/toggle");

        $this->assertDatabaseHas('boilerplate_npm_packages', ['id' => $package->id, 'enabled' => false]);
    }

    public function test_can_delete_npm_package(): void
    {
        $package = BoilerplateNpmPackage::factory()->create();

        $this->delete("/admin/npm-packages/{$package->id}");

        $this->assertDatabaseMissing('boilerplate_npm_packages', ['id' => $package->id]);
    }

    // --- Docker Services ---

    public function test_docker_services_page_loads(): void
    {
        $response = $this->get('/admin/docker-services');

        $response->assertStatus(200);
        $response->assertSee('Docker Services');
    }

    public function test_can_add_docker_service(): void
    {
        $response = $this->post('/admin/docker-services', [
            'name' => 'elasticsearch',
            'config' => "image: elasticsearch:8\nports:\n  - '9200:9200'",
        ]);

        $response->assertRedirect(route('admin.docker-services'));
        $this->assertDatabaseHas('boilerplate_docker_services', ['name' => 'elasticsearch']);
    }

    public function test_can_update_docker_service(): void
    {
        $service = BoilerplateDockerService::factory()->create(['name' => 'old-service']);

        $this->put("/admin/docker-services/{$service->id}", [
            'name' => 'new-service',
            'config' => "image: new:latest",
            'enabled' => '1',
        ]);

        $this->assertDatabaseHas('boilerplate_docker_services', ['id' => $service->id, 'name' => 'new-service']);
    }

    public function test_can_delete_docker_service(): void
    {
        $service = BoilerplateDockerService::factory()->create();

        $this->delete("/admin/docker-services/{$service->id}");

        $this->assertDatabaseMissing('boilerplate_docker_services', ['id' => $service->id]);
    }

    // --- Validation ---

    public function test_sail_service_requires_name(): void
    {
        $response = $this->post('/admin/sail-services', []);

        $response->assertSessionHasErrors('name');
    }

    public function test_file_requires_path(): void
    {
        $response = $this->post('/admin/files', []);

        $response->assertSessionHasErrors('path');
    }

    public function test_composer_package_requires_package_name(): void
    {
        $response = $this->post('/admin/composer-packages', []);

        $response->assertSessionHasErrors('package');
    }

    public function test_docker_service_requires_name_and_config(): void
    {
        $response = $this->post('/admin/docker-services', []);

        $response->assertSessionHasErrors(['name', 'config']);
    }
}
