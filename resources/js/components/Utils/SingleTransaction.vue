<style scoped>

</style>

<template>
    <div class="flex flex-col shadow rounded p-4 m-2 bg-white" :class="{'text-green-900 ': isPositive, ' text-red-900': !isPositive }"
         :style="(isPositive ? 'border-top: solid #38A169 2px;' : 'border-top: solid #E53E3E 2px;') "
    >
        <div class="flex justify-between items-center -mb-1" :class="{'text-green-800': isPositive, 'text-red-800': isPositive }">
            <div class="font-bold text-lg truncate pr-2 tracking-normal">{{ actualTransaction.name }}</div>
            <div class="font-bold text-lg">${{ Number(-actualTransaction.amount).toFixed(2) }}</div>
        </div>

        <div class="flex justify-between items-center">
            <div class="text-xs flex-grow tracking-wide">{{ actualTransaction.account.name }} / {{ actualTransaction.account.official_name }}</div>
            <div class="text-xs tracking-wide">{{ actualTransaction.start }}</div>
        </div>

        <div class="flex justify-between items-center">
            <div class="text-xs flex-grow tracking-wide">
                <span v-if="actualTransaction.bill_name">{{ actualTransaction.bill_name.type }},</span>
                {{ actualTransaction.categories.map(cat => cat.name).join(', ') }}
            </div>

            <div class="text-xs font-bold tracking-wide mr-px">
                <div v-if="actualTransaction.pending" class="text-orange-600 flex flex-wrap items-center">
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
</template>

<script>
    export default {
        props: ['transaction'],

        data() {
            return {
                trans: null,
            }
        },
        computed: {
            isPositive() {
                return Number(this.actualTransaction.amount) < 0;
            },
            actualTransaction() {
                if (!this.trans) {
                    return {
                        account: {
                            name: ''
                        },
                        categories: []
                    };
                }

                return this.trans;
            }
        },
        mounted() {
            this.trans = Object.assign({
                account: {
                    name: ''
                },
                categories: [],
            }, this.transaction);
        }
    }
</script>
