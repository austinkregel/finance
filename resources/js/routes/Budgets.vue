<style lang="scss">
    progress[value] {
        appearance: none;
        background: red;
    }
    progress[value]::-webkit-progress-bar {
        background: red;
    }
</style>
<template>
    <div>
        <div class="mx-2 mb-4 flex flex-wrap items-center">
            <div class="flex items-center justify-between w-full">
               <div class="flex flex-col w-2/3">
                   <div class="text-xs mx-2" v-dark-mode-light-gray-text>
                       A budget is a dollar amount and time period for one or more groups...
                   </div>
               </div>

                <div class="flex justify-end">
                    <budget-modal />
                </div>
            </div>
        </div>
        <div class="rounded-lg flex flex-wrap" v-if="!loading">
            <div v-for="budget in data" class="flex-1 max-w-1/2">
                <div v-dark-mode-white-background class="mx-4 rounded shadow">

                    <div class="pt-4 px-2 mt-4 mx-2  relative">
                        <budget-modal :budget="budget" class="flex"/>
                        <div class="font-bold text-2xl tracking-wide">
                            ${{ (Math.round(budget.total_spend * 100)/ 100).toLocaleString() }}
                        </div>
                        <div class="flex w-full items-center mt-2">
                            <div class="h-full w-full bg-gray-300 rounded">
                                <div class="h-full text-xs rounded leading-none py-1 text-center text-white" :class="budget.total_spend > budget.amount ? 'bg-red-400' : 'bg-blue-400'" :style="'max-width: 100%; width: ' + budgetSpendsFormat(budget) + '%;'"></div>
                            </div>
                            <div v-if="false" class="ml-4" :class="budget.total_spend > budget.amount ? 'w-32' : ''">
                                {{ budgetSpendsFormat(budget) }}% <span v-if="budget.total_spend > budget.amount"> over</span>
                            </div>
                        </div>
                        <div class="flex justify-between w-full mt-2" v-dark-mode-light-text>
                            <div class="uppercase font-bold">total {{ budget.name }} budget</div>
                            <div class="tracking-wide">${{ Math.round(budget.amount).toLocaleString() }}</div>
                        </div>
                    </div>
                    <div class="w-full overflow-hidden">
                        <div class="flex flex-wrap pb-4 rounded-b">
                            <div v-for="(tag, $i) in budget.tags" class="px-2 py-1 rounded mt-4 text-center ml-4" v-dark-mode-input>
                                {{ tag.name.en }}
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div v-else class="flex w-full items-center justify-center">
            <loading-animation :loading="loading" description="Loading budgets..."/>
        </div>
    </div>
</template>

<script>
    import InfiniteLoading from 'vue-infinite-loading';
    import Kpi from "../components/Utils/Kpi";
    import BudgetModal from "../components/BudgetModal";
    import LoadingAnimation from "../components/Utils/LoadingAnimation";
    export default {
        components: {
            LoadingAnimation,
            BudgetModal,
            Kpi,
            InfiniteLoading,
        },
        data() {
            return {
                modals: {
                    group: false,
                },
            }
        },
        computed: {
            data() {
                return this.$store.getters.budgets.data;
            },
            loading() {
                return this.$store.getters.budgets.loading;
            }
        },
        methods: {
            async fetchBudgets($state, options) {
                try {
                    await this.$store.dispatch('fetchBudgets', {
                        page: this.$store.getters.budgets.meta.current_page + 1
                    })
                } finally {
                    $state.loaded();

                    if (this.$store.getters.budgets.meta.current_page >= this.$store.getters.budgets.meta.last_page) {
                        $state.complete();
                    }
                }
            },
            budgetSpendsFormat(budget) {
                return Math.round((budget.total_spend/budget.amount) * 100)
            }
        },
    }
</script>
