@extends('layouts.app')

@section('content')
    <div class="container mx-auto mt-4">
        <div class="flex flex-wrap justify-center">
            <div class="w-full max-w-sm">
                <div class="flex flex-col break-words rounded shadow-md overflow-hidden" v-dark-mode-white-background>

                    <div class="font-semibold py-3 px-6 mb-0" :class="{ 'bg-gray-900 text-white': darkMode, 'bg-gray-200 text-gray-700 border-t border-b': !darkMode }">
                        {{ __('Register') }}
                    </div>

                    <form class="w-full p-6" method="POST" action="{{ route('register') }}">
                        @csrf

                        <div class="flex flex-wrap mb-6">
                            <label for="name" class="block text-sm font-bold mb-2" v-dark-mode-dark-text>
                                {{ __('Name') }}:
                            </label>

                            <input v-dark-mode-input id="name" type="text" class="shadow appearance-none rounded w-full py-2 px-3 leading-tight focus:outline-none focus:shadow-outline{{ $errors->has('name') ? ' border-red-500' : '' }}" name="name" value="{{ old('name') }}" required autofocus>

                            @if ($errors->has('name'))
                                <p class="text-red-500 text-xs italic mt-4">
                                    {{ $errors->first('name') }}
                                </p>
                            @endif
                        </div>

                        <div class="flex flex-wrap mb-6">
                            <label for="email" class="block text-sm font-bold mb-2" v-dark-mode-dark-text>
                                {{ __('E-Mail Address') }}:
                            </label>

                            <input v-dark-mode-input id="email" type="email" class="shadow appearance-none rounded w-full py-2 px-3 leading-tight focus:outline-none focus:shadow-outline{{ $errors->has('email') ? ' border-red-500' : '' }}" name="email" value="{{ old('email') }}" required>

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

                        <div class="flex flex-wrap mb-6">
                            <label for="password-confirm" class="block text-sm font-bold mb-2" v-dark-mode-dark-text>
                                {{ __('Confirm Password') }}:
                            </label>

                            <input v-dark-mode-input id="password-confirm" type="password" class="shadow appearance-none rounded w-full py-2 px-3 leading-tight focus:outline-none focus:shadow-outline" name="password_confirmation" required>
                        </div>

                        <div class="flex flex-wrap">
                            <button type="submit" class="inline-block align-middle text-center select-none font-bold whitespace-no-wrap py-2 px-4 rounded text-base leading-normal no-underline text-gray-100 bg-blue-500 hover:bg-blue-700">
                                {{ __('Register') }}
                            </button>

                            <p class="w-full text-xs text-center mt-8 -mb-4" v-dark-mode-light-text>
                                Already have an account?
                                <a v-dark-mode-link class="no-underline" href="{{ route('login') }}">
                                    Login
                                </a>
                            </p>
                        </div>
                    </form>

                </div>
            </div>
        </div>
    </div>
@endsection
