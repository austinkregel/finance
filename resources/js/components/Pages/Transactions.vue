<style scoped>
    .model-box {
        width: 50%;
        top: 50%;
        left:50%;
        transform: translate(-50%, -50%);
    }
</style>

<template>
    <div class="m-2">
        <div class="text-2xl mx-2 font-bold flex flex-wrap">
            <span class="flex-grow">Recent Transactions</span>
            <toggle @input="togglePossibleSubscriptionSearch" :value="possibleSubscriptions" label="Filter possible subscriptions"/>
        </div>
        <div class="mx-2">
            <input type="text" v-model="search" @keyup="getTransactions" class="block py-2 px-4 shadow w-full my-2 rounded" placeholder="Search for a transaction! Try Walmart or McDonalds" />
        </div>
        <div class="flex flex-wrap">
            <div class="w-full md:w-1/2 lg:w-1/3" v-for="transaction in transactions">
                <div class="flex flex-col shadow rounded p-4 m-2 bg-white" :class="{'text-green-900 ': isPositive(transaction), ' text-red-900': !isPositive(transaction) }"
                    :style="(isPositive(transaction) ? 'border-top: solid #38A169 2px;' : 'border-top: solid #E53E3E 2px;') "
                >
                    <div class="flex justify-between items-center -mb-1" :class="{'text-green-800': isPositive(transaction), 'text-red-800': isPositive(transaction) }">
                        <div class="font-bold text-lg truncate pr-2 tracking-normal">{{ transaction.name }}</div>
                        <div class="font-bold text-lg">${{ Number(-transaction.amount).toFixed(2) }}</div>
                    </div>

                    <div class="flex justify-between items-center">
                        <div class="text-xs flex-grow tracking-wide">{{ transaction.account.name }}</div>
                        <div class="text-xs tracking-wide">{{ transaction.start }}</div>
                        <button v-if="!transaction.is_subscription && transaction.is_possible_subscription === null" @click="() => showModal(transaction)" class="text-xs flex flex-wrap items-center">
                            <zondicon title="Our system flagged this is a possible subscription!" class="ml-1 cursor-pointer w-4 h-4 fill-current" icon="information-outline" />
                        </button>
                    </div>

                    <div class="flex justify-between items-center">
                        <div class="text-xs flex-grow tracking-wide" v-if="transaction.categories">
                            {{ transaction.categories.map(cat => cat.name).join(', ') }}
                        </div>

                        <div class="text-xs font-bold tracking-wide mr-px">
                            <div v-if="transaction.pending" class="text-orange-600 flex flex-wrap items-center">
                                Pending
                                <div class="bg-orange-500 w-3 h-3 rounded-full p-1 ml-1"></div>
                            </div>
                            <div v-else class="text-blue-800 flex flex-wrap items-center">
                                Approved
                                <div class="bg-blue-600 w-3 h-3 rounded-full p-1 ml-1"></div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <infinite-loading @infinite="getTransactions">
            <div slot="spinner" class="mt-4"><loading-animation /></div>
        </infinite-loading>

        <modal ref="transactionModal" @closed="closeModal">
            <div v-if="transaction" class="relative">
                <span v-if="transaction.is_possible_subscription">Our systems said this looks like a reoccurring {{ Number(transaction.amount) > 0 ? 'payment' : 'income'}}! Please set the record straight for us!</span>
                <div class="flex flex-wrap py-2" :class="{'border-t border-b': transaction.is_possible_subscription}">
                    <div class="w-full text-xl font-thin">{{ transaction.name }}</div>
                    <div class="w-full text-4xl p-0">{{ Number(transaction.amount) > 0 ? '-' : '' }}${{ Math.abs(Number(transaction.amount)).toFixed(2) }}</div>
                    <div class="w-full text-base p-0 ">{{ renderDate(transaction.date) }}</div>
                    <div class="w-full text-xs flex flex-wrap items-center border border-yellow-600 rounded bg-yellow-100 text-yellow-800 py-2 my-2" v-if="transaction.pending">
                        <zondicon title="Our system flagged this is a possible subscription!" class="mx-2 cursor-pointer w-3 h-3 fill-current text-yellow-600" icon="information-outline"/>
                        Pending
                    </div>
                    <div class="w-full text-base p-0 " v-if="transaction.categories">
                        Grouped in the categories: {{ transaction.categories.map(category => category.name).join(', ')}}
                    </div>
                </div>
                <div v-if="!shouldLabel" class="flex flex-wrap py-2 justify-between">
                    <button @click.prevent="nonRecurrent" class="border border-red-600 text-red-800 bg-red-100 rounded px-1">Not a bill</button>
                    <button @click.prevent="letsApplyLabel" class="border border-green-600 text-green-800 bg-green-100 rounded px-1">It's a bill!</button>
                </div>
                <div v-if="shouldLabel" class="flex flex-col w-full">
                    <div>
                        <div class="text-xs font-bold text-gray-700">The type of {{ Number(transaction.amount) > 0 ? 'payment' : 'income'}}.</div>
                        <select v-model="form.type" class="w-full mb-4 p-2 rounded text-gray-800 bg-gray-200">
                            <option value="subscription">Subscription</option>
                            <option value="bill">Bill/Utility</option>
                            <option value="income">Income</option>
                        </select>
                    </div>

                    <div>
                        <div class="text-xs font-bold text-gray-700">We will divide this evenly in the period.</div>
                        <select v-model="form.interval" class="w-full mb-4 p-2 rounded text-gray-800 bg-gray-200">
                            <option value="1">Once</option>
                            <option value="2">Twice</option>
                            <option value="3">Three times</option>
                        </select>
                    </div>

                    <div>
                        <div class="text-xs font-bold text-gray-700">Frequency in which this {{ form.type }} occurs.</div>
                        <select v-model="form.frequency" class="w-full mb-4 p-2 rounded text-gray-800 bg-gray-200">
                            <option value="YEARLY">Yearly</option>
                            <option value="MONTHLY">Monthly</option>
                            <option value="WEEKLY">Weekly</option>
                            <option value="DAILY">Daily</option>
                            <option value="MINUTELY">Minutely</option>
                            <option value="SECONDLY">Secondly</option>
                        </select>
                    </div>
                    <button @click.prevent="saveTheLabel" class="border border-green-600 bg-green-500 text-white rounded px-2 py-1">Lets Save It!</button>
                </div>
            </div>
        </modal>
    </div>
