<style scoped>

</style>

<template>
    <div>
        <div class="container mx-auto">
            <div class="flex flex-wrap mx-4">
                <div class="w-full md:w-1/4">
                    <div class="router-link-table">
                        <ul class="flex flex-col text-gray-600">
                            <li class="uppercase font-bold">
                                Settings
                            </li>
                            <li  class="flex items-center pt-4">
                                <svg class="w-6" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10.496 2.132a1 1 0 00-.992 0l-7 4A1 1 0 003 8v7a1 1 0 100 2h14a1 1 0 100-2V8a1 1 0 00.496-1.868l-7-4zM6 9a1 1 0 00-1 1v3a1 1 0 102 0v-3a1 1 0 00-1-1zm3 1a1 1 0 012 0v3a1 1 0 11-2 0v-3zm5-1a1 1 0 00-1 1v3a1 1 0 102 0v-3a1 1 0 00-1-1z" clip-rule="evenodd"></path></svg>
                                <router-link to="/plaid" class="text-lg pl-4 font-bold">
                                    Plaid
                                </router-link>
                            </li>
                            <li  class="flex items-center pt-4">
                                <svg class="w-6" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M5 2a2 2 0 00-2 2v14l3.5-2 3.5 2 3.5-2 3.5 2V4a2 2 0 00-2-2H5zm4.707 3.707a1 1 0 00-1.414-1.414l-3 3a1 1 0 000 1.414l3 3a1 1 0 001.414-1.414L8.414 9H10a3 3 0 013 3v1a1 1 0 102 0v-1a5 5 0 00-5-5H8.414l1.293-1.293z" clip-rule="evenodd"></path></svg>
                                <router-link to="/historical-sync" class="text-lg pl-4 font-bold">
                                    Historical Sync
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
