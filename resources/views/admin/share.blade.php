@extends('layouts.admin')

@section('title', 'Share Configuration')

@section('content')
    <h1 class="text-2xl font-bold text-cyan-400 mb-6">Share Configuration</h1>
    <p class="text-gray-400 text-sm mb-8">Export your custom files, Docker services, and commands as a JSON file to share with colleagues, or import a configuration from someone else.</p>

    {{-- Export --}}
    <div class="bg-gray-800 rounded-lg p-6 border border-gray-700 mb-8">
        <h2 class="text-lg font-semibold text-white mb-2">Export</h2>
        <p class="text-gray-400 text-sm mb-4">Download your current configuration as a JSON file.</p>
        <a href="{{ route('admin.export') }}" class="inline-block bg-cyan-600 hover:bg-cyan-700 text-white px-4 py-2 rounded font-medium text-sm">Download JSON</a>
    </div>

    {{-- Import --}}
    <div class="bg-gray-800 rounded-lg p-6 border border-gray-700">
        <h2 class="text-lg font-semibold text-white mb-2">Import</h2>
        <p class="text-gray-400 text-sm mb-4">Upload a JSON configuration file. This will replace all existing files, Docker services, and commands.</p>

        <form action="{{ route('admin.import') }}" method="POST" enctype="multipart/form-data" class="flex items-end gap-3" onsubmit="return confirm('This will replace all existing files, Docker services, and commands. Continue?')">
            @csrf
            <div>
                <label for="config_file" class="block text-sm text-gray-400 mb-1">Configuration file</label>
                <input type="file" name="config_file" id="config_file" accept=".json" required class="text-sm text-gray-400 file:mr-3 file:py-2 file:px-4 file:rounded file:border-0 file:text-sm file:font-medium file:bg-gray-700 file:text-gray-300 hover:file:bg-gray-600 file:cursor-pointer">
            </div>
            <button type="submit" class="bg-yellow-600 hover:bg-yellow-700 text-white px-4 py-2 rounded font-medium text-sm whitespace-nowrap">Import</button>
        </form>
    </div>
@endsection