</template>

<script>
    import InfiniteLoading from 'vue-infinite-loading';
    export default {
        props: ['user'],
        components: {
            InfiniteLoading,
        },
        data() {
            return {
                loading: true,
                transactions: [],
                pagination: {},
                search: '',
                modal: false,
                page: 1,
                transaction: null,
                shouldProvideLabel: false,
                possibleSubscriptions: false,
                form: {
                    type: 'subscription',
                    interval: 1,
                    frequency: 'MONTHLY',
                    user_id: this.user.id
                }
            }
        },
        methods: {
            isPositive(transaction) {
                return Number(transaction.amount) < 0;
            },
            getTransactions($state, extraFilters = {}) {
                if (this.search) {
                    this.page = 1;
                    this.transactions = [];
                    extraFilters['q'] = this.search;
                }

                if (this.possibleSubscriptions) {
                    extraFilters['null'] = 'is_subscription,is_possible_subscription'
                }

                axios.get(buildUrl('/abstract-api/transactions', {
                    page: this.page + '',
                    include: '',
                    filter: extraFilters,
                    sort: '-date',
                    action: 'paginate:30'
                }))
                    .then(({ data }) => {
                        this.transactions = this.transactions.concat(data.data)
                        delete data.data;
                        this.pagination = data;
                        this.loading = false;
                        if (data.next_page_url) {
                            this.page++;
                            $state && !$state.code && $state.loaded();

                        } else {
                            $state && !$state.code && $state.complete();
                        }
                    })
            },
            closeModal() {
                this.$refs.transactionModal.hide();
                this.transaction = null;
                this.shouldProvideLabel = false;
            },
            showModal(transaction) {
                if (this.$refs.transactionModal.localShow) {
                    this.$refs.transactionModal.hide();
                    this.transaction = null;
                    this.shouldProvideLabel = false;
                } else {
                    this.$refs.transactionModal.show();
                    this.transaction = transaction;
                }
            },
            renderDate(date) {
                return moment(date).format('MMMM Do YYYY, h:mm:ss a')
            },
            nonRecurrent() {
                axios.put('/abstract-api/transactions/' + this.transaction.id, {
                    is_possible_subscription: false,
                    is_subscription: false,
                })
                    .then(({ data }) => {
                        this.page = 1;
                        this.transactions = [];
                        this.getTransactions();
                        this.closeModal();
                    })
            },
            saveTheLabel() {
                axios.put('/abstract-api/transactions/' + this.transaction.id, Object.assign({
                    is_possible_subscription: false,
                    is_subscription: true,
                }, this.form))
                    .then(({ data }) => {
                        this.page = 1;
                        this.transactions = [];
                        this.getTransactions();
                        this.closeModal();
                    })
            },
            letsApplyLabel() {
                this.shouldProvideLabel = true;
            },
            togglePossibleSubscriptionSearch(toggled) {
                this.possibleSubscriptions = toggled;
                this.page = 1;
                this.transactions = [];
                this.getTransactions();
            },

            isCurrentBillCycle(transaction) {
                let next_bill_due_date = moment(transaction.date);

                let now = moment();

                return next_bill_due_date.format('YYYY-MM') === now.format('YYYY-MM');
            }
        },
        computed: {
            shouldLabel () {
                if (!this.transaction) {
                    return false;
                }

                return this.shouldProvideLabel;
            }
        },
        mounted() {
        }
    }
</script>
