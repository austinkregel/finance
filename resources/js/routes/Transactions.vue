<template>
    <div>
        <div class="mx-4 mb-4 flex flex-wrap items-center">
            <div class="flex items-center justify-end w-full">
                <button @click="refreshAccounts" class="p-2 ml-4 border-2 border-blue-700 focus:outline-none text-blue-700 rounded-lg flex items-center hover:bg-blue-700 hover:text-white hover:shadow">
                    <zondicon icon="refresh" class="w-6 h-6 fill-current" />
                    <span class="ml-2 font-medium">Refresh</span>
                </button>
            </div>
        </div>
        <div class="shadow bg-white rounded-lg mx-4">
            <div v-for="(transaction, $index) in data">
                <single-transaction :transaction="transaction" :index="$index"/>
            </div>

            <infinite-loading @infinite="fetchNewTransactions" force-use-infinite-wrapper>
                <div slot="spinner" class="mt-4"><loading-animation /></div>
            </infinite-loading>
        </div>
    </div>
</template>

<script>
    import InfiniteLoading from 'vue-infinite-loading';
    export default {
        components: {
            InfiniteLoading,
        },
        data() {
            return {
                transactionAction: '',
                transactionActions: [
                    {
                        path: '/api/action/thing',
                        name: 'Tag Transactions'
                    },
                ],
                modals: {
                    group: false,

                }
            }
        },
        computed: {
            data() {
                return this.$store.getters.transactions.data;
            },
            selectedTransactions() {
                return Object.keys(this.$store.getters.selectedTransactions);
            }
        },
        methods: {
            async fetchNewTransactions($state, options) {
                try {
                    await this.$store.dispatch('fetchTransactions', {
                        page: this.$store.getters.transactions.meta.current_page + 1
                    })
                } finally {
                    $state.loaded();



                    if (this.$store.getters.transactions.meta.current_page >= this.$store.getters.transactions.meta.last_page) {
                        $state.complete();
                    }
                }
            },
            refreshAccounts() {
                const tokens = Object.values(this.$store.getters.accounts.data.reduce((accessTokens, account) => ({
                    ...accessTokens,
                    [account.token.id]: account.token,
                }), {}))

                tokens.forEach(token =>
                    this.$store.dispatch('runAction', {
                        action: 'fetch-transactions',
                        data: {
                            months: 1,
                            access_token_id: token.id
                        }
                    })
                )
            }
        },
        mounted() {
        }
    }
</script>

<style scoped>

</style>
