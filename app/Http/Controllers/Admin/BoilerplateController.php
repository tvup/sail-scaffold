<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\BoilerplateCommand;
use App\Models\BoilerplateComposerPackage;
use App\Models\BoilerplateDockerService;
use App\Models\BoilerplateFile;
use App\Models\BoilerplateNpmPackage;
use App\Models\BoilerplateSailService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class BoilerplateController extends Controller
{
    public function index(): View
    {
        return view('admin.index', [
            'sailServices' => BoilerplateSailService::query()->orderBy('name')->get(),
            'files' => BoilerplateFile::query()->orderBy('path')->get(),
            'composerPackages' => BoilerplateComposerPackage::query()->orderBy('package')->get(),
            'npmPackages' => BoilerplateNpmPackage::query()->orderBy('package')->get(),
            'dockerServices' => BoilerplateDockerService::query()->orderBy('name')->get(),
            'commands' => BoilerplateCommand::query()->orderBy('sort_order')->get(),
        ]);
    }

    // --- Sail Services ---

    public function sailServices(): View
    {
        return view('admin.sail-services', [
            'services' => BoilerplateSailService::query()->orderBy('name')->get(),
        ]);
    }

    public function storeSailService(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'config' => ['nullable', 'string'],
            'enabled' => ['boolean'],
        ]);

        BoilerplateSailService::query()->create([
            'name' => $validated['name'],
            'config' => $validated['config'] ?? null,
            'enabled' => $request->boolean('enabled', true),
        ]);

        return redirect()->route('admin.sail-services')->with('success', 'Service added.');
    }

    public function updateSailService(Request $request, BoilerplateSailService $service): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'config' => ['nullable', 'string'],
            'enabled' => ['boolean'],
        ]);

        $service->update([
            'name' => $validated['name'],
            'config' => $validated['config'] ?? null,
            'enabled' => $request->boolean('enabled'),
        ]);

        return redirect()->route('admin.sail-services')->with('success', 'Service updated.');
    }

    public function toggleSailService(BoilerplateSailService $service): RedirectResponse
    {
        $service->update(['enabled' => ! $service->enabled]);

        return redirect()->route('admin.sail-services');
    }

    public function editSailService(BoilerplateSailService $service): View
    {
        return view('admin.sail-service-edit', ['service' => $service]);
    }

    public function destroySailService(BoilerplateSailService $service): RedirectResponse
    {
        $service->delete();

        return redirect()->route('admin.sail-services')->with('success', 'Service removed.');
    }

    // --- Files ---

    public function files(): View
    {
        return view('admin.files', [
            'files' => BoilerplateFile::query()->orderBy('path')->get(),
        ]);
    }

    public function storeFile(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'path' => ['required', 'string', 'max:500'],
            'content' => ['nullable', 'string'],
            'enabled' => ['boolean'],
        ]);

        BoilerplateFile::query()->create([
            'path' => $validated['path'],
            'content' => $validated['content'] ?? null,
            'enabled' => $request->boolean('enabled', true),
        ]);

        return redirect()->route('admin.files')->with('success', 'File added.');
    }

    public function updateFile(Request $request, BoilerplateFile $file): RedirectResponse
    {
        $validated = $request->validate([
            'path' => ['required', 'string', 'max:500'],
            'content' => ['nullable', 'string'],
            'enabled' => ['boolean'],
        ]);

        $file->update([
            'path' => $validated['path'],
            'content' => $validated['content'] ?? null,
            'enabled' => $request->boolean('enabled'),
        ]);

        return redirect()->route('admin.files')->with('success', 'File updated.');
    }

    public function editFile(BoilerplateFile $file): View
    {
        return view('admin.file-edit', ['file' => $file]);
    }

    public function toggleFile(BoilerplateFile $file): RedirectResponse
    {
        $file->update(['enabled' => ! $file->enabled]);

        return redirect()->route('admin.files');
    }

    public function destroyFile(BoilerplateFile $file): RedirectResponse
    {
        $file->delete();

        return redirect()->route('admin.files')->with('success', 'File removed.');
    }

    // --- Composer Packages ---

    public function composerPackages(): View
    {
        return view('admin.composer-packages', [
            'packages' => BoilerplateComposerPackage::query()->orderBy('package')->get(),
        ]);
    }

    public function storeComposerPackage(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'package' => ['required', 'string', 'max:255'],
            'dev' => ['boolean'],
            'enabled' => ['boolean'],
        ]);

        BoilerplateComposerPackage::query()->create([
            'package' => $validated['package'],
            'dev' => $request->boolean('dev'),
            'enabled' => $request->boolean('enabled', true),
        ]);

        return redirect()->route('admin.composer-packages')->with('success', 'Package added.');
    }

    public function toggleComposerPackage(BoilerplateComposerPackage $package): RedirectResponse
    {
        $package->update(['enabled' => ! $package->enabled]);

        return redirect()->route('admin.composer-packages');
    }

    public function destroyComposerPackage(BoilerplateComposerPackage $package): RedirectResponse
    {
        $package->delete();

        return redirect()->route('admin.composer-packages')->with('success', 'Package removed.');
    }

    // --- npm Packages ---

    public function npmPackages(): View
    {
        return view('admin.npm-packages', [
            'packages' => BoilerplateNpmPackage::query()->orderBy('package')->get(),
        ]);
    }

    public function storeNpmPackage(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'package' => ['required', 'string', 'max:255'],
            'dev' => ['boolean'],
            'enabled' => ['boolean'],
        ]);

        BoilerplateNpmPackage::query()->create([
            'package' => $validated['package'],
            'dev' => $request->boolean('dev'),
            'enabled' => $request->boolean('enabled', true),
        ]);

        return redirect()->route('admin.npm-packages')->with('success', 'Package added.');
    }

    public function toggleNpmPackage(BoilerplateNpmPackage $package): RedirectResponse
    {
        $package->update(['enabled' => ! $package->enabled]);

        return redirect()->route('admin.npm-packages');
    }

    public function destroyNpmPackage(BoilerplateNpmPackage $package): RedirectResponse
    {
        $package->delete();

        return redirect()->route('admin.npm-packages')->with('success', 'Package removed.');
    }

    // --- Docker Services ---

    public function dockerServices(): View
    {
        return view('admin.docker-services', [
            'services' => BoilerplateDockerService::query()->orderBy('name')->get(),
        ]);
    }

    public function storeDockerService(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'config' => ['required', 'string'],
            'enabled' => ['boolean'],
        ]);

        BoilerplateDockerService::query()->create([
            'name' => $validated['name'],
            'config' => $validated['config'],
            'enabled' => $request->boolean('enabled', true),
        ]);

        return redirect()->route('admin.docker-services')->with('success', 'Docker service added.');
    }

    public function updateDockerService(Request $request, BoilerplateDockerService $service): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'config' => ['required', 'string'],
            'enabled' => ['boolean'],
        ]);

        $service->update([
            'name' => $validated['name'],
            'config' => $validated['config'],
            'enabled' => $request->boolean('enabled'),
        ]);

        return redirect()->route('admin.docker-services')->with('success', 'Docker service updated.');
    }

    public function editDockerService(BoilerplateDockerService $service): View
    {
        return view('admin.docker-service-edit', ['service' => $service]);
    }

    public function toggleDockerService(BoilerplateDockerService $service): RedirectResponse
    {
        $service->update(['enabled' => ! $service->enabled]);

        return redirect()->route('admin.docker-services');
    }

    public function destroyDockerService(BoilerplateDockerService $service): RedirectResponse
    {
        $service->delete();

        return redirect()->route('admin.docker-services')->with('success', 'Docker service removed.');
    }

    // --- Post-Install Commands ---

    public function commands(): View
    {
        return view('admin.commands', [
            'commands' => BoilerplateCommand::query()->orderBy('sort_order')->get(),
        ]);
    }

    public function storeCommand(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'command' => ['required', 'string'],
            'sort_order' => ['integer'],
            'enabled' => ['boolean'],
        ]);

        BoilerplateCommand::query()->create([
            'name' => $validated['name'],
            'command' => $validated['command'],
            'sort_order' => $validated['sort_order'] ?? 0,
            'enabled' => $request->boolean('enabled', true),
        ]);

        return redirect()->route('admin.commands')->with('success', 'Command added.');
    }

    public function editCommand(BoilerplateCommand $command): View
    {
        return view('admin.command-edit', ['command' => $command]);
    }

    public function updateCommand(Request $request, BoilerplateCommand $command): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'command' => ['required', 'string'],
            'sort_order' => ['integer'],
            'enabled' => ['boolean'],
        ]);

        $command->update([
            'name' => $validated['name'],
            'command' => $validated['command'],
            'sort_order' => $validated['sort_order'] ?? 0,
            'enabled' => $request->boolean('enabled'),
        ]);

        return redirect()->route('admin.commands')->with('success', 'Command updated.');
    }

    public function toggleCommand(BoilerplateCommand $command): RedirectResponse
    {
        $command->update(['enabled' => ! $command->enabled]);

        return redirect()->route('admin.commands');
    }

    public function destroyCommand(BoilerplateCommand $command): RedirectResponse
    {
        $command->delete();

        return redirect()->route('admin.commands')->with('success', 'Command removed.');
    }
}
