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
                <div class="flex-1 flex justify-end items-center">
                    <a href="/settings#/notifications" class="relative text-white">
                        <svg class="w-6 fill-current" viewBox="0 0 20 20"><path d="M10 2a6 6 0 00-6 6v3.586l-.707.707A1 1 0 004 14h12a1 1 0 00.707-1.707L16 11.586V8a6 6 0 00-6-6zM10 18a3 3 0 01-3-3h6a3 3 0 01-3 3z"></path></svg>
                        <span v-if="$store.getters.notifications.length > 0" class="bg-red-500 text-white px-1 rounded absolute top-0 left-0 text-xs -mt-2 ml-3">
                            @{{ $store.getters.notifications.length }}
                        </span>
                    </a>
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
