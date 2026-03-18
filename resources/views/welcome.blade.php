<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sail Scaffold</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-900 text-gray-100 min-h-screen">
    <div class="max-w-3xl mx-auto px-4 py-16">
        <h1 class="text-4xl font-bold text-cyan-400 mb-2">Sail Scaffold</h1>
        <p class="text-gray-400 mb-12">Scaffold a new Laravel project with pre-configured Sail services, packages, and files.</p>

        {{-- Quick start --}}
        <section class="mb-12">
            <h2 class="text-xl font-semibold text-white mb-4">Quick Start</h2>
            <div class="bg-gray-800 rounded-lg p-4 border border-gray-700">
                <code class="text-cyan-400 text-sm break-all">curl -s "{{ url('/') }}/example-app" | bash</code>
            </div>
            <p class="text-gray-500 text-sm mt-2">Replace <code class="text-gray-400">example-app</code> with your desired project name.</p>
        </section>

        {{-- With services --}}
        <section class="mb-12">
            <h2 class="text-xl font-semibold text-white mb-4">Choosing Services</h2>
            <p class="text-gray-400 text-sm mb-4">Use the <code class="text-cyan-400">with</code> query parameter to select which Sail services to include in your project's <code class="text-cyan-400">compose.yml</code>:</p>
            <div class="bg-gray-800 rounded-lg p-4 border border-gray-700 space-y-2">
                <div>
                    <span class="text-gray-500 text-xs">With MySQL and Redis:</span>
                    <code class="block text-cyan-400 text-sm break-all">curl -s "{{ url('/') }}/my-app?with=mysql,redis" | bash</code>
                </div>
                <div class="border-t border-gray-700 pt-2">
                    <span class="text-gray-500 text-xs">With PostgreSQL, Redis, and Mailpit:</span>
                    <code class="block text-cyan-400 text-sm break-all">curl -s "{{ url('/') }}/my-app?with=pgsql,redis,mailpit" | bash</code>
                </div>
            </div>
            <p class="text-gray-500 text-sm mt-2">When <code class="text-gray-400">with</code> is specified, only the listed services are used — the defaults below are ignored. Without <code class="text-gray-400">with</code>, the default services are included automatically.</p>
        </section>

        {{-- Available services --}}
        <section class="mb-12">
            <h2 class="text-xl font-semibold text-white mb-4">Available Services</h2>
            <p class="text-gray-400 text-sm mb-4">Services marked as default are included when no <code class="text-cyan-400">with</code> parameter is specified. Use <code class="text-cyan-400">with</code> to override the selection entirely.</p>
            <div class="bg-gray-800 rounded-lg border border-gray-700 divide-y divide-gray-700">
                @foreach ($services as $service)
                    <div class="flex items-center justify-between px-4 py-2">
                        <code class="text-sm font-mono {{ $service->enabled ? 'text-white' : 'text-gray-500' }}">{{ $service->name }}</code>
                        @if ($service->enabled)
                            <span class="text-xs text-green-400 bg-green-900/50 border border-green-700 px-2 py-0.5 rounded">Default</span>
                        @else
                            <span class="text-xs text-gray-500">Optional</span>
                        @endif
                    </div>
                @endforeach
            </div>
        </section>

        {{-- Admin link --}}
        <div class="text-center">
            <a href="{{ route('admin.index') }}" class="text-cyan-400 hover:text-cyan-300 text-sm">Admin Panel &rarr;</a>
        </div>
    </div>
</body>
</html>
