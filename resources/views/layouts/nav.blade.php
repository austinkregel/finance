<nav class="shadow py-6" :class="{ 'bg-blue-800': !$store.getters.darkMode, 'bg-gray-900': $store.getters.darkMode }">
    <div class="container mx-auto px-6 md:px-0">
        <div class="flex items-center justify-between w-full">
            <div class="mr-6 ml-4">
                <a href="{{ url('/') }}" class="text-lg font-semibold text-white no-underline">
                    {{ config('app.name', 'Laravel') }}
                </a>
            </div>

            @auth
            <div class="flex-1 text-left text-white">
                <a class="nav-link underline mr-4" href="/accounts">Accounts</a>
            </div>
            @endauth
            <!-- Authentication Links -->
            @guest
                <div class="text-white">
                    <a class="mr-4" href="{{ route('login') }}">{{ __('Login') }}</a>
                    @if (Route::has('register'))
                        <a class="py-2 px-4 border border-white text-white rounded" href="{{ route('register') }}">{{ __('Register') }}</a>
                    @endif
                </div>

            @else
                <div class="flex-1 text-right">
                    <a class="nav-link text-white underline ml-4" href="/settings">Settings</a>
                    <a class="text-white py-2 pl-4 pr-6 rounded"
                       href="{{ route('logout') }}"
                       onclick="event.preventDefault();document.getElementById('logout-form').submit();">
                        {{ __('Logout') }}
                    </a>

                    <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
                        @csrf
                    </form>
                </div>
            @endguest
        </div>
    </div>
</nav>
