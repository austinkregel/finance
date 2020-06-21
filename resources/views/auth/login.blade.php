@extends('layouts.app')

@section('content')
    <div class="container mx-auto mt-4">
        <div class="flex flex-wrap justify-center">
            <div class="w-full max-w-sm">
                <div class="flex flex-col break-words rounded shadow-md overflow-hidden" v-dark-mode-white-background>

                    <div class="font-semibold py-3 px-6 mb-0" :class="{ 'bg-gray-900 text-white': darkMode, 'bg-gray-200 text-gray-700 border-t border-b': !darkMode }">
                        {{ __('Login') }}
                    </div>

                    <form class="w-full p-6" method="POST" action="{{ route('login') }}">
                        @csrf

                        <div class="flex flex-wrap mb-6">
                            <label for="email" class="block text-sm font-bold mb-2" v-dark-mode-dark-text>
                                {{ __('E-Mail Address') }}:
                            </label>

                            <input v-dark-mode-input id="email" type="email" class="shadow appearance-none rounded w-full py-2 px-3 leading-tight focus:outline-none focus:shadow-outline{{ $errors->has('email') ? ' border-red-500' : '' }}" name="email" value="{{ old('email') }}" required autofocus>

                            @if ($errors->has('email'))
                                <p class="text-red-500 text-xs italic mt-4">
                                    {{ $errors->first('email') }}
                                </p>
                            @endif
                        </div>

                        <div class="flex flex-wrap mb-6">
                            <label for="password" class="block text-sm font-bold mb-2" v-dark-mode-dark-text>
                                {{ __('Password') }}:
                            </label>

                            <input v-dark-mode-input id="password" type="password" class="shadow appearance-none rounded w-full py-2 px-3 leading-tight focus:outline-none focus:shadow-outline{{ $errors->has('password') ? ' border-red-500' : '' }}" name="password" required>

                            @if ($errors->has('password'))
                                <p class="text-red-500 text-xs italic mt-4">
                                    {{ $errors->first('password') }}
                                </p>
                            @endif
                        </div>

                        <div class="flex mb-6 items-center">
                            <input type="checkbox" name="remember" id="remember" {{ old('remember') ? 'checked' : '' }}>

                            <label class="text-sm ml-3" for="remember" v-dark-mode-dark-text>
                                {{ __('Remember Me') }}
                            </label>
                        </div>

                        <div class="flex flex-wrap items-center">
                            <button type="submit" class="bg-blue-500 hover:bg-blue-700 text-gray-100 font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline">
                                {{ __('Login') }}
                            </button>

                            @if (Route::has('password.request'))
                                <a class="text-sm whitespace-no-wrap no-underline ml-auto" href="{{ route('password.request') }}" v-dark-mode-link>
                                    {{ __('Forgot Your Password?') }}
                                </a>
                            @endif

                            @if (Route::has('register'))
                                <p class="w-full text-xs text-center mt-8 -mb-4" v-dark-mode-dark-text>
                                    Don't have an account?
                                    <a class="no-underline" v-dark-mode-link href="{{ route('register') }}">
                                        Register
                                    </a>
                                </p>
                            @endif
                        </div>
                    </form>

                </div>
            </div>
        </div>
    </div>
@endsection
