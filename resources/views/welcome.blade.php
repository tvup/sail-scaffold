<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ config('app.name') }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-gray-900 text-gray-100 min-h-screen">
    <div class="max-w-3xl mx-auto px-4 py-16" x-data="{
        projectName: 'example-app',
        baseUrl: '{{ url('/') }}',
        copied: null,
        services: [
            @foreach ($services as $service)
                { name: '{{ $service->name }}', defaultEnabled: {{ $service->enabled ? 'true' : 'false' }}, state: 'default', type: 'sail' },
            @endforeach
            @foreach ($dockerServices as $service)
                { name: '{{ $service->name }}', defaultEnabled: {{ $service->enabled ? 'true' : 'false' }}, state: 'default', type: 'docker' },
            @endforeach
        ],
        cycleState(service) {
            if (service.state === 'default') {
                service.state = service.defaultEnabled ? 'excluded' : 'included';
            } else if (service.state === 'included') {
                service.state = service.defaultEnabled ? 'default' : 'excluded';
            } else {
                service.state = service.defaultEnabled ? 'included' : 'default';
            }
        },
        sanitize(str) {
            return str.replace(/[^a-zA-Z0-9\-_]/g, '');
        },
        get withParam() {
            const included = this.services.filter(s => s.state === 'included' || (s.state === 'default' && s.defaultEnabled)).map(s => s.name);
            return included.length ? 'with=' + included.join(',') : '';
        },
        get generatedCommand() {
            const name = this.sanitize(this.projectName) || 'example-app';
            const qs = this.withParam ? '?' + this.withParam : '';
            return 'curl -s &quot;' + this.baseUrl + '/' + name + qs + '&quot; | bash';
        },
        get generatedRawCommand() {
            const name = this.sanitize(this.projectName) || 'example-app';
            const qs = this.withParam ? '?' + this.withParam : '';
            const q = String.fromCharCode(34);
            return 'curl -s ' + q + this.baseUrl + '/' + name + qs + q + ' | bash';
        },
        copyGenerated() {
            navigator.clipboard.writeText(this.generatedRawCommand);
            this.copied = 'generated';
            setTimeout(() => this.copied = null, 2000);
        }
    }">
        <h1 class="text-4xl font-bold text-cyan-400 mb-2">Sail Scaffold</h1>
        <p class="text-gray-400 mb-12">Scaffold a new Laravel project with pre-configured Sail services, packages, and files.</p>

        {{-- Quick start --}}
        <section class="mb-12">
            <h2 class="text-xl font-semibold text-white mb-4">Quick Start with Defaults</h2>
            <div class="mb-4 flex items-center gap-3">
                <label for="project-name" class="shrink-0 text-gray-400 text-sm">Enter project name</label>
                <input
                    id="project-name"
                    type="text"
                    x-model="projectName"
                    x-init="$nextTick(() => { $el.focus(); $el.select(); })"
                    placeholder="example-app"
                    class="w-48 bg-gray-800 border border-gray-700 rounded-lg px-4 py-2 text-white text-sm focus:outline-none focus:border-cyan-400"
                />
            </div>
            <div class="bg-gray-800 rounded-lg p-4 border border-gray-700 flex items-center justify-between gap-2">
                <code class="text-cyan-400 text-sm break-all" x-html="'curl -s &quot;' + baseUrl + '/' + (sanitize(projectName) || 'example-app') + '&quot; | bash'"></code>
                <button
                    @click="navigator.clipboard.writeText('curl -s ' + String.fromCharCode(34) + baseUrl + '/' + (sanitize(projectName) || 'example-app') + String.fromCharCode(34) + ' | bash'); copied = 'quick'; setTimeout(() => copied = null, 2000)"
                    class="shrink-0 text-gray-400 hover:text-cyan-400 transition-colors"
                    :title="copied === 'quick' ? 'Copied!' : 'Copy to clipboard'"
                >
                    <svg x-show="copied !== 'quick'" xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z" />
                    </svg>
                    <svg x-show="copied === 'quick'" xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-green-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" />
                    </svg>
                </button>
            </div>
            <p class="text-gray-500 text-sm mt-2">This command uses the default service selection. Customize services below to generate a tailored command.</p>
        </section>

        {{-- Available services --}}
        <section class="mb-12">
            <h2 class="text-xl font-semibold text-white mb-4">Choosing Your Services</h2>
            <p class="text-gray-400 text-sm mb-4">Click the service badges to customize which services are included. The command below updates automatically.</p>
            <div class="bg-gray-800 rounded-lg border border-gray-700 divide-y divide-gray-700">
                <template x-for="(service, index) in services.filter(s => s.type === 'sail')" :key="service.name">
                    <div class="flex items-center justify-between px-4 py-2">
                        <code class="text-sm font-mono" :class="service.state === 'excluded' || (service.state === 'default' && !service.defaultEnabled) ? 'text-gray-500' : 'text-white'" x-text="service.name"></code>
                        <button @click="cycleState(service)" class="text-xs cursor-pointer transition-colors">
                            <span x-show="service.state === 'default' && service.defaultEnabled" class="text-green-400 bg-green-900/50 border border-green-700 px-2 py-0.5 rounded">Included (default)</span>
                            <span x-show="service.state === 'default' && !service.defaultEnabled" class="text-red-400 bg-red-900/50 border border-red-700 px-2 py-0.5 rounded">Excluded (default)</span>
                            <span x-show="service.state === 'included'" class="text-green-400 bg-green-900/50 border border-green-700 px-2 py-0.5 rounded">Included</span>
                            <span x-show="service.state === 'excluded'" class="text-red-400 bg-red-900/50 border border-red-700 px-2 py-0.5 rounded">Excluded</span>
                        </button>
                    </div>
                </template>
                <template x-if="services.some(s => s.type === 'docker')">
                    <div>
                        <div class="px-4 py-2 border-t-2 border-gray-600">
                            <span class="text-xs text-gray-500 italic">Other Docker services:</span>
                        </div>
                        <template x-for="service in services.filter(s => s.type === 'docker')" :key="service.name">
                            <div class="flex items-center justify-between px-4 py-1.5 border-t border-gray-700">
                                <code class="text-sm font-mono" :class="service.state === 'excluded' || (service.state === 'default' && !service.defaultEnabled) ? 'text-gray-500' : 'text-white'" x-text="service.name"></code>
                                <button @click="cycleState(service)" class="text-xs cursor-pointer transition-colors">
                                    <span x-show="service.state === 'default' && service.defaultEnabled" class="text-green-400 bg-green-900/50 border border-green-700 px-2 py-0.5 rounded">Included (default)</span>
                                    <span x-show="service.state === 'default' && !service.defaultEnabled" class="text-red-400 bg-red-900/50 border border-red-700 px-2 py-0.5 rounded">Excluded (default)</span>
                                    <span x-show="service.state === 'included'" class="text-green-400 bg-green-900/50 border border-green-700 px-2 py-0.5 rounded">Included</span>
                                    <span x-show="service.state === 'excluded'" class="text-red-400 bg-red-900/50 border border-red-700 px-2 py-0.5 rounded">Excluded</span>
                                </button>
                            </div>
                        </template>
                    </div>
                </template>
            </div>

            {{-- Generated command based on selection --}}
            <div class="mt-4 bg-gray-800 rounded-lg p-4 border border-gray-700 flex items-center justify-between gap-2">
                <code class="text-cyan-400 text-sm break-all" x-html="generatedCommand"></code>
                <button
                    @click="copyGenerated()"
                    class="shrink-0 text-gray-400 hover:text-cyan-400 transition-colors"
                    :title="copied === 'generated' ? 'Copied!' : 'Copy to clipboard'"
                >
                    <svg x-show="copied !== 'generated'" xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z" />
                    </svg>
                    <svg x-show="copied === 'generated'" xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-green-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" />
                    </svg>
                </button>
            </div>
        </section>

        {{-- Admin link --}}
        <div class="text-center">
            <a href="{{ route('admin.index') }}" class="text-cyan-400 hover:text-cyan-300 text-sm">Admin Panel &rarr;</a>
        </div>
    </div>
</body>
</html>
