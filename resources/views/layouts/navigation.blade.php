{{-- resources/views/layouts/navigation.blade.php --}}
<nav x-data="{ open: false }" class="bg-transparent border-b border-flag-white/20 text-flag-white">
    <!-- Primary Navigation Menu -->
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between h-16">
            <div class="flex">
                <!-- Logo -->
                <div class="shrink-0 flex items-center">
                    <a href="{{ route('home') }}" class="font-extrabold tracking-wide text-flag-white hover:text-flag-white/80">
                        {{-- Si tu utilises le composant logo Breeze, colore-le en blanc --}}
                        <x-application-logo class="block h-9 w-auto fill-current text-flag-white" />
                    </a>
                </div>

                <!-- Navigation Links (public) -->
                <div class="hidden space-x-8 sm:-my-px sm:ms-10 sm:flex">
                    <a href="{{ route('association') }}" class="inline-flex items-center px-1 pt-1 text-sm font-medium hover:underline">
                        L’association
                    </a>
                    <a href="{{ route('calendar.index') }}" class="inline-flex items-center px-1 pt-1 text-sm font-medium hover:underline">
                        Agenda (liste)
                    </a>
                    <a href="{{ route('calendar.full') }}" class="inline-flex items-center px-1 pt-1 text-sm font-medium hover:underline">
                        Calendrier
                    </a>
                    <a href="{{ route('contact') }}" class="inline-flex items-center px-1 pt-1 text-sm font-medium hover:underline">
                        Contact
                    </a>
                    <a href="{{ route('mentions') }}" class="inline-flex items-center px-1 pt-1 text-sm font-medium hover:underline">
                        Mentions légales
                    </a>
                </div>
            </div>

            <!-- Settings Dropdown -->
            <div class="hidden sm:flex sm:items-center sm:ms-6">
                @auth
                <x-dropdown align="right" width="48">
                    <x-slot name="trigger">
                        <button
                            class="inline-flex items-center px-3 py-2 border border-transparent text-sm leading-4 font-medium rounded-md
                                   text-flag-white bg-flag-blue hover:bg-flag-blue/80 focus:outline-none transition">
                            <div>{{ Auth::user()->name }}</div>
                            <div class="ms-1">
                                <svg class="fill-current h-4 w-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" />
                                </svg>
                            </div>
                        </button>
                    </x-slot>

                    <x-slot name="content">
                        <x-dropdown-link :href="route('dashboard')">
                            {{ __('Dashboard') }}
                        </x-dropdown-link>
                        <x-dropdown-link :href="url('/admin')">
                            Admin
                        </x-dropdown-link>

                        <!-- Authentication -->
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <x-dropdown-link :href="route('logout')"
                                onclick="event.preventDefault(); this.closest('form').submit();">
                                {{ __('Log Out') }}
                            </x-dropdown-link>
                        </form>
                    </x-slot>
                </x-dropdown>
                @endauth
            </div>

            <!-- Hamburger -->
            <div class="-me-2 flex items-center sm:hidden">
                <button @click="open = ! open"
                        class="inline-flex items-center justify-center p-2 rounded-md text-flag-white hover:text-flag-white/90 hover:bg-white/10 focus:outline-none transition">
                    <svg class="h-6 w-6" stroke="currentColor" fill="none" viewBox="0 0 24 24">
                        <path :class="{'hidden': open, 'inline-flex': ! open }" class="inline-flex"
                              stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M4 6h16M4 12h16M4 18h16" />
                        <path :class="{'hidden': ! open, 'inline-flex': open }" class="hidden"
                              stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
        </div>
    </div>

    <!-- Responsive Navigation Menu -->
    <div :class="{'block': open, 'hidden': ! open}" class="hidden sm:hidden bg-flag-blue/95 border-t border-flag-white/20">
        <div class="pt-2 pb-3 space-y-1">
            <a href="{{ route('association') }}" class="block px-4 py-2 hover:bg-white/10">L’association</a>
            <a href="{{ route('calendar.index') }}" class="block px-4 py-2 hover:bg-white/10">Agenda (liste)</a>
            <a href="{{ route('calendar.full') }}" class="block px-4 py-2 hover:bg-white/10">Calendrier</a>
            <a href="{{ route('contact') }}" class="block px-4 py-2 hover:bg-white/10">Contact</a>
            <a href="{{ route('mentions') }}" class="block px-4 py-2 hover:bg-white/10">Mentions légales</a>
        </div>

        <!-- Responsive Settings Options -->
        @auth
        <div class="pt-4 pb-1 border-t border-flag-white/20">
            <div class="px-4">
                <div class="font-medium text-base text-flag-white">{{ Auth::user()->name }}</div>
                <div class="font-medium text-sm text-flag-white/80">{{ Auth::user()->email }}</div>
            </div>

            <div class="mt-3 space-y-1">
                <a href="{{ route('dashboard') }}" class="block px-4 py-2 hover:bg-white/10">{{ __('Dashboard') }}</a>
                <a href="{{ url('/admin') }}" class="block px-4 py-2 hover:bg-white/10">Admin</a>
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <a href="{{ route('logout') }}" class="block px-4 py-2 hover:bg-white/10"
                       onclick="event.preventDefault(); this.closest('form').submit();">
                        {{ __('Log Out') }}
                    </a>
                </form>
            </div>
        </div>
        @endauth
    </div>
</nav>
