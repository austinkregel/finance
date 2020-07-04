<template>
    <div v-if="$store.getters.notifications.length > 0" v-dark-mode-white-background v-dark-mode-dark-text class="px-4 italic rounded shadow">
        <div v-for="(notification, $i) in $store.getters.notifications" class="border-gray-500 py-4" :class="{'border-b': $i !== ($store.getters.notifications.length - 1)}">
            <div class="flex flex-wrap items-center">
                <div>
                    <button class="cursor-pointer" title="Mark this notification as read" @click.prevent="$store.dispatch('markNotificationAsRead', notification)">
                        <svg class="w-6" fill="currentColor" viewBox="0 0 20 20"><path d="M10 12a2 2 0 100-4 2 2 0 000 4z"></path><path fill-rule="evenodd" d="M.458 10C1.732 5.943 5.522 3 10 3s8.268 2.943 9.542 7c-1.274 4.057-5.064 7-9.542 7S1.732 14.057.458 10zM14 10a4 4 0 11-8 0 4 4 0 018 0z" clip-rule="evenodd"></path></svg>
                    </button>
                </div>
                <div class="ml-4">
                    <div class="font-bold">{{ notification.data.title }}</div>
                    <div>{{ notification.data.body }}</div>
                    <div v-dark-mode-dark-text>{{ date(notification.created_at) }}</div>
                </div>
            </div>
        </div>
    </div>
    <div v-else>
        <div v-dark-mode-white-background v-dark-mode-dark-text class="p-4 italic rounded shadow">
            You're all caught up!
        </div>
    </div>
</template>
<script>
    export default {
        methods: {
            date(date) {
                return dayjs(date).formatToLocaleTimezone()
            }
        }
    }
</script>
