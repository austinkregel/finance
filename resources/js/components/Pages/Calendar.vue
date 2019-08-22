<style scoped>

</style>

<template>
    <div class="container mx-auto">
        <div class="m-4">
            <div class="w-full">
                Known spends per month <span class="font-bold">${{ (paymentsAtBeginning + paymentsAtEnding).toFixed(2) }} /  {{ Math.abs(income).toFixed(2) }} (based on last month)</span>
            </div>
            <FullCalendar ref="fullCalendar" :aspectRatio="16/9" defaultView="dayGridMonth" :plugins="calendarPlugins" :events="events" class="shadow bg-white rounded my-calendar-thing"/>
        </div>
        <div class="flex flex-wrap w-full">
            <div class="w-full text-xl mx-4">
                Payments in the beginning half of the month: <span class="font-bold">{{ paymentsAtBeginning }}</span>
            </div>
            <div class="w-full md:w-1/2 lg:w-1/3" v-for="(bill, $i) in subsStartOfMonth">
                <bill :bill="bill"></bill>
            </div>
        </div>
        <div class="flex flex-wrap w-full">
            <div class="w-full text-xl mx-4">
                Payments in the last half of the month: <span class="font-bold">{{ paymentsAtEnding }}</span>
            </div>
            <div class="w-full md:w-1/2 lg:w-1/3" v-for="(bill, $i) in subsToEndOfMonth">
                <bill :bill="bill"></bill>
            </div>
        </div>
    </div>
</template>

<script>
    import FullCalendar from '@fullcalendar/vue'
    import dayGridPlugin from '@fullcalendar/daygrid'
    import rrule from '@fullcalendar/rrule'

    export default {
        props: [],
        components: {
            FullCalendar // make the <FullCalendar> tag available
        },
        data() {
            return {
                events: [],
                calendarPlugins: [ dayGridPlugin, rrule ],
                subsStartOfMonth: [],
                subsToEndOfMonth: [],
                income: 0,
            }
        },

        computed: {
            paymentsAtBeginning() {
                return this.subsStartOfMonth.reduce((amount, transaction) => amount + transaction.amount, 0);
            },
            paymentsAtEnding() {
                return this.subsToEndOfMonth.reduce((amount, transaction) => amount + transaction.amount, 0);
            }
        },

        async mounted() {
            const { data } = await axios.get('/api/subscription-event');
            this.events = data;

            const {data: subsStartOfMonth} = await axios.get(buildUrl('/api/subscriptions2', {
                filter: {
                    'current_due_date': 'between:' + moment().startOf('month').toISOString()
                        + ','
                        + moment().startOf('month').add(13, 'days').endOf('day').toISOString(),
                    amount: '>:0'
                },
                include: 'account,fiveTransactions',
                action: 'values'
            }))
            const {data: subsToEndOfMonth} = await axios.get(buildUrl('/api/subscriptions2', {
                filter: {
                    'current_due_date': 'between:' + moment().startOf('month').add(14, 'days').startOf('day').toISOString()
                        + ','
                        + moment().endOf('month').endOf('day').toISOString(),
                    amount: '>:0'
                },
                include: 'account,fiveTransactions',
                action: 'values'
            }))

            const {data: incomes} = await axios.get(buildUrl('/api/transactions', {
                filter: {
                    between: moment.utc().startOf('month').subtract(1, 'month').startOf('month').toISOString()
                        + ',' + moment.utc().startOf('month').subtract(1, 'month').endOf('month').toISOString(),
                    has: 'subscription',
                    'subscription.type': 'income'
                },
                action: 'sum:amount'
            }))

            this.income = incomes;

            this.subsStartOfMonth = subsStartOfMonth
            this.subsToEndOfMonth = subsToEndOfMonth
        }
    }
</script>
