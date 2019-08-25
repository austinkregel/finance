<style scoped>

</style>

<template>
    <div class="flex flex-wrap">
        <div v-for="account in accounts" class="w-full md:w-1/2 lg:w-1/3">
            <div class="rounded-lg m-4 p-4 shadow"
                 :class="{ 'bg-green-100 text-green-900': account.balance >= 0, 'bg-red-100 text-red-900': account.balance < 0 }"
            >
                <div class="text-xl">{{ account.name }}</div>
                <div>
                    <div class="text-2xl font-bold">${{ account.available }} <span class="text-sm">/ ${{ account.balance }}</span></div>
                    <div>{{ lastUpdated(account.updated_at) }}</div>
                    <div>{{ account.type }} - {{ account.subtype }}</div>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
    export default {
        props: [],

        data() {
            return {
                accounts: [],
            }
        },

        methods: {
            async getAccounts() {
                this.accounts = await request('get', '/api/accounts');
            },
            lastUpdated(time) {
                return moment(time).format('LLL')
            }
        },

        mounted() {
            this.getAccounts();
        }
    }
</script>
