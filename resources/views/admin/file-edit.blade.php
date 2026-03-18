@extends('layouts.admin')

@section('title', 'Edit File — ' . $file->path)

@section('content')
    <div class="mb-4">
        <a href="{{ route('admin.files') }}" class="text-gray-400 hover:text-cyan-400 text-sm">&larr; Back to Files</a>
    </div>

    <h1 class="text-2xl font-bold text-cyan-400 mb-6">Edit: {{ $file->path }}</h1>

    <form action="{{ route('admin.files.update', $file) }}" method="POST" class="bg-gray-800 rounded-lg p-6 border border-gray-700 space-y-4">
        @csrf
        @method('PUT')
        <div>
            <label for="path" class="block text-sm text-gray-400 mb-1">Path</label>
            <input type="text" name="path" id="path" value="{{ $file->path }}" required class="w-full bg-gray-700 border border-gray-600 rounded px-3 py-2 text-white focus:border-cyan-500 focus:outline-none font-mono text-sm">
        </div>
        <div>
            <label for="content" class="block text-sm text-gray-400 mb-1">Content (leave empty for directory)</label>
            <textarea name="content" id="content" rows="12" class="w-full bg-gray-700 border border-gray-600 rounded px-3 py-2 text-white placeholder-gray-500 focus:border-cyan-500 focus:outline-none font-mono text-sm">{{ $file->content }}</textarea>
            <div class="text-gray-500 text-xs mt-1">
                Available placeholders:
                @foreach (config('boilerplate.placeholders') as $key => $def)
                    <div><code class="text-cyan-400">{{ $key }}</code> — {{ $def['description'] }}</div>
                @endforeach
            </div>
        </div>
        <div>
            <label class="flex items-center gap-2 text-sm">
                <input type="hidden" name="enabled" value="0">
                <input type="checkbox" name="enabled" value="1" {{ $file->enabled ? 'checked' : '' }} class="rounded">
                <span class="text-gray-400">Enabled</span>
            </label>
        </div>
        <div class="flex items-center justify-between pt-2">
            <button type="submit" class="bg-cyan-600 hover:bg-cyan-700 text-white px-4 py-2 rounded font-medium">Save</button>
        </div>
    </form>

    <form action="{{ route('admin.files.destroy', $file) }}" method="POST" class="mt-4" onsubmit="return confirm('Delete this file?')">
        @csrf
        @method('DELETE')
        <button type="submit" class="text-red-400 hover:text-red-300 text-sm">Delete this file</button>
    </form>
@endsection
