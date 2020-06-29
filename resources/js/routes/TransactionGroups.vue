<template>
    <div>
        <div class="mx-2 mb-4 flex flex-wrap items-center">
            <div class="flex items-center justify-between w-full">
               <div class="flex flex-col w-2/3">
                   <div class="text-xs mx-2" v-dark-mode-light-gray-text>
                       These groups are meant to be a way for you to classify your own transactions. Ex: taxes, bills, mortgage, investments, fees, subscriptions, gas, grocery stores, etc...
                   </div>
               </div>

                <div class="flex justify-end">
                    <button @click.prevent="regroupTransactions" class="p-2 rounded-lg" :class="{ 'rotate': loading }" v-dark-mode-button>
                        <svg class="w-6" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M5 2a2 2 0 00-2 2v14l3.5-2 3.5 2 3.5-2 3.5 2V4a2 2 0 00-2-2H5zm4.707 3.707a1 1 0 00-1.414-1.414l-3 3a1 1 0 000 1.414l3 3a1 1 0 001.414-1.414L8.414 9H10a3 3 0 013 3v1a1 1 0 102 0v-1a5 5 0 00-5-5H8.414l1.293-1.293z" clip-rule="evenodd"></path></svg>
                    </button>
                    <new-group-modal />
                </div>
            </div>
        </div>
        <div class="rounded-lg flex flex-wrap">
            <div v-for="tag in data" class="relative w-full md:w-1/3 mt-4">
                <div class="ml-4 shadow px-2 pb-2 pt-1 rounded" v-dark-mode-dark-text v-dark-mode-white-background>
                    <edit-group-modal :tag="tag" class="flex"/>
                    <span class="font-medium" v-dark-mode-dark-text>{{ tag.name.en }}</span>
                    <div class="text-xs tracking-tight">
                        <div v-for="condition in tag.conditionals" :key="condition.id">
                            <span :title="'transaction.' + condition.parameter" v-text="'t.' + condition.parameter"></span>
                            {{ condition.comparator }} {{ condition.value }}
                        </div>
                    </div>
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
                },
                loading: false,
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
