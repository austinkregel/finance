<template>
    <div v-if="activities.length > 0" v-dark-mode-white-background v-dark-mode-dark-text class="px-4 italic rounded shadow">
        <div v-for="(activity, $i) in activities" class="border-gray-500 py-4" :class="{'border-b': $i !== ($store.getters.notifications.length - 1)}">
            <div class="flex flex-wrap items-center">
                <activity-access-token :activity="activity"/>
            </div>
        </div>
    </div>
    <div v-else>
        <div v-dark-mode-white-background v-dark-mode-dark-text class="p-4 italic rounded shadow">
            No activity yet... Go do something...
        </div>
    </div>
</template>
<script>
    import ActivityAccessToken from "../components/ActivityLog/ActivityAccessToken";
    export default {
        components: {
            ActivityAccessToken
        },
        methods: {
            date(date) {
                return dayjs(date).formatToLocaleTimezone()
            }
        },
        computed: {
            activities() {
                return this.$store.getters.activities.data
            }
        }
    }
</script>
