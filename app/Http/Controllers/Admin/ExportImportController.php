<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\BoilerplateCommand;
use App\Models\BoilerplateDockerService;
use App\Models\BoilerplateFile;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class ExportImportController extends Controller
{
    public function index(): View
    {
        return view('admin.share');
    }

    public function export(): JsonResponse
    {
        $data = [
            'sail_scaffold_version' => '1.0',
            'exported_at' => now()->toIso8601String(),
            'files' => BoilerplateFile::query()
                ->orderBy('path')
                ->get(['path', 'content', 'enabled']),
            'docker_services' => BoilerplateDockerService::query()
                ->orderBy('name')
                ->get(['name', 'config', 'enabled']),
            'commands' => BoilerplateCommand::query()
                ->orderBy('sort_order')
                ->get(['name', 'command', 'sort_order', 'enabled']),
        ];

        return response()
            ->json($data, options: JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE)
            ->header('Content-Disposition', 'attachment; filename=sail-scaffold-config.json');
    }

    public function import(Request $request): RedirectResponse
    {
        $request->validate([
            'config_file' => ['required', 'file', 'mimes:json', 'max:1024'],
        ]);

        $json = json_decode($request->file('config_file')->getContent(), true);

        if (! is_array($json)) {
            return back()->withErrors(['config_file' => 'Invalid JSON file.']);
        }

        DB::transaction(function () use ($json) {
            if (isset($json['files']) && is_array($json['files'])) {
                BoilerplateFile::query()->truncate();
                foreach ($json['files'] as $file) {
                    BoilerplateFile::query()->create([
                        'path' => $file['path'],
                        'content' => $file['content'] ?? null,
                        'enabled' => $file['enabled'] ?? true,
                    ]);
                }
            }

            if (isset($json['docker_services']) && is_array($json['docker_services'])) {
                BoilerplateDockerService::query()->truncate();
                foreach ($json['docker_services'] as $service) {
                    BoilerplateDockerService::query()->create([
                        'name' => $service['name'],
                        'config' => $service['config'],
                        'enabled' => $service['enabled'] ?? true,
                    ]);
                }
            }

            if (isset($json['commands']) && is_array($json['commands'])) {
                BoilerplateCommand::query()->truncate();
                foreach ($json['commands'] as $command) {
                    BoilerplateCommand::query()->create([
                        'name' => $command['name'],
                        'command' => $command['command'],
                        'sort_order' => $command['sort_order'] ?? 0,
                        'enabled' => $command['enabled'] ?? true,
                    ]);
                }
            }
        });

        return redirect()->route('admin.index')->with('success', 'Configuration imported successfully.');
    }
}
