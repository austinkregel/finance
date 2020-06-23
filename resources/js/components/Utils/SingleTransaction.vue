<style scoped>

</style>

<template>
    <div class="flex flex-col p-4" :class="{ 'border-t border-gray-300': index !== 0, 'bg-blue-100': selected }">
        <div class="flex justify-between items-center">
            <div class="p-2 rounded-full text-white" :style="'background: ' + primaryColor">
                <category-icon class="w-6 h6" :category="transaction.category"></category-icon>
            </div>
            <div class="font-bold text-lg flex-grow truncate ml-3 tracking-normal flex flex-col">
                <div>{{ transaction.name }}</div>
                <div class="text-sm font-normal" v-dark-mode-light-text>
                    {{ transaction.category.name }}
                    •
                    {{ date }}
                </div>
                <div class="flex items-center -ml-2">
                    <div v-for="tag in transaction.tags" class="ml-2 text-xs bg-gray-300 text-black rounded-full px-1">{{ tag.name.en }}</div>
                </div>
            </div>
            <div class="flex flex-col text-right">
                <span class="font-bold text-lg" :class="{ 'text-green-500': isPositive, 'text-red-500': !isPositive }">
                    ${{ Math.abs(transaction.amount).toFixed(2) }}
                </span>
                <span class="font-normal text-sm" v-dark-mode-light-text>{{ account.name }}</span>
            </div>
        </div>
    </div>
</template>

<script>
    export default {
        props: ['transaction', 'index'],
        computed: {
            isPositive() {
                return Number(this.transaction.amount) < 0;
            },
            date() {
                return dayjs(this.transaction.date).formatToLocaleTimezone()
            },
            account() {
                return this.$store.getters.accountsById[this.transaction.account_id]
            },
            institution() {
                if (!this.account.token.institution) {
                    return null;
                }

                return this.account.token.institution
            },
            primaryColor() {
                if (!this.institution) {
                    return '#4299e1';
                }

                return this.institution.primary_color
            },
            selected() {
                return this.$store.getters.selectedTransactions[this.transaction.id]
            }
        },
        methods: {
            selectTransaction() {
                this.$store.commit('select', this.transaction)
            }
        },
        mounted() {
        }
    }
</script>
