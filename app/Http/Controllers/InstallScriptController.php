<?php

namespace App\Http\Controllers;

use App\Models\BoilerplateCommand;
use App\Models\BoilerplateComposerPackage;
use App\Models\BoilerplateDockerService;
use App\Models\BoilerplateFile;
use App\Models\BoilerplateNpmPackage;
use App\Models\BoilerplateSailService;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class InstallScriptController extends Controller
{
    private const VALID_SERVICES = [
        'mysql', 'pgsql', 'mariadb', 'redis', 'memcached',
        'meilisearch', 'typesense', 'minio', 'mailpit',
        'selenium', 'soketi', 'valkey',
    ];

    public function __invoke(Request $request, string $appName): Response
    {
        if (! preg_match('/^[a-zA-Z0-9][a-zA-Z0-9\-]*$/', $appName)) {
            abort(400, 'Invalid application name.');
        }

        if ($request->query('with')) {
            $services = explode(',', $request->query('with'));
            $services = array_values(array_filter($services, fn (string $s): bool => in_array($s, self::VALID_SERVICES, true)));
        } else {
            $services = BoilerplateSailService::query()
                ->where('enabled', true)
                ->pluck('name')
                ->toArray();
        }

        $composerPackages = BoilerplateComposerPackage::query()
            ->where('enabled', true)
            ->get();

        $npmPackages = BoilerplateNpmPackage::query()
            ->where('enabled', true)
            ->get();

        $files = BoilerplateFile::query()
            ->where('enabled', true)
            ->get();

        $dockerServices = BoilerplateDockerService::query()
            ->where('enabled', true)
            ->get();

        $sailServiceOverrides = BoilerplateSailService::query()
            ->where('enabled', true)
            ->whereNotNull('config')
            ->where('config', '!=', '')
            ->get();

        $commands = BoilerplateCommand::query()
            ->where('enabled', true)
            ->orderBy('sort_order')
            ->get();

        $hasVitePlugin = $npmPackages->contains(fn ($pkg): bool => $pkg->package === '@tailwindcss/vite');

        $servicesString = implode(',', $services);

        $runtimeContext = [
            'appName' => $appName,
            'servicesString' => $servicesString,
        ];

        $placeholders = collect(config('boilerplate.placeholders'))
            ->mapWithKeys(fn (array $def, string $key): array => [
                $key => $runtimeContext[$def['resolve']] ?? '',
            ])
            ->all();

        $script = view('install', [
            'appName' => $appName,
            'services' => $services,
            'servicesString' => $servicesString,
            'composerPackages' => $composerPackages,
            'npmPackages' => $npmPackages,
            'files' => $files,
            'dockerServices' => $dockerServices,
            'sailServiceOverrides' => $sailServiceOverrides,
            'hasVitePlugin' => $hasVitePlugin,
            'placeholders' => $placeholders,
            'commands' => $commands,
        ])->render();

        return response($script, 200, [
            'Content-Type' => 'text/plain',
        ]);
    }
}
