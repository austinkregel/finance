<template>
    <div>
        <div class="container mx-auto">
            <div class="flex flex-wrap mx-4">
                <div class="w-full md:w-1/4">
                    <div class="router-link-table">
                        <ul class="flex flex-col" :class="{ 'text-white': $store.getters.darkMode, 'text-gray-600': !$store.getters.darkMode }">

                            <li class="uppercase font-bold">
                                Settings
                            </li>
                            <settings-link  path="/notifications">
                                <svg slot="image" class="w-6" fill="currentColor" viewBox="0 0 20 20"><path d="M10 2a6 6 0 00-6 6v3.586l-.707.707A1 1 0 004 14h12a1 1 0 00.707-1.707L16 11.586V8a6 6 0 00-6-6zM10 18a3 3 0 01-3-3h6a3 3 0 01-3 3z"></path></svg>
                                <router-link slot="text" to="/notifications" class="text-lg pl-4 font-bold">
                                    Notifications
                                </router-link>
                            </settings-link>
                            <settings-link  path="/plaid">
                                <svg slot="image" class="w-6" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10.496 2.132a1 1 0 00-.992 0l-7 4A1 1 0 003 8v7a1 1 0 100 2h14a1 1 0 100-2V8a1 1 0 00.496-1.868l-7-4zM6 9a1 1 0 00-1 1v3a1 1 0 102 0v-3a1 1 0 00-1-1zm3 1a1 1 0 012 0v3a1 1 0 11-2 0v-3zm5-1a1 1 0 00-1 1v3a1 1 0 102 0v-3a1 1 0 00-1-1z" clip-rule="evenodd"></path></svg>
                                <router-link slot="text" to="/plaid" class="text-lg pl-4 font-bold ">
                                    Plaid
                                </router-link>
                            </settings-link>
                            <settings-link path="/historical-sync">
                                <svg slot="image" class="w-6" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M5 2a2 2 0 00-2 2v14l3.5-2 3.5 2 3.5-2 3.5 2V4a2 2 0 00-2-2H5zm4.707 3.707a1 1 0 00-1.414-1.414l-3 3a1 1 0 000 1.414l3 3a1 1 0 001.414-1.414L8.414 9H10a3 3 0 013 3v1a1 1 0 102 0v-1a5 5 0 00-5-5H8.414l1.293-1.293z" clip-rule="evenodd"></path></svg>
                                <router-link slot="text" to="/historical-sync" class="text-lg pl-4 font-bold">
                                    Historical Sync
                                </router-link>
                            </settings-link>
                            <settings-link  path="/alert-channels">
                                <svg slot="image" class="w-6" fill="currentColor" viewBox="0 0 20 20"><path d="M10 2a6 6 0 00-6 6v3.586l-.707.707A1 1 0 004 14h12a1 1 0 00.707-1.707L16 11.586V8a6 6 0 00-6-6zM10 18a3 3 0 01-3-3h6a3 3 0 01-3 3z"></path></svg>
                                <router-link slot="text" to="/alert-channels" class="text-lg pl-4 font-bold">
                                    Alert Channels
                                </router-link>
                            </settings-link>
                            <settings-link path="/failed-jobs">
                                <svg slot="image" class="w-6" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"></path></svg>
                                <router-link slot="text" to="/failed-jobs" class="text-lg pl-4 font-bold">
                                    Failed Jobs
                                </router-link>
                            </settings-link>
                            <settings-link path="/activity-log">
                                <svg slot="image" class="w-6 h-6" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg"><path d="M9 4.804A7.968 7.968 0 005.5 4c-1.255 0-2.443.29-3.5.804v10A7.969 7.969 0 015.5 14c1.669 0 3.218.51 4.5 1.385A7.962 7.962 0 0114.5 14c1.255 0 2.443.29 3.5.804v-10A7.968 7.968 0 0014.5 4c-1.255 0-2.443.29-3.5.804V12a1 1 0 11-2 0V4.804z"></path></svg>
                                <router-link slot="text" to="/activity-log" class="text-lg pl-4 font-bold">
                                    Activity Log
                                </router-link>
                            </settings-link>
                            <li class="flex items-center p-2">
                                <button class="flex items-center justify-between h-8" @click="$store.commit('toggleDarkmode')">
                                    <span class="rounded-full flex items-center shadow cursor-pointer w-6" :class="{ 'bg-blue-500 justify-end': $store.getters.darkMode, 'justify-start bg-gray-600': !$store.getters.darkMode }">
                                        <span class="rounded-full shadow w-3 h-3 shadow-inner bg-white shadow" :class="{ 'border border-gray-400': !$store.getters.darkMode }"></span>
                                    </span>
                                </button>
                                <span class="text-lg pl-4 font-bold">Dark Mode</span>
                            </li>
                            <li class="flex items-center p-2 text-red-500">
                                <button class="flex items-center justify-between h-8" @click="$store.dispatch('purgeCache')">
                                    <svg class="w-6" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M9 2a1 1 0 00-.894.553L7.382 4H4a1 1 0 000 2v10a2 2 0 002 2h8a2 2 0 002-2V6a1 1 0 100-2h-3.382l-.724-1.447A1 1 0 0011 2H9zM7 8a1 1 0 012 0v6a1 1 0 11-2 0V8zm5-1a1 1 0 00-1 1v6a1 1 0 102 0V8a1 1 0 00-1-1z" clip-rule="evenodd"></path></svg>
                                    <span class="text-lg pl-4 font-bold">Purge your cache</span>
                                </button>
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
    import SettingsLink from "./SettingsLink";
    export default {
        components: { SettingsLink },
        props: ['darkMode'],
        methods: {
            active(path) {
                return this.$route.fullPath
            }
        },
        mounted() {
            this.$store.dispatch('fetchAccessTokens')
            this.$store.dispatch('fetchAccounts')
        }
    }
</script>
