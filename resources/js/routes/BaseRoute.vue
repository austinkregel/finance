<template>
    <div class="container mx-auto flex flex-wrap">
        <div class="w-full md:w-1/4 xl:1/5 relative">
            <div class="md:mt-16 pt-2 md:fixed">
                <ul class="flex flex-col mx-4" v-dark-mode-light-text>
                    <li v-for="route in routes" class="mt-2 rounded-lg w-full pr-4" :class="{
                        'shadow-lg bg-white': route.path === currentRoute && !$store.state.darkMode,
                        'shadow-lg bg-gray-700': route.path === currentRoute && $store.state.darkMode,
                    }">
                        <router-link :to="route.path" class="text-lg font-bold p-2 flex items-center ml-1">
                            <span v-html="route.svg"></span>
                            <span class="ml-2">{{ route.name }}</span>
                        </router-link>
                    </li>
                </ul>
            </div>
        </div>

        <div class="w-full md:w-3/4 xl:4/5 mt-4">
            <router-view/>
        </div>
    </div>
</template>

<script>
    export default {
        props: ['user'],
        data() {
            return {
                routes: [
                    {
                        path: '/',
                        name: 'Transactions',
                        svg: '<svg class="w-6" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M4 4a2 2 0 00-2 2v4a2 2 0 002 2V6h10a2 2 0 00-2-2H4zm2 6a2 2 0 012-2h8a2 2 0 012 2v4a2 2 0 01-2 2H8a2 2 0 01-2-2v-4zm6 4a2 2 0 100-4 2 2 0 000 4z" clip-rule="evenodd"></path></svg>'
                    },
                    {
                        path: '/groups',
                        name: 'Groups',
                        svg: '<svg class="w-6" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M17.707 9.293a1 1 0 010 1.414l-7 7a1 1 0 01-1.414 0l-7-7A.997.997 0 012 10V5a3 3 0 013-3h5c.256 0 .512.098.707.293l7 7zM5 6a1 1 0 100-2 1 1 0 000 2z" clip-rule="evenodd"></path></svg>'
                    },
                    {
                        path: '/alerts',
                        name: 'Alert',
                        svg: '<svg class="w-6" fill="currentColor" viewBox="0 0 20 20"><path d="M10 2a6 6 0 00-6 6v3.586l-.707.707A1 1 0 004 14h12a1 1 0 00.707-1.707L16 11.586V8a6 6 0 00-6-6zM10 18a3 3 0 01-3-3h6a3 3 0 01-3 3z"></path></svg>'
                    },
                    {
                        path: '/metrics',
                        name: 'Metrics',
                        svg: '<svg class="w-6" fill="currentColor" viewBox="0 0 20 20"><path d="M2 11a1 1 0 011-1h2a1 1 0 011 1v5a1 1 0 01-1 1H3a1 1 0 01-1-1v-5zM8 7a1 1 0 011-1h2a1 1 0 011 1v9a1 1 0 01-1 1H9a1 1 0 01-1-1V7zM14 4a1 1 0 011-1h2a1 1 0 011 1v12a1 1 0 01-1 1h-2a1 1 0 01-1-1V4z"></path></svg>'
                    },
                ]
            }
        },
        computed: {
            currentRoute() {
                return this.$route.path;
            },
        },
        mounted() {
            this.$store.commit('user', this.user)
            console.log(this.user)
        }
    }
</script>
