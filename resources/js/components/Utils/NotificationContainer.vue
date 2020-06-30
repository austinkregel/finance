<template>
    <div>
        <button class="" @click="() => isTrayOpen = !isTrayOpen">
        </button>

        <div v-if="isTrayOpen" class="fixed top-0 left-0 right-0 bottom-0 z-10" style="background: rgba(0,0,0,0.2);">
            <div class="flex top-0 left-0 w-full h-full md:w-1/2 xl:w-1/4 ml-auto">
                <div class="p-4 w-full flex flex-col overflow-y-auto shadow" v-dark-mode-white-background v-dark-mode-dark-text>
                    <div class="w-full flex justify-end">
                        <button @click="() => isTrayOpen = !isTrayOpen">
                            <svg class="text-gray-800 w-6" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd"></path></svg>
                        </button>
                    </div>
                    <div class="w-full border-b border-gray-400"  v-dark-mode-light-text>
                        Notifications
                    </div>

                    <div class="flex flex-col">
                        <div v-for="notification in notifications" :key="notification.id" class="py-4 border-b boreder-400">
                            <div>{{ notification.data.title }}</div>
                            <div>{{ notification.data.body }}</div>
                            <div>{{ formatDate(notification.created_at) }}</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
    export default {
        data() {
            return {
                isTrayOpen: false,
                notifications: [],
            }
        },
        methods: {
            async fetchNotifications() {
                const { data: notifications } = await axios.get('/abstract-api/notifications?action=paginate:50&sort=-created_at');

                this.notifications = notifications.data
            },
            formatDate(format) {
                return dayjs(format).formatToLocaleTimezone()
            }
        },
        mounted() {
            this.fetchNotifications();
        }
    }
</script>

<style scoped>

</style>
