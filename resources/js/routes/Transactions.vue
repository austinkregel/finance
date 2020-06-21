<template>
    <div v-if="accounts.length > 0">
        <div class="mx-4 mb-4 flex flex-wrap items-center">
            <div class="flex items-center justify-end w-full">
                <button @click="refreshAccounts" :disabled="loading" class="p-2 ml-4 border-2 focus:outline-none rounded-lg flex items-center hover:shadow"
                        :class="{ 'opacity-50': loading }"
                        v-dark-mode-button
                >
                    <zondicon icon="refresh" class="w-6 h-6 fill-current" :class="{ 'rotate': loading }" />
                    <span class="ml-2 font-medium">Refresh<span v-if="loading">ing</span></span>
                </button>
            </div>
        </div>
        <div class="shadow rounded-lg mx-4" v-dark-mode-dark-text v-dark-mode-white-background>
            <div v-for="(transaction, $index) in data">
                <single-transaction :transaction="transaction" :index="$index"/>
            </div>

            <infinite-loading @infinite="fetchNewTransactions" force-use-infinite-wrapper>
                <div slot="spinner" class="p-4"><loading-animation /></div>
                <div slot="no-more" class="py-4">No more data...</div>
                <div slot="no-results" class="py-4">No results...</div>
            </infinite-loading>
        </div>
    </div>
    <div v-else>
        No accounts...
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
                },
                loading: false,
            }
        },
        computed: {
            data() {
                return this.$store.getters.transactions.data;
            },
            selectedTransactions() {
                return Object.keys(this.$store.getters.selectedTransactions);
            },
            accounts() {
                return this.$store.getters.accounts.data
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
                this.loading = true;
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

                setTimeout(() => {
                    this.$store.dispatch('fetchTransactions', {
                        page: 1
                    });
                    this.loading = false;
                }, 10000);
            }
        },
        mounted() {
        }
    }
</script>

<style scoped>

</style>
