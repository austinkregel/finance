<template>
    <div class="flex flex-wrap items-center rounded">
        <div class="w-12">
            <div v-if="isError" class="p-2 rounded-full text-red-500" v-dark-mode-input>
                <svg class="w-8" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg"><path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"></path></svg>
            </div>
        </div>
        <div class="flex flex-wrap items-center flex-1 ml-2">
            <div class="font-bold">{{ activity.subject.institution.name }}</div>
            <pre class="w-full text-monospace">{{ activity.description }}</pre>
            <div class="text-xs" v-dark-mode-dark-text>{{ date }}</div>
        </div>
    </div>
</template>

<script>
export default {
    props: ['activity'],
    computed: {
        isError() {
            return compromise(this.activity.description).isError()
        },

        date() {
            return dayjs(this.activity.created_at).formatToLocaleTimezone()
        }
    }
}
</script>
