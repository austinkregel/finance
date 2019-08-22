<style scoped>

</style>

<template>
    <div>
        <div class="container mx-auto">
            <div class="flex flex-wrap">
                <div class="w-full md:w-1/4">
                    <div class="router-link-table">
                        <ul class="flex flex-col">
                            <li class="uppercase text-gray-600 font-bold">
                                Settings
                            </li>
                            <li  class="flex items-center">
                                <svg class="w-6 fill-current inline text-gray-600" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20"><path d="M0 6l10-6 10 6v2H0V6zm0 12h20v2H0v-2zm2-2h16v2H2v-2zm0-8h4v8H2V8zm6 0h4v8H8V8zm6 0h4v8h-4V8z"></path></svg>
                                <router-link to="/plaid" class="text-lg font-bold">
                                    Plaid
                                </router-link>
                            </li>
                        </ul>
                    </div>
                </div>
                <div class="w-full md:w-3/4">
                    <div class="mx-2">
                        <router-view></router-view>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
    export default {
        name: 'Settings',
    }
</script>
