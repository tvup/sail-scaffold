@extends('layouts.admin')

@section('title', 'Custom Files')

@section('content')
    <h1 class="text-2xl font-bold text-cyan-400 mb-6">Custom Files</h1>

    <x-placeholder-info />

    {{-- Add new --}}
    <form action="{{ route('admin.files.store') }}" method="POST" class="bg-gray-800 rounded-lg p-6 border border-gray-700 mb-8">
        @csrf
        <div class="space-y-4">
            <div>
                <label for="path" class="block text-sm text-gray-400 mb-1">Path (leave content empty for directory)</label>
                <input type="text" name="path" id="path" required placeholder="e.g. config/custom.php" class="w-full bg-gray-700 border border-gray-600 rounded px-3 py-2 text-white placeholder-gray-500 focus:border-cyan-500 focus:outline-none">
            </div>
            <button type="submit" class="bg-cyan-600 hover:bg-cyan-700 text-white px-4 py-2 rounded font-medium">Add File</button>
        </div>
    </form>

    {{-- List --}}
    <div class="bg-gray-800 rounded-lg border border-gray-700 divide-y divide-gray-700">
        @forelse ($files as $file)
            <div class="flex items-center justify-between px-6 py-3">
                <a href="{{ route('admin.files.edit', $file) }}" class="text-white hover:text-cyan-400 font-mono text-sm">{{ $file->path }}</a>
                <div class="flex items-center gap-3">
                    @if ($file->content)
                        <span class="text-xs text-gray-500">has content</span>
                    @endif
                    <form action="{{ route('admin.files.toggle', $file) }}" method="POST">
                        @csrf
                        @method('PATCH')
                        <button type="submit" class="px-2 py-1 rounded text-xs font-medium {{ $file->enabled ? 'bg-green-900/50 text-green-400 border border-green-700' : 'bg-gray-700 text-gray-500 border border-gray-600' }}">
                            {{ $file->enabled ? 'Enabled' : 'Disabled' }}
                        </button>
                    </form>
                </div>
            </div>
        @empty
            <div class="p-8 text-center text-gray-500">
                No custom files configured.
            </div>
        @endforelse
    </div>
@endsection
