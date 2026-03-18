@extends('layouts.admin')

@section('title', 'npm Packages')

@section('content')
    <h1 class="text-2xl font-bold text-cyan-400 mb-6">npm Packages</h1>

    {{-- Add new --}}
    <form action="{{ route('admin.npm-packages.store') }}" method="POST" class="bg-gray-800 rounded-lg p-6 border border-gray-700 mb-8">
        @csrf
        <div class="flex items-end gap-4">
            <div class="flex-1">
                <label for="package" class="block text-sm text-gray-400 mb-1">Package</label>
                <input type="text" name="package" id="package" required placeholder="e.g. vue@^3.0" class="w-full bg-gray-700 border border-gray-600 rounded px-3 py-2 text-white placeholder-gray-500 focus:border-cyan-500 focus:outline-none">
            </div>
            <label class="flex items-center gap-2 text-sm pb-2">
                <input type="checkbox" name="dev" value="1" checked class="rounded">
                <span class="text-gray-400">Dev dependency</span>
            </label>
            <button type="submit" class="bg-cyan-600 hover:bg-cyan-700 text-white px-4 py-2 rounded font-medium">Add</button>
        </div>
    </form>

    {{-- List --}}
    <div class="bg-gray-800 rounded-lg border border-gray-700 overflow-hidden">
        <table class="w-full">
            <thead>
                <tr>
                    <th class="text-left px-6 py-3 text-sm text-gray-400 font-medium">Package</th>
                    <th class="text-center px-6 py-3 text-sm text-gray-400 font-medium">Type</th>
                    <th class="text-center px-6 py-3 text-sm text-gray-400 font-medium">Status</th>
                    <th class="text-right px-6 py-3 text-sm text-gray-400 font-medium">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-700">
                @forelse ($packages as $package)
                    <tr>
                        <td class="px-6 py-4 font-mono text-sm">{{ $package->package }}</td>
                        <td class="px-6 py-4 text-center">
                            <span class="px-2 py-1 rounded text-xs {{ $package->dev ? 'bg-yellow-900/50 text-yellow-400' : 'bg-blue-900/50 text-blue-400' }}">
                                {{ $package->dev ? 'dev' : 'prod' }}
                            </span>
                        </td>
                        <td class="px-6 py-4 text-center">
                            <form action="{{ route('admin.npm-packages.toggle', $package) }}" method="POST" class="inline">
                                @csrf
                                @method('PATCH')
                                <button type="submit" class="px-3 py-1 rounded text-xs font-medium {{ $package->enabled ? 'bg-green-900/50 text-green-400 border border-green-700' : 'bg-red-900/50 text-red-400 border border-red-700' }}">
                                    {{ $package->enabled ? 'Enabled' : 'Disabled' }}
                                </button>
                            </form>
                        </td>
                        <td class="px-6 py-4 text-right">
                            <form action="{{ route('admin.npm-packages.destroy', $package) }}" method="POST" class="inline" onsubmit="return confirm('Delete this package?')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="text-red-400 hover:text-red-300 text-sm">Delete</button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="4" class="px-6 py-8 text-center text-gray-500">No packages configured.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
@endsection
