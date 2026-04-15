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
            <button type="submit" class="bg-cyan-600 hover:bg-cyan-700 text-white px-4 py-2 rounded font-medium">Add Command</button>
        </div>
    </form>

    {{-- List --}}
    <div id="command-list" class="bg-gray-800 rounded-lg border border-gray-700 divide-y divide-gray-700">
        @forelse ($commands as $index => $command)
            <div class="flex items-center justify-between px-6 py-3 transition-colors duration-300" data-id="{{ $command->id }}">
                <div class="flex items-center gap-4">
                    <span class="text-gray-500 text-xs font-mono w-6 text-center command-position">{{ $index + 1 }}</span>
                    <div class="flex flex-col gap-0.5">
                        <button
                            onclick="moveCommand({{ $command->id }}, 'up', this)"
                            class="text-gray-600 hover:text-cyan-400 transition-colors text-xs leading-none disabled:opacity-20 disabled:cursor-default disabled:hover:text-gray-600"
                            {{ $loop->first ? 'disabled' : '' }}
                            title="Move up"
                        >&#9650;</button>
                        <button
                            onclick="moveCommand({{ $command->id }}, 'down', this)"
                            class="text-gray-600 hover:text-cyan-400 transition-colors text-xs leading-none disabled:opacity-20 disabled:cursor-default disabled:hover:text-gray-600"
                            {{ $loop->last ? 'disabled' : '' }}
                            title="Move down"
                        >&#9660;</button>
                    </div>
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

    <script>
        async function moveCommand(id, direction, btn) {
            const row = btn.closest('[data-id]');
            const list = document.getElementById('command-list');
            const token = document.querySelector('meta[name="csrf-token"]')?.content
                || '{{ csrf_token() }}';

            const res = await fetch(`/admin/commands/${id}/move`, {
                method: 'PATCH',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': token,
                    'Accept': 'application/json',
                },
                body: JSON.stringify({ direction }),
            });

            if (!res.ok) return;

            // Swap DOM rows
            const sibling = direction === 'up' ? row.previousElementSibling : row.nextElementSibling;
            if (!sibling) return;

            if (direction === 'up') {
                list.insertBefore(row, sibling);
            } else {
                list.insertBefore(sibling, row);
            }

            // Flash highlight
            row.style.backgroundColor = 'rgba(34, 211, 238, 0.15)';
            setTimeout(() => { row.style.backgroundColor = ''; }, 300);

            // Update positions and disabled states
            const rows = list.querySelectorAll('[data-id]');
            rows.forEach((r, i) => {
                r.querySelector('.command-position').textContent = i + 1;
                const upBtn = r.querySelectorAll('button[onclick*="move"]')[0];
                const downBtn = r.querySelectorAll('button[onclick*="move"]')[1];
                upBtn.disabled = i === 0;
                downBtn.disabled = i === rows.length - 1;
            });
        }
    </script>
@endsection
