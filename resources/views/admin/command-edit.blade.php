@extends('layouts.admin')

@section('title', 'Edit Command — ' . $command->name)

@section('content')
    <div class="mb-4">
        <a href="{{ route('admin.commands') }}" class="text-gray-400 hover:text-cyan-400 text-sm">&larr; Back to Commands</a>
    </div>

    <h1 class="text-2xl font-bold text-cyan-400 mb-6">Edit: {{ $command->name }}</h1>

    <x-placeholder-info />

    <form action="{{ route('admin.commands.update', $command) }}" method="POST" class="bg-gray-800 rounded-lg p-6 border border-gray-700 space-y-4">
        @csrf
        @method('PUT')
        <div>
            <label for="name" class="block text-sm text-gray-400 mb-1">Name</label>
            <input type="text" name="name" id="name" value="{{ $command->name }}" required class="w-full bg-gray-700 border border-gray-600 rounded px-3 py-2 text-white focus:border-cyan-500 focus:outline-none font-mono text-sm">
        </div>
        <div>
            <label for="command" class="block text-sm text-gray-400 mb-1">Command</label>
            <textarea name="command" id="command" rows="6" required class="w-full bg-gray-700 border border-gray-600 rounded px-3 py-2 text-white placeholder-gray-500 focus:border-cyan-500 focus:outline-none font-mono text-sm">{{ $command->command }}</textarea>
        </div>
        <div>
            <label for="sort_order" class="block text-sm text-gray-400 mb-1">Sort order</label>
            <input type="number" name="sort_order" id="sort_order" value="{{ $command->sort_order }}" class="w-32 bg-gray-700 border border-gray-600 rounded px-3 py-2 text-white focus:border-cyan-500 focus:outline-none">
        </div>
        <div>
            <label class="flex items-center gap-2 text-sm">
                <input type="hidden" name="enabled" value="0">
                <input type="checkbox" name="enabled" value="1" {{ $command->enabled ? 'checked' : '' }} class="rounded">
                <span class="text-gray-400">Enabled</span>
            </label>
        </div>
        <div class="flex items-center justify-between pt-2">
            <button type="submit" class="bg-cyan-600 hover:bg-cyan-700 text-white px-4 py-2 rounded font-medium">Save</button>
        </div>
    </form>

    <form action="{{ route('admin.commands.destroy', $command) }}" method="POST" class="mt-4" onsubmit="return confirm('Delete this command?')">
        @csrf
        @method('DELETE')
        <button type="submit" class="text-red-400 hover:text-red-300 text-sm">Delete this command</button>
    </form>
@endsection
