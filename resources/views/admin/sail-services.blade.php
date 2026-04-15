@extends('layouts.admin')

@section('title', 'Sail Services')

@section('content')
    <h1 class="text-2xl font-bold text-cyan-400 mb-6">Sail Services</h1>
    <p class="text-gray-400 text-sm mb-6">Built-in Laravel Sail services. The enabled services below are the defaults — included when no <code class="text-cyan-400">with</code> parameter is specified. When <code class="text-cyan-400">with</code> is provided, only the specified services are used, overriding these defaults.</p>
    <p class="text-gray-400 text-sm mb-6">Example: <code class="text-cyan-400">curl -s "{{ url('/') }}/example-app?with=mysql,redis" | bash</code></p>

    <div class="bg-gray-800 rounded-lg border border-gray-700 divide-y divide-gray-700">
        @forelse ($services as $service)
            <div class="flex items-center justify-between px-6 py-3">
                <a href="{{ route('admin.sail-services.edit', $service) }}" class="text-white hover:text-cyan-400 font-mono text-sm">{{ $service->name }}</a>
                <div class="flex items-center gap-3">
                    @if ($service->config)
                        <span class="text-xs text-gray-500">has config</span>
                    @endif
                    <form action="{{ route('admin.sail-services.toggle', $service) }}" method="POST">
                        @csrf
                        @method('PATCH')
                        <button type="submit" class="px-2 py-1 rounded text-xs font-medium {{ $service->enabled ? 'bg-green-900/50 text-green-400 border border-green-700' : 'bg-gray-700 text-gray-500 border border-gray-600' }}">
                            {{ $service->enabled ? 'Enabled' : 'Disabled' }}
                        </button>
                    </form>
                </div>
            </div>
        @empty
            <div class="p-8 text-center text-gray-500">
                No Sail services configured.
            </div>
        @endforelse
    </div>
@endsection
