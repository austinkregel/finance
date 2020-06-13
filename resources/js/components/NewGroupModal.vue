<template>
    <div>
        <button @click="openModal" class="p-2 ml-4 border-2 border-blue-700 focus:outline-none text-blue-700 rounded-lg flex items-center hover:bg-blue-700 hover:text-white hover:shadow">
            <zondicon icon="add-outline" class="w-6 h-6 fill-current" />
            <span class="ml-2 font-medium">New Group</span>
        </button>

        <modal ref="groupingModal" @closed="closeModal">
            <div class="flex flex-wrap">
                <div class="w-full">
                    <label class="block uppercase tracking-wide text-gray-700 text-xs font-bold mb-2">
                        Group Name
                    </label>
                    <input v-model="form.name" class="appearance-none block w-full bg-gray-200 text-gray-700 border border-gray-200 rounded py-3 px-4 leading-tight focus:outline-none focus:bg-white focus:border-gray-500" type="text" placeholder="video games" />
                </div>

                <div class="w-full py-4 ">
                    <div class="block uppercase tracking-wide text-gray-700 text-xs font-bold mb-2">
                        Conditions
                        <div class="text-xs font-normal normal-case text-gray-500">
                            Adding conditions to groups allows our system to automatically apply these groups to your transactions.
                            Not adding any conditions will automatically apply the group to all transactions.
                        </div>
                    </div>

                    <div class="flex w-full mt-4" v-for="condition in form.conditions">
                        <div class="flex-1">
                            <div class="block uppercase tracking-wide text-gray-700 text-xs mb-2 font-semibold">
                                Parameter
                                <div class="text-xs font-normal tracking-tight text-gray-600">
                                    The field who's value we will compare against
                                </div>
                            </div>
                            <select v-model="condition.parameter" class="appearance-none block w-full bg-gray-200 text-gray-700 border border-gray-200 rounded py-1 px-2 leading-tight focus:outline-none focus:bg-white focus:border-gray-500">
                                <option value="name">name</option>
                                <option value="amount">amount</option>
                                <option value="account.name">account.name</option>
                                <option value="date">date</option>
                                <option value="pending">pending</option>
                                <option value="category.name">category.name</option>
                            </select>
                        </div>
                        <div class="flex-1 ml-4">
                            <div class="block uppercase tracking-wide text-gray-700 text-xs mb-2 font-semibold">
                                Comparator
                                <div class="text-xs font-normal tracking-tight text-gray-600">
                                    How we compare the parameter to the value
                                </div>
                            </div>

                            <select v-model="condition.comparator" class="appearance-none block w-full bg-gray-200 text-gray-700 border border-gray-200 rounded py-1 px-2 leading-tight focus:outline-none focus:bg-white focus:border-gray-500">
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
                            <div class="block uppercase tracking-wide text-gray-700 text-xs mb-2 font-semibold">
                                Value
                                <div class="text-xs font-normal tracking-tight text-gray-600">
                                    What we are comparing the transaction to
                                </div>
                            </div>
                            <input v-model="condition.value" class="appearance-none block w-full bg-gray-200 text-gray-700 border border-gray-200 rounded py-1 px-2 leading-tight focus:outline-none focus:bg-white focus:border-gray-500" type="text" placeholder="STEAMGAMES.COM" />
                        </div>
                    </div>
                    <div class="w-full text-xs text-gray-600">
                        Remember, ALL of the conditions must be true for the group to be applied.
                    </div>
                    <button @click="addCondition" class="mt-4 px-2 py-1 text-sm border-2 border-blue-700 focus:outline-none text-blue-700 rounded-lg flex items-center hover:bg-blue-700 hover:text-white hover:shadow">
                        <zondicon icon="add-outline" class="fill-current w-4 h-4" />
                        <span class="ml-2">Add condition</span>
                    </button>
                    <div class="w-full my-4">
                        <label>
                            <input type="checkbox" v-model="form.applyRetroactively">
                             Apply Retroactively
                            <div class="text-xs text-gray-600">
                                (we'll run all your transactions through these group conditionals and add the groups if needed)
                            </div>
                        </label>
                    </div>
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
        data() {
            return {
                form: {
                    name: '',
                    conditions: [],
                    applyRetroactively: true
                },
                saving: false
            }
        },
        methods: {
            closeModal() {
                this.$refs.groupingModal.hide();
            },
            openModal() {
                this.$refs.groupingModal.show();
            },
            addCondition() {
                this.form.conditions.push({
                    value: '',
                    comparator: 'LIKE',
                    parameter: 'name',
                })
            },
            async saveGroup() {
                this.saving = true;

                await this.$store.dispatch('saveGroup', this.form);

                this.saving = false;

                setTimeout(() => this.closeModal(), 300);
            }
        }
    }
</script>

<style scoped>

</style>
