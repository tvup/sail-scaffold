<div class="bg-gray-800/50 rounded-lg p-4 border border-gray-700 mb-6">
    <p class="text-gray-400 text-sm mb-2">Available placeholders:</p>
    <ul class="space-y-1">
        @foreach (config('boilerplate.placeholders') as $key => $def)
            <li><span class="font-mono text-xs text-cyan-400 bg-gray-700 px-2 py-1 rounded">{{ $key }}</span> <span class="text-gray-500 text-xs">{{ $def['description'] }}</span></li>
        @endforeach
    </ul>
</div>
