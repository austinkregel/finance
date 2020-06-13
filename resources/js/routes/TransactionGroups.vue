<template>
    <div>
        <div class="mx-2 mb-4 flex flex-wrap items-center">
            <div class="flex items-center justify-between w-full">
               <div class="flex flex-col w-2/3">
                   <div class="text-xs text-gray-500 mx-2">
                       These groups are meant to be a way for you to classify your own transactions. Ex: taxes, bills, mortgage, investments, fees, subscriptions, gas, grocery stores, etc...
                   </div>
               </div>

                <button @click.prevent="regroupTransactions" class="p-2 border border-blue-500 rounded-lg text-blue-500">
                    <zondicon icon="refresh" class="w-6 h-6 fill-current"/>
                </button>
                <new-group-modal />
            </div>
        </div>
        <div class="rounded-lg flex flex-wrap">
            <div v-for="tag in data" class="mx-4 shadow bg-white px-2 pb-2 pt-1 rounded relative">
                <edit-group-modal :tag="tag" class="flex"/>
                <span class="font-medium text-gray-700">{{ tag.name.en }}</span>
                <div class="text-xs tracking-tight">
                    <div v-for="condition in tag.conditionals" :key="condition.id">transaction.{{ condition.parameter }} {{ condition.comparator }} {{ condition.value }}</div>
                </div>
            </div>
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
                return this.$store.getters.groups.data;
            },
            selectedTransactions() {
                return Object.keys(this.$store.getters.selectedTransactions);
            }
        },
        methods: {
            async fetchGroups($state, options) {
                try {
                    await this.$store.dispatch('fetchGroups', {
                        page: this.$store.getters.transactions.meta.current_page + 1
                    })
                } finally {
                    $state.loaded();

                    if (this.$store.getters.transactions.meta.current_page >= this.$store.getters.transactions.meta.last_page) {
                        $state.complete();
                    }
                }
            },
            async regroupTransactions() {
                this.loading = true;
                await this.$store.dispatch('runAction', {
                    action: 'regroup-transactions',
                    data: {
                        user_id: this.$store.getters.user.id,
                    }
                });
                this.loading = false;
            }
        },
        mounted() {
        }
    }
</script>
