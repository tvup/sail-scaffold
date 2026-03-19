@extends('layouts.admin')

@section('title', 'Boilerplate Overview')

@section('content')
    <h1 class="text-2xl font-bold text-cyan-400 mb-8">Configuration Overview</h1>

    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        <a href="{{ route('admin.sail-services') }}" class="block bg-gray-800 rounded-lg p-6 border border-gray-700 hover:border-cyan-500 transition-colors">
            <h2 class="text-lg font-semibold text-white mb-2">Sail Services</h2>
            <p class="text-gray-400 text-sm mb-4">Default services included in new projects</p>
            <span class="text-cyan-400 font-mono text-2xl">{{ $sailServices->where('enabled', true)->count() }}</span>
            <span class="text-gray-500 text-sm"> / {{ $sailServices->count() }} active</span>
        </a>

        <a href="{{ route('admin.files') }}" class="block bg-gray-800 rounded-lg p-6 border border-gray-700 hover:border-cyan-500 transition-colors">
            <h2 class="text-lg font-semibold text-white mb-2">Custom Files</h2>
            <p class="text-gray-400 text-sm mb-4">Files and directories added to new projects</p>
            <span class="text-cyan-400 font-mono text-2xl">{{ $files->where('enabled', true)->count() }}</span>
            <span class="text-gray-500 text-sm"> / {{ $files->count() }} active</span>
        </a>

        <a href="{{ route('admin.composer-packages') }}" class="block bg-gray-800 rounded-lg p-6 border border-gray-700 hover:border-cyan-500 transition-colors">
            <h2 class="text-lg font-semibold text-white mb-2">Composer Packages</h2>
            <p class="text-gray-400 text-sm mb-4">PHP packages installed in new projects</p>
            <span class="text-cyan-400 font-mono text-2xl">{{ $composerPackages->where('enabled', true)->count() }}</span>
            <span class="text-gray-500 text-sm"> / {{ $composerPackages->count() }} active</span>
        </a>

        <a href="{{ route('admin.npm-packages') }}" class="block bg-gray-800 rounded-lg p-6 border border-gray-700 hover:border-cyan-500 transition-colors">
            <h2 class="text-lg font-semibold text-white mb-2">npm Packages</h2>
            <p class="text-gray-400 text-sm mb-4">JavaScript packages installed in new projects</p>
            <span class="text-cyan-400 font-mono text-2xl">{{ $npmPackages->where('enabled', true)->count() }}</span>
            <span class="text-gray-500 text-sm"> / {{ $npmPackages->count() }} active</span>
        </a>

        <a href="{{ route('admin.docker-services') }}" class="block bg-gray-800 rounded-lg p-6 border border-gray-700 hover:border-cyan-500 transition-colors">
            <h2 class="text-lg font-semibold text-white mb-2">Docker Services</h2>
            <p class="text-gray-400 text-sm mb-4">Extra Docker Compose services</p>
            <span class="text-cyan-400 font-mono text-2xl">{{ $dockerServices->where('enabled', true)->count() }}</span>
            <span class="text-gray-500 text-sm"> / {{ $dockerServices->count() }} active</span>
        </a>

        <a href="{{ route('admin.commands') }}" class="block bg-gray-800 rounded-lg p-6 border border-gray-700 hover:border-cyan-500 transition-colors">
            <h2 class="text-lg font-semibold text-white mb-2">Post-Install Commands</h2>
            <p class="text-gray-400 text-sm mb-4">Shell commands run after installation</p>
            <span class="text-cyan-400 font-mono text-2xl">{{ $commands->where('enabled', true)->count() }}</span>
            <span class="text-gray-500 text-sm"> / {{ $commands->count() }} active</span>
        </a>
    </div>
@endsection
