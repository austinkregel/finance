<style scoped>

</style>

<template>
    <div class="shadow bg-white rounded m-4 p-2 relative flex border-t-2 border-transparent" :class="{'border-t-2 border-red-500': !isCurrentBillCycle && !latestTransaction.pending, 'border-t-2 border-orange-500': latestTransaction.pending, 'border-t-2 border-green-500': isCurrentBillCycle, }">
        <zondicon icon="user" class="text-blue-500 w-6 h-6 mr-2 my-2 fill-current"/>
        <div class="flex-grow" style="max-width: calc(100% - 2rem);">
            <div class="flex flex-wrap items-end justify-between w-full">
                <span class="font-bold w-3/4 truncate text-xl text-gray-800">{{ bill.name }}</span>
                <span class="text-xl w-1/4 text-right text-gray-600">${{ bill.amount }}</span>
            </div>

            <div class="flex flex-wrap justify-between text-xs text-gray-900 tracking-wider w-full">
                <span class="font-thin w-3/4 truncate">{{ bill.account.name }} / {{ bill.account.official_name }}</span>
                <span class="font-thin w-1/4 text-right text-gray-600">{{ bill.next_due_date }}</span>
            </div>

            <div class="flex flex-wrap w-full">
                <div v-if="isCurrentBillCycle" class="flex text-xs text-green-700 tracking-wide flex-wrap items-center">
                    <div class="w-2 h-2 mr-1 bg-green-600 tracking-tight rounded-full"></div>
                    Paid
                </div>
                <div v-else-if="isPending" class="flex text-xs text-orange-700 flex-wrap tracking-wide items-center">
                    <div class="w-2 h-2 mr-1 bg-orange-600 rounded-full"></div>
                    Payment pending
                </div>
                <div v-else class="flex text-xs text-red-700 flex-wrap tracking-wide items-center">
                    <div class="w-2 h-2 mr-1 bg-red-600 rounded-full"></div>
                    Awaiting payment
                </div>
                <div class="text-xs pl-2 flex-grow text-gray-500">{{ latestTransaction.start }}</div>
                <button @click.prevent="openEditModal">
                    <zondicon icon="edit-pencil" class="w-3 h-3 fill-current text-blue-500" />
                </button>
            </div>
        </div>

        <modal ref="billModal" @closed="closeModal">
            <div v-if="bill" class="relative">
                <div class="mb-4">
                    <label class="block text-gray-700 text-sm font-bold mb-2">
                        Bill Name
                        <input v-model="form.name" class="block appearance-none w-full bg-white border border-gray-200 text-gray-700 py-3 px-4 pr-8 rounded leading-tight focus:outline-none focus:bg-white focus:border-gray-500" />
                    </label>

                </div>

                <div class="relative mb-4">
                    <label class="block text-gray-700 text-sm font-bold mb-2">
                        Bill Type
                        <select v-model="form.type" class="block appearance-none w-full bg-white border border-gray-200 text-gray-700 py-3 px-4 pr-8 rounded leading-tight focus:outline-none focus:bg-white focus:border-gray-500" >
                            <option value="subscription">Subscription</option>
                            <option value="bill">Bill/Utility</option>
                            <option value="income">Income</option>
                        </select>
                    </label>

                    <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center px-2 text-gray-700">
                        <svg class="fill-current h-4 mt-6 mr-2 w-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20"><path d="M9.293 12.95l.707.707L15.657 8l-1.414-1.414L10 10.828 5.757 6.586 4.343 8z"/></svg>
                    </div>
                </div>

                <div class="relative mb-4">
                    <label class="block text-gray-700 text-sm font-bold mb-2">
                        Bill Frequency
                        <select v-model="form.frequency" class="block appearance-none w-full bg-white border border-gray-200 text-gray-700 py-3 px-4 pr-8 rounded leading-tight focus:outline-none focus:bg-white focus:border-gray-500">
                            <option value="YEARLY">Yearly</option>
                            <option value="MONTHLY">Monthly</option>
                            <option value="WEEKLY">Weekly</option>
                            <option value="DAILY">Daily</option>
                            <option value="MINUTELY">Minutely</option>
                            <option value="SECONDLY">Secondly</option>
                        </select>
                    </label>

                    <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center px-2 text-gray-700">
                        <svg class="fill-current h-4 mt-6 mr-2 w-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20"><path d="M9.293 12.95l.707.707L15.657 8l-1.414-1.414L10 10.828 5.757 6.586 4.343 8z"/></svg>
                    </div>
                </div>

                <div class="mb-4">
                    <label class="block text-gray-700 text-sm font-bold mb-2">
                        Bill Cost
                        <input v-model="form.amount" class="block appearance-none w-full bg-white border border-gray-200 text-gray-700 py-3 px-4 pr-8 rounded leading-tight focus:outline-none focus:bg-white focus:border-gray-500" type="text" placeholder="40.19">
                    </label>

                </div>

                <div class="mb-4">
                    <div class="flex flex-wrap justify-end">
                        <button class="bg-blue-500 py-2 px-4 text-white rounded shadow" @click.prevent="updateBill">
                            Save
                        </button>
                    </div>
                </div>

                <div class="w-full flex flex-col" v-if="bill.five_transactions">
                    <div class="flex w-full" v-for="transaction in bill.five_transactions">
                        <single-transaction class="w-full" :transaction="transaction"></single-transaction>
                    </div>
                </div>
            </div>
        </modal>
    </div>
</template>

<script>
    export default {
        props: ['bill'],

        data() {
            return {
                form: {},
                showModal: false
            }
        },

        computed: {
            latestTransaction() {
                if (!this.bill.five_transactions) {
                    return {};
                }

                return this.bill.five_transactions[0] || {};
            },
            isCurrentBillCycle() {

                let next_bill_due_date = moment.utc(this.latestTransaction.date);

                let this_month_due_date = moment.utc(this.bill.current_due_date);

                // We want to get the diff from previous bill to the next to determine if it's actually been paid.
                return this_month_due_date.diff(next_bill_due_date, 'days') < 28
            },
            isPending() {
                return this.latestTransaction.pending;
            }
        },

        methods: {
            openEditModal() {
                this.showModal = !this.showModal;
                if (this.showModal) {
                    this.form = Object.assign(this.bill);
                    this.$refs.billModal.show();
                } else {
                    this.$refs.billModal.hide();
                }
            },
            closeModal() {
                this.showModal = false;
            },
            updateBill() {
                axios.put('/api/subscriptions/' + this.bill.id, this.form)
                    .then(({ data }) => {
                        this.form = data
                        Bus.$emit('updateSubscriptions')
                    })
            }
        },

        mounted() {

        }
    }
</script>
