@include('layouts.navigation')

            <!-- Page Heading -->
            @if (isset($header))
                <header class="bg-white shadow">
                    <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
                        {{ $header }}
                    </div>
                </header>
            @endif

            <!-- Page Content -->
            <main>
                {{ $slot }}  
            </main> 
            {{-- <main class="py-4">
                @yield('chat.content')
            </main> --}}
        </div>
    @yield('js')    
    </body>
</html>
