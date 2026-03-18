@extends('layouts.admin')

@section('title', 'Edit Sail Service — ' . $service->name)

@section('content')
    <div class="mb-4">
        <a href="{{ route('admin.sail-services') }}" class="text-gray-400 hover:text-cyan-400 text-sm">&larr; Back to Sail Services</a>
    </div>

    <h1 class="text-2xl font-bold text-cyan-400 mb-6">Edit: {{ $service->name }}</h1>

    <form action="{{ route('admin.sail-services.update', $service) }}" method="POST" class="bg-gray-800 rounded-lg p-6 border border-gray-700 space-y-4">
        @csrf
        @method('PUT')
        <div>
            <label for="name" class="block text-sm text-gray-400 mb-1">Service name</label>
            <input type="text" name="name" id="name" value="{{ $service->name }}" required class="w-full bg-gray-700 border border-gray-600 rounded px-3 py-2 text-white focus:border-cyan-500 focus:outline-none font-mono text-sm">
        </div>
        <div>
            <label for="config" class="block text-sm text-gray-400 mb-1">Config override (optional, YAML for compose.override.yml)</label>
            <textarea name="config" id="config" rows="10" placeholder="        ports:&#10;            - '3307:3306'" class="w-full bg-gray-700 border border-gray-600 rounded px-3 py-2 text-white placeholder-gray-500 focus:border-cyan-500 focus:outline-none font-mono text-sm">{{ $service->config }}</textarea>
        </div>
        <div>
            <label class="flex items-center gap-2 text-sm">
                <input type="hidden" name="enabled" value="0">
                <input type="checkbox" name="enabled" value="1" {{ $service->enabled ? 'checked' : '' }} class="rounded">
                <span class="text-gray-400">Enabled</span>
            </label>
        </div>
        <div class="flex items-center justify-between pt-2">
            <button type="submit" class="bg-cyan-600 hover:bg-cyan-700 text-white px-4 py-2 rounded font-medium">Save</button>
        </div>
    </form>

    <form action="{{ route('admin.sail-services.destroy', $service) }}" method="POST" class="mt-4" onsubmit="return confirm('Delete this service?')">
        @csrf
        @method('DELETE')
        <button type="submit" class="text-red-400 hover:text-red-300 text-sm">Delete this service</button>
    </form>
@endsection
