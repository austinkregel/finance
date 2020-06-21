<style scoped>

</style>

<template>
    <div>
        <div class="flex flex-wrap items-center justify-between mb-4">
            <h4 class="text-xl">Alert Channels</h4>
        </div>

        <div class="shadow p-4 rounded"  v-dark-mode-dark-text v-dark-mode-white-background>
            <div class="italic flex flex-col">
                <div class="">You will need to ensure your environment variables are set up correctly for their respective channel, otherwise the notifications may not work.</div>
                <label v-for="channel in channels" class="w-full mt-4 cursor-pointer">
                    <input type="checkbox" v-model="alert_channels" :value="channel.type" />
                    <span class="ml-2">{{ channel.name }}</span>
                </label>
            </div>
            <button class="mt-4 py-2 px-4 bg-blue-500 rounded text-white flex items-center" @click.prevent="updateChannels">
                <svg class="w-5 rotate" v-if="loading" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M4 2a1 1 0 011 1v2.101a7.002 7.002 0 0111.601 2.566 1 1 0 11-1.885.666A5.002 5.002 0 005.999 7H9a1 1 0 010 2H4a1 1 0 01-1-1V3a1 1 0 011-1zm.008 9.057a1 1 0 011.276.61A5.002 5.002 0 0014.001 13H11a1 1 0 110-2h5a1 1 0 011 1v5a1 1 0 11-2 0v-2.101a7.002 7.002 0 01-11.601-2.566 1 1 0 01.61-1.276z" clip-rule="evenodd"></path></svg>
                <span class="ml-2">
                    Sav<span v-if="loading">ing</span><span v-else>e</span> Channels
                </span>
            </button>
        </div>
    </div>
</template>

<script>
    export default {
        props: ['darkMode'],
        data() {
            return {
                alert_channels: [],
                channels: require('../channels'),
                loading: false,
            }
        },
        methods: {
            updateChannels() {
                this.loading = true;
                axios.put('/api/user', {
                    alert_channels: this.alert_channels
                }).then(() => this.loading = false).catch(() => this.loading = false);
            }
        },
        async mounted() {
            const { data: user } = await axios.get('/api/user');
            this.alert_channels = user.alert_channels || [];
            console.log(user)
        }
    }
</script>
