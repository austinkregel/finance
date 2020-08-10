<template>
    <div>
        <button v-if="!budget" @click="openModal" class="p-2 ml-4 focus:outline-none rounded-lg flex items-center hover:shadow" v-dark-mode-button>
            <zondicon icon="add-outline" class="w-6 h-6 fill-current" />
            <span class="ml-2 font-medium">New Budget</span>
        </button>

        <button v-else class="outline-none" @click="openModal">
            <svg class="w-4 h-4 mr-2 absolute right-0" fill="currentColor" viewBox="0 0 20 20"><path d="M13.586 3.586a2 2 0 112.828 2.828l-.793.793-2.828-2.828.793-.793zM11.379 5.793L3 14.172V17h2.828l8.38-8.379-2.83-2.828z"></path></svg>
        </button>

        <modal ref="budgetingModal" @closed="closeModal">
            <div class="flex flex-wrap">
                <div class="w-full">
                    <label class="block uppercase tracking-wide text-xs font-bold mb-2" v-dark-mode-dark-text>
                        Budget Name
                    </label>
                    <input v-model="form.name" class="appearance-none block w-full  rounded py-3 px-4 leading-tight focus:outline-none" type="text" placeholder="bills" v-dark-mode-input/>
                </div>

                <div class="w-full mt-4">
                    <label class="block uppercase tracking-wide text-xs font-bold mb-2" v-dark-mode-dark-text>
                        Budget Amount
                    </label>
                    <input v-model="form.amount" class="appearance-none block w-full mt-2 rounded py-3 px-4 leading-tight focus:outline-none" type="number" :placeholder="`38.30`" v-dark-mode-input/>
                </div>

                <div class="w-full mt-4">
                    <label class="block uppercase tracking-wide text-xs font-bold mb-2" v-dark-mode-dark-text>
                        Interval between budget period resets. (think how many periods must pass before we set the "spent" amount to 0, like the start of a new month or week)
                    </label>
                    <input v-model="form.interval" min="0" max="50" class="appearance-none block w-full mt-2 rounded py-3 px-4 leading-tight focus:outline-none" type="number" :placeholder="`1`" v-dark-mode-input />
                </div>

                <div class="w-full mt-4">
                    <label class="block uppercase tracking-wide text-xs font-bold mb-2" v-dark-mode-dark-text>
                        Budget Period.
                    </label>
                    <select v-model="form.frequency" class="appearance-none block w-full mt-2 rounded py-3 px-4 leading-tight focus:outline-none" v-dark-mode-input>
                        <option value="DAILY">Daily</option>
                        <option value="WEEKLY">Weekly</option>
                        <option value="MONTHLY">Monthly</option>
                        <option value="YEARLY">Yearly</option>
                    </select>
                </div>

                <label>
                    <input type="checkbox" v-model="limited"> Does this budget end?
                </label>

                <div class="w-full mt-4" v-if="limited">
                    <label class="block uppercase tracking-wide text-xs font-bold mb-2" v-dark-mode-dark-text>
                        A limited-time budget: the number of times the the period must occur before resetting the spent amount to 0
                    </label>
                    <input v-model="form.count" min="0" max="999" class="appearance-none block w-full mt-2 rounded py-3 px-4 leading-tight focus:outline-none" type="number" :placeholder="`1`" v-dark-mode-input />
                </div>
                <div class="w-full mt-4">
                    <label class="block uppercase tracking-wide text-xs font-bold mb-2" v-dark-mode-dark-text>
                        Date this budget starts
                    </label>
                    <date-picker dark-mode :id="budget ? budget.id : false" :value="budget ? budget.started_at: null"/>
                </div>

                <div class="w-full mt-4">
                    <div class="flex w-full" v-for="group in $store.getters.groups.data">
                        <label class="cursor-pointer">
                            <input type="checkbox" v-model="selectedTags" :value="group.id" /> {{ group.name.en }}
                        </label>
                    </div>
                </div>

                <div class="w-full py-4">
                    <button @click.prevent="saveBudget" class="py-2 px-4 border-transparent focus:outline-none rounded-lg flex items-center hover:shadow" v-dark-mode-button>
                        <zondicon v-if="saving" icon="refresh" class="w-4 h-4 rotate" />
                        Sav<span v-if="!saving">e</span><span v-else>ing</span>
                    </button>
                </div>
            </div>
        </modal>
    </div>
</template>

<script>
    import DatePicker from "../settings/Plaid/DatePicker";
    export default {
        components: {DatePicker},
        props: ['budget'],
        data() {
            return {
                form: {
                    id: undefined,
                    name: '',
                    frequency: 'MONTHLY',
                    interval: 0,
                    amount: 100,
                    count: 12,
                    started_at: new Date
                },
                selectedTags: [],
                limited: false,
                saving: false,
                displayExample: false,
            }
        },
        methods: {
            closeModal() {
                this.$refs.budgetingModal.hide();
            },
            openModal() {
                if (this.budget ) {
                    this.form = Object.assign({}, {
                        ...this.budget,
                        started_at: dayjs(this.budget.started_at)
                    })

                    this.selectedTags = this.budget.tags.map(tag => tag.id)
                }

                this.$refs.budgetingModal.show();
            },
            async saveBudget() {
                this.saving = true;

                if (this.form.id) {
                    await this.$store.dispatch('updateBudget', {
                        original: this.budget,
                        updated: this.form,
                        tags: this.selectedTags
                    });

                } else {
                    await this.$store.dispatch('saveBudget', this.form);
                }
                this.saving = false;

                setTimeout(() => this.closeModal(), 300);
            },
            async deleteCondition(condition) {
                if (!condition.id) {
                    this.form = {
                        ...this.form,
                    }
                    return;
                }
                await this.$store.dispatch('deleteBudgetCondition', {
                    budget: this.budget,
                });

                setTimeout(() => this.closeModal(), 300);
                await this.$store.dispatch('fetchBudgets');
            },
        },
        mounted() {
            // This is a big problem. This isn't a reliable way to handle multiple dates when we display
            // one modal per budget... This event will get overwritten by the last modal.
            const id = this.budget ? '.'+this.budget.id : ''
            Bus.$off('chosenDate'+ id)
            Bus.$on('chosenDate'+ id, (date) => {
                this.form = {
                    ...this.form,
                    started_at: dayjs(date)
                }
            })
        }
    }
</script>

<style scoped>

</style>
