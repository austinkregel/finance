<template>
    <div class="container mx-auto">
        <div class="w-full flex flex-wrap">
            <kpi-url
                class="w-full md:w-1/2 lg:w-1/3"
                :url="billsThisMonth"
                :previous-url="billsLastMonth"
                description="Bill payments to date"
                icon="metric"
                inverse
                label="$"
            ></kpi-url>

            <kpi-url
                class="w-full md:w-1/2 lg:w-1/3"
                :url="subscriptionsThisMonth"
                :previous-url="subscriptionsLastMonth"
                description="Sub payments to date"
                icon="metric"
                inverse
                label="$"
            ></kpi-url>

            <kpi-url
                class="w-full md:w-1/2 lg:w-1/3"
                :url="moneySpentToDate"
                :previous-url="moneySpentLastMonth"
                description="Money spent month to date"
                label="$"
                inverse
                icon="metric"
            ></kpi-url>

            <kpi-url
                class="w-full md:w-1/2 lg:w-1/3"
                :url="accountBalance"
                :previous-url="accountBalanceLastMonth"
                description="Total account balance"
                label="$"
                inverse
            ></kpi-url>
            <card-loading-animation class="w-full md:w-1/2 lg:w-1/3 flex flex-col" :loading="spendsToDate.loading">
                <kpi
                    :decrease-description="'$' + spendsToDate.previousValue + ' ' + spendsToDate.header"
                    :value="'$'+spendsToDate.value"
                    :description="spendsToDate.description"
                    :color="spendsToDate.value > spendsToDate.previousValue ? 'red' : 'green'"
                ></kpi>
            </card-loading-animation>
            <card-loading-animation class="w-full md:w-1/2 lg:w-1/3 flex flex-col" :loading="spendsToDate.loading">
                <kpi
                    :decrease-description="'$' + feesToDate.previousValue + ' ' + feesToDate.header"
                    :value="'$'+feesToDate.value"
                    :description="feesToDate.description"
                    :color="feesToDate.value > feesToDate.previousValue ? 'red' : 'green'"
                ></kpi>
            </card-loading-animation>
        </div>
        <div>
            <transactions :user="user" />
        </div>
    </div>
</template>

<script>
    import CardLoadingAnimation from "../Utils/CardLoadingAnimation";
    export default {
        components: {CardLoadingAnimation},
        props: ['user'],
        data: () => ({
            moment,
            buildUrl,
            spendsToDate: {
                value: 0,
                previousValue: 0,
                header: "Last month income",
                description: "Spends / Income",
                loading: true
            },
            feesToDate: {
                value: 0,
                previousValue: 0,
                header: "Fees last month",
                description: "Fees this month",
                loading: true
            }
        }),
        computed: {
            billsThisMonth() {
                return buildUrl('/api/subscriptions2', {
                    filter: {
                        due_currently: 'between:'+ moment().startOf('month').toISOString() + ',' + moment().endOf('day').toISOString(),
                        type: 'eq:bill'
                    },
                    action: 'sum:amount'
                })
            },
            billsLastMonth() {
                return buildUrl('/api/transactions', {
                    filter: {
                        between: moment().subtract(1, 'month').startOf('month').toISOString() + ',' + moment().subtract(1, 'month').endOf('month').toISOString(),
                        'subscription.type': 'bill'
                    },
                    action: 'sum:amount'
                })
            },
            subscriptionsThisMonth() {
                return buildUrl('/api/subscriptions2', {
                    filter: {
                        due_currently: 'between:'+ moment().startOf('month').toISOString() + ',' + moment().toISOString(),
                        type:'eq:subscription'
                    },
                    action: 'sum:amount'
                })
            },
            subscriptionsLastMonth() {
                return buildUrl('/api/transactions', {
                    filter: {
                        between: moment().subtract(1, 'month').startOf('month').toISOString() + ',' + moment().subtract(1, 'month').endOf('month').toISOString(),
                        'subscription.type': 'subscription'
                    },
                    action: 'sum:amount'
                })
            },
            moneySpentToDate() {
                return buildUrl('/api/transactions', {
                    filter:{
                        after: moment().startOf('month').toISOString(),
                        no_income: true,
                        withoutFeesOrTransfers: true
                    },
                    action: 'sum:amount'
                })
            },
            moneySpentLastMonth() {
                return buildUrl('/api/transactions', {
                    filter:{
                        between: moment().subtract(1, 'month').startOf('month').toISOString() + ',' + moment().subtract(1, 'month').endOf('month').toISOString(),
                        no_income: true,
                    },
                    action: 'sum:amount'
                })
            },
            accountBalance() {
                return buildUrl('/api/accounts', {
                    filter: {
                        currentUser: true,
                        type: 'depository',
                    },
                    action: 'sum:balance'
                })
            },
            accountBalanceLastMonth() {
                return buildUrl('/api/account-kpis', {
                    filter: {
                        date: moment.utc().subtract(1, 'month'),
                    },
                    action: 'sum:balance'
                });
            },
        },
        methods: {
            async getSpendsToDate() {
                let income = await axios.get(buildUrl('/api/transactions', {
                    filter: {
                        between: moment.utc().startOf('month').startOf('month').toISOString()
                            + ',' + moment.utc().startOf('month').endOf('month').toISOString(),
                        has: 'subscription',
                        'subscription.type': 'income'
                    },
                    action: 'sum:amount'
                }))

                let spends = await axios.get(buildUrl('/api/transactions', {
                    filter: {
                        between: moment.utc().startOf('month').subtract(1, 'month').startOf('month').toISOString()
                            + ',' + moment.utc().startOf('month').subtract(1, 'month').endOf('month').toISOString(),
                        has: 'subscription',
                        withoutFeesOrTransfers: true,
                        no_income: true,
                    },
                    action: 'sum:amount'
                }))

                this.spendsToDate = Object.assign(this.spendsToDate, {
                    previousValue: Math.abs(income.data),
                    value: Math.abs(spends.data),
                    loading: false
                })

            },
            async getFeesToDate() {
                let income = await axios.get(buildUrl('/api/transactions', {
                    filter: {
                        between: moment.utc().startOf('month').startOf('month').toISOString()
                            + ',' + moment.utc().startOf('month').endOf('month').toISOString(),
                        withFeesAndTransfers: true,
                    },
                    action: 'sum:amount'
                }));

                let spends = await axios.get(buildUrl('/api/transactions', {
                    filter: {
                        between: moment.utc().startOf('month').subtract(1, 'month').startOf('month').toISOString()
                            + ',' + moment.utc().startOf('month').subtract(1, 'month').endOf('month').toISOString(),
                        withFeesAndTransfers: true,
                    },
                    action: 'sum:amount'
                }));

                this.feesToDate = Object.assign(this.feesToDate, {
                    previousValue: Math.abs(income.data),
                    value: Math.abs(spends.data),
                    loading: false
                })

            }
        },
        mounted() {
            this.getSpendsToDate();
            this.getFeesToDate();
        }
    }
</script>
