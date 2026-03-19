@extends('layouts.admin')

@section('title', 'Post-Install Commands')

@section('content')
    <h1 class="text-2xl font-bold text-cyan-400 mb-6">Post-Install Commands</h1>
    <p class="text-gray-400 text-sm mb-6">Shell commands that run as the final step of the install script, after all containers are built. Failed commands become warnings and do not abort the installation.</p>

    <x-placeholder-info />

    {{-- Add new --}}
    <form action="{{ route('admin.commands.store') }}" method="POST" class="bg-gray-800 rounded-lg p-6 border border-gray-700 mb-8">
        @csrf
        <div class="space-y-4">
            <div>
                <label for="name" class="block text-sm text-gray-400 mb-1">Name</label>
                <input type="text" name="name" id="name" required placeholder="e.g. Assign ports" class="w-full bg-gray-700 border border-gray-600 rounded px-3 py-2 text-white placeholder-gray-500 focus:border-cyan-500 focus:outline-none">
            </div>
            <div>
                <label for="command" class="block text-sm text-gray-400 mb-1">Command</label>
                <textarea name="command" id="command" rows="3" required placeholder="e.g. assign_forward_instance_ports" class="w-full bg-gray-700 border border-gray-600 rounded px-3 py-2 text-white placeholder-gray-500 focus:border-cyan-500 focus:outline-none font-mono text-sm"></textarea>
            </div>
            <div>
                <label for="sort_order" class="block text-sm text-gray-400 mb-1">Sort order</label>
                <input type="number" name="sort_order" id="sort_order" value="0" class="w-32 bg-gray-700 border border-gray-600 rounded px-3 py-2 text-white focus:border-cyan-500 focus:outline-none">
            </div>
            <button type="submit" class="bg-cyan-600 hover:bg-cyan-700 text-white px-4 py-2 rounded font-medium">Add Command</button>
        </div>
    </form>

    {{-- List --}}
    <div class="bg-gray-800 rounded-lg border border-gray-700 divide-y divide-gray-700">
        @forelse ($commands as $command)
            <div class="flex items-center justify-between px-6 py-3">
                <div class="flex items-center gap-4">
                    <span class="text-gray-500 text-xs font-mono w-6">{{ $command->sort_order }}</span>
                    <a href="{{ route('admin.commands.edit', $command) }}" class="text-white hover:text-cyan-400 font-mono text-sm">{{ $command->name }}</a>
                </div>
                <div class="flex items-center gap-3">
                    <form action="{{ route('admin.commands.toggle', $command) }}" method="POST">
                        @csrf
                        @method('PATCH')
                        <button type="submit" class="px-2 py-1 rounded text-xs font-medium {{ $command->enabled ? 'bg-green-900/50 text-green-400 border border-green-700' : 'bg-gray-700 text-gray-500 border border-gray-600' }}">
                            {{ $command->enabled ? 'Enabled' : 'Disabled' }}
                        </button>
                    </form>
                </div>
            </div>
        @empty
            <div class="p-8 text-center text-gray-500">
                No post-install commands configured.
            </div>
        @endforelse
    </div>
@endsection
