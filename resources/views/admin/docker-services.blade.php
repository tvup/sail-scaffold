@extends('layouts.admin')

@section('title', 'Docker Services')

@section('content')
    <h1 class="text-2xl font-bold text-cyan-400 mb-6">Docker Services</h1>
    <p class="text-gray-400 text-sm mb-6">Custom Docker services that Sail does not support out of the box. These are appended directly to <code class="text-cyan-400">compose.yml</code> as raw YAML service definitions. Use this for things like Elasticsearch, RabbitMQ, or any other container your project needs.</p>

    {{-- Add new --}}
    <form action="{{ route('admin.docker-services.store') }}" method="POST" class="bg-gray-800 rounded-lg p-6 border border-gray-700 mb-8">
        @csrf
        <div class="space-y-4">
            <div>
                <label for="name" class="block text-sm text-gray-400 mb-1">Service name</label>
                <input type="text" name="name" id="name" required placeholder="e.g. elasticsearch" class="w-full bg-gray-700 border border-gray-600 rounded px-3 py-2 text-white placeholder-gray-500 focus:border-cyan-500 focus:outline-none">
            </div>
            <div>
                <label for="config" class="block text-sm text-gray-400 mb-1">YAML config (docker-compose format)</label>
                <textarea name="config" id="config" rows="6" required placeholder="        image: elasticsearch:8.x&#10;        ports:&#10;            - '9200:9200'" class="w-full bg-gray-700 border border-gray-600 rounded px-3 py-2 text-white placeholder-gray-500 focus:border-cyan-500 focus:outline-none font-mono text-sm"></textarea>
            </div>
            <button type="submit" class="bg-cyan-600 hover:bg-cyan-700 text-white px-4 py-2 rounded font-medium">Add Docker Service</button>
        </div>
    </form>

    {{-- List --}}
    <div class="bg-gray-800 rounded-lg border border-gray-700 divide-y divide-gray-700">
        @forelse ($services as $service)
            <div class="flex items-center justify-between px-6 py-3">
                <a href="{{ route('admin.docker-services.edit', $service) }}" class="text-white hover:text-cyan-400 font-mono text-sm">{{ $service->name }}</a>
                <div class="flex items-center gap-3">
                    <form action="{{ route('admin.docker-services.toggle', $service) }}" method="POST">
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
                No custom Docker services configured.
            </div>
        @endforelse
    </div>
@endsection
