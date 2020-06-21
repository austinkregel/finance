<template>
    <div>
        <button class="outline-none" @click="openModal">
            <svg class="w-4 h-4 mr-6 absolute right-0" fill="currentColor" viewBox="0 0 20 20"><path d="M13.586 3.586a2 2 0 112.828 2.828l-.793.793-2.828-2.828.793-.793zM11.379 5.793L3 14.172V17h2.828l8.38-8.379-2.83-2.828z"></path></svg>
        </button>

        <modal ref="groupingModal" @closed="closeModal">
            <div class="flex flex-wrap" v-dark-mode-dark-text>
                <div class="w-full">
                    <label class="block uppercase tracking-wide text-xs font-bold mb-2">
                        Group Name
                    </label>
                    <input v-model="form.name" class="appearance-none block w-full rounded py-3 px-4 leading-tight focus:outline-none" type="text" placeholder="video games" v-dark-mode-input />
                </div>

                <div class="w-full py-4 ">
                    <div class="block uppercase tracking-wide text-xs font-bold mb-2">
                        conditionals
                        <div class="text-xs font-normal normal-case" v-dark-mode-light-gray-text>
                            Adding conditionals to groups allows our system to automatically apply these groups to your transactions.
                            Not adding any conditionals will automatically apply the group to all transactions.
                        </div>
                    </div>

                    <div class="flex w-full mt-4" v-for="condition in form.conditionals">
                        <div class="flex-1">
                            <div class="block uppercase tracking-wide text-xs mb-2 font-semibold">
                                Parameter
                                <div class="text-xs font-normal tracking-tight" v-dark-mode-light-text>
                                    The field who's value we will compare against
                                </div>
                            </div>
                            <select v-model="condition.parameter" class="appearance-none block w-full rounded py-1 px-2 leading-tight focus:outline-none" v-dark-mode-input>
                                <option value="name">name</option>
                                <option value="amount">amount</option>
                                <option value="account.name">account.name</option>
                                <option value="date">date</option>
                                <option value="pending">pending</option>
                                <option value="category.name">category.name</option>
                            </select>
                        </div>
                        <div class="flex-1 ml-4">
                            <div class="block uppercase tracking-wide text-xs mb-2 font-semibold">
                                Comparator
                                <div class="text-xs font-normal tracking-tight" v-dark-mode-light-text>
                                    How we compare the parameter to the value
                                </div>
                            </div>

                            <select v-model="condition.comparator" class="appearance-none block w-full rounded py-1 px-2 leading-tight focus:outline-none" v-dark-mode-input>
                                <option value="EQUAL">EQUAL</option>
                                <option value="NOT_EQUAL">NOT_EQUAL</option>
                                <option value="LIKE">LIKE</option>
                                <option value="NOT_LIKE">NOT_LIKE</option>
                                <option value="IN">IN</option>
                                <option value="NOT_IN">NOT_IN</option>
                                <option value="IN_LIKE">IN_LIKE</option>
                                <option value="NOT_IN_LIKE">NOT_IN_LIKE</option>
                                <option value="STARTS_WITH">STARTS_WITH</option>
                                <option value="ENDS_WITH">ENDS_WITH</option>
                                <option value="LESS_THAN">LESS_THAN</option>
                                <option value="LESS_THAN_EQUAL">LESS_THAN_EQUAL</option>
                                <option value="GREATER_THAN">GREATER_THAN</option>
                                <option value="GREATER_THAN_EQUAL">GREATER_THAN_EQUAL</option>
                            </select>
                        </div>
                        <div class="flex-1 ml-4">
                            <div class="block uppercase tracking-wide text-xs mb-2 font-semibold">
                                Value
                                <div class="text-xs font-normal tracking-tight" v-dark-mode-light-text>
                                    What we are comparing the transaction to
                                </div>
                            </div>
                            <input v-model="condition.value" class="appearance-none block w-full rounded py-1 px-2 leading-tight focus:outline-none" type="text" placeholder="STEAMGAMES.COM" v-dark-mode-input/>
                        </div>
                        <div class="w-6 ml-2 flex justify-center items-end mb-1">
                            <button class="text-red-600 h-6" @click.prevent="() => deleteCondition(condition)">
                                <svg class="w-6" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M9 2a1 1 0 00-.894.553L7.382 4H4a1 1 0 000 2v10a2 2 0 002 2h8a2 2 0 002-2V6a1 1 0 100-2h-3.382l-.724-1.447A1 1 0 0011 2H9zM7 8a1 1 0 012 0v6a1 1 0 11-2 0V8zm5-1a1 1 0 00-1 1v6a1 1 0 102 0V8a1 1 0 00-1-1z" clip-rule="evenodd"></path></svg>
                            </button>
                        </div>
                    </div>
                    <div class="w-full text-xs" v-dark-mode-light-text>
                        Remember, ALL of the conditionals must be true for the group to be applied.
                    </div>
                    <button @click="addCondition" class="mt-4 px-2 py-1 text-sm focus:outline-none rounded-lg flex items-center hover:shadow" v-dark-mode-button>
                        <zondicon icon="add-outline" class="fill-current w-4 h-4" />
                        <span class="ml-2">Add condition</span>
                    </button>
                </div>

                <div class="w-full py-4">
                    <button @click.prevent="saveGroup" class="py-2 px-4 border-2 border-transparent focus:outline-none text-white bg-blue-700 rounded-lg flex items-center hover:bg-white hover:border-blue-700 hover:text-blue-700 hover:shadow">
                        <zondicon v-if="saving" icon="refresh" class="w-4 h-4 rotate" />
                        Sav<span v-if="!saving">e</span><span v-else>ing</span>
                    </button>
                </div>
            </div>
        </modal>
    </div>
</template>

<script>
    export default {
        props: ['tag'],
        data() {
            return {
                form: {
                    name: '',
                    conditionals: [],
                },
                saving: false
            }
        },
        methods: {
            closeModal() {
                this.$refs.groupingModal.hide();
            },
            openModal() {
                this.form = Object.assign({}, {
                    name: this.tag.name.en,
                    conditionals: this.tag.conditionals.map(item => Object.assign({}, item)),
                })

                this.$refs.groupingModal.show();
            },
            addCondition() {
                this.form.conditionals.push({
                    parameter: 'name',
                    comparator: 'EQUAL',
                    value: '',
                })
            },
            async saveGroup() {
                this.saving = true;

                await this.$store.dispatch('updateGroup', {
                    original: this.tag,
                    updated: this.form
                });

                this.saving = false;

                setTimeout(() => this.closeModal(), 300);
            },
            async deleteCondition(condition) {
                if (!condition.id) {
                    this.form = {
                        ...this.form,
                        conditionals: this.form.conditionals.filter(con => !(
                            con.value === condition.value && con.parameter === condition.parameter && con.comparator === condition.comparator
                        ))
                    }
                    return;
                }
                await this.$store.dispatch('deleteGroupCondition', {
                    tag: this.tag,
                    condition
                });

                setTimeout(() => this.closeModal(), 300);
                await this.$store.dispatch('fetchGroups');
            },
        }
    }
</script>

<style scoped>

</style>
