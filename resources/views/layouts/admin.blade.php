<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Boilerplate Admin')</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-900 text-gray-100 min-h-screen">
    <nav class="bg-gray-800 border-b border-gray-700">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex items-center justify-between h-16">
                <a href="{{ route('admin.index') }}" class="text-cyan-400 font-bold text-lg">Boilerplate Admin</a>
                <div class="flex space-x-4">
                    <a href="{{ route('admin.sail-services') }}" class="text-gray-300 hover:text-white px-3 py-2 text-sm">Sail Services</a>
                    <a href="{{ route('admin.files') }}" class="text-gray-300 hover:text-white px-3 py-2 text-sm">Files</a>
                    <a href="{{ route('admin.composer-packages') }}" class="text-gray-300 hover:text-white px-3 py-2 text-sm">Composer</a>
                    <a href="{{ route('admin.npm-packages') }}" class="text-gray-300 hover:text-white px-3 py-2 text-sm">npm</a>
                    <a href="{{ route('admin.docker-services') }}" class="text-gray-300 hover:text-white px-3 py-2 text-sm">Docker Services</a>
                    <a href="{{ route('admin.commands') }}" class="text-gray-300 hover:text-white px-3 py-2 text-sm">Commands</a>
                    <span class="text-gray-600">|</span>
                    <a href="{{ url('/') }}" class="text-gray-300 hover:text-white px-3 py-2 text-sm">&larr; Front Page</a>
                </div>
            </div>
        </div>
    </nav>

    <main class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        @if (session('success'))
            <div class="bg-green-900/50 border border-green-700 text-green-300 px-4 py-3 rounded mb-6">
                {{ session('success') }}
            </div>
        @endif

        @if ($errors->any())
            <div class="bg-red-900/50 border border-red-700 text-red-300 px-4 py-3 rounded mb-6">
                <ul class="list-disc list-inside">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        @yield('content')
    </main>
</body>
</html>
