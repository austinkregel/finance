<template>
    <div>
        <modal ref="alertingModal" @closed="closeModal">
            <div class="flex flex-wrap" v-dark-mode-dark-text>
                <div class="w-full">
                    <label class="block uppercase tracking-wide text-xs font-bold mb-2" v-dark-mode-dark-text>
                        Alert Name
                    </label>
                    <input v-model="form.name" class="appearance-none block w-full  rounded py-3 px-4 leading-tight focus:outline-none" type="text" placeholder="charged a banking fee" v-dark-mode-input/>
                </div>

                <pre v-if="displayExample" class="text-xs h-32 overflow-y-scroll w-full border rounded my-3">{
  "id": 2454,
  "name": "McDonald's",
  "amount": "9.53",
  "account_id": "0b9Vkk7R4vi5BoO4mwPLQNKBsXXO6Z6LFRwbp",
  "date": "2020-06-15T00:00:00.000000Z",
  "pending": true,
  "category_id": 13005032,
  "transaction_id": "wk7R0ZdQkL5VJrk0IeD9mqVPQ4Urm0nMMtXO8",
  "transaction_type": "place",
  "created_at": "2020-06-15T04:26:01.000000Z",
  "updated_at": "2020-06-15T04:26:01.000000Z",
  "categories": [],
  "category": {
    "id": 102,
    "name": "Fast Food",
    "category_id": 13005032,
    "created_at": "2020-06-06T03:47:19.000000Z",
    "updated_at": "2020-06-06T03:47:19.000000Z"
  },
  "tags": [
     {
        "name": {
           "en": "fast food"
        },
        "slug": {
           "en": "fast food"
        },
        "type": "automatic"
        "order_column": 4,
        "user_id": 1,
     },
     {
        "name": {
           "en": "food-budget"
        },
        "slug": {
           "en": "food-budget"
        },
        "type": "automatic"
        "order_column": 1,
        "user_id": 1,
     }
  ]
}</pre>
                <span class="text-xs mt-4">
                        You can use mustache style templates for the title, body, and payload. To access parts of arrays,
                        we've added some filters namely map, filter, first, last, or offset.
                        <button
                            class="underline focus:outline-none"
                            @click="() => displayExample = !displayExample"
                        >
                            example transaction
                        </button>
                    </span>

                <div class="w-full mt-4">
                    <label class="block uppercase tracking-wide text-xs font-bold mb-2" v-dark-mode-dark-text>
                        Alert Title
                    </label>
                    <input v-model="form.title" class="appearance-none block w-full mt-2 rounded py-3 px-4 leading-tight focus:outline-none" type="text" :placeholder="`New Charge from {{ transaction.name }}`" v-dark-mode-input/>
                </div>
                <div class="w-full mt-4">
                    <label class="block uppercase tracking-wide text-xs font-bold mb-2" v-dark-mode-dark-text>
                        Alert Body
                    </label>
                    <textarea v-model="form.body" class="appearance-none block w-full mt-2 rounded py-3 px-4 leading-tight focus:outline-none" type="text" :placeholder="`{{ transaction.name }} charged your account {{ transaction.account.name }} \${{ transaction.amount }}`" v-dark-mode-input/>
                </div>

                <div class="w-full mt-4">
                    <label class="block uppercase tracking-wide text-xs font-bold mb-2" v-dark-mode-dark-text>
                        Alert Payload
                    </label>
                    <textarea v-model="form.payload" class="appearance-none block w-full mt-2 rounded py-3 px-4 leading-tight focus:outline-none h-32" type="text" :placeholder="alertPayloadPlaceholder" v-dark-mode-input/>
                </div>

                <div class="w-full mt-4 flex flex-col">
                    <label class="block uppercase tracking-wide text-xs font-bold mb-2" v-dark-mode-dark-text>
                        Alert Channels
                    </label>
                    <label v-for="channel in channels" class="w-full mt-2 cursor-pointer">
                        <input type="checkbox" v-model="form.channels" :value="channel.type" />
                        <span class="ml-2">{{ channel.name }}</span>
                    </label>
                </div>


                <div class="w-full py-4 ">
                    <div class="block uppercase tracking-wide text-xs font-bold mb-2">
                        conditionals
                        <div class="text-xs font-normal normal-case" v-dark-mode-light-gray-text>
                            Adding conditionals to alerts allows our system to automatically apply these alerts to your transactions.
                            Not adding any conditionals will automatically apply the alert to all transactions.
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
                    <div class="w-full text-xs mt-2 font-bold" v-dark-mode-light-text>
                        Remember, ALL of the conditionals must be true for the alert to be triggered.
                    </div>
                    <div class="w-full text-xs mt-4" v-dark-mode-light-text>
                        These alerts can be triggered for any TransactionUpdated, TransactionCreated, or TransactionGroupedEvent.
                    </div>
                    <button @click="addCondition" class="mt-4 px-2 py-1 text-sm focus:outline-none rounded-lg flex items-center hover:shadow" v-dark-mode-button>
                        <zondicon icon="add-outline" class="fill-current w-4 h-4" />
                        <span class="ml-2">Add condition</span>
                    </button>
                </div>

                <div class="w-full py-4">
                    <button @click.prevent="saveAlert" class="py-2 px-4 border-2 border-transparent focus:outline-none text-white bg-blue-700 rounded-lg flex items-center hover:bg-white hover:border-blue-700 hover:text-blue-700 hover:shadow">
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
        props: ['alert'],
        data() {
            return {
                form: {
                    name: '',
                    title: '',
                    body: '',
                    payload: '',
                    channels: [],
                    conditions: [],
                    applyRetroactively: true
                },
                saving: false,
                displayExample: false,
                alertPayloadPlaceholder: JSON.stringify({
                    "title": "New Charge from {{ transaction.name }}",
                    "body": "{{ transaction.name }} charged your account {{ transaction.account.name }} \${{ transaction.amount }}",
                    "transaction": "{{ transaction | json }}"
                }, null, 4)

            }
        },
        methods: {
            closeModal() {
                this.$refs.alertingModal.hide();
            },
            openModal() {
                this.form = Object.assign({}, {
                    ...this.alert,
                    conditionals: this.alert.conditionals.map(item => Object.assign({}, item)),
                })

                this.$refs.alertingModal.show();
            },
            addCondition() {
                this.form.conditionals.push({
                    parameter: 'name',
                    comparator: 'EQUAL',
                    value: '',
                })
            },
            async saveAlert() {
                this.saving = true;

                await this.$store.dispatch('updateAlert', {
                    original: this.alert,
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
                await this.$store.dispatch('deleteAlertCondition', {
                    alert: this.alert,
                    condition
                });

                setTimeout(() => this.closeModal(), 300);
                await this.$store.dispatch('fetchAlerts');
            },
        },
        computed: {
            channels() {
                const channels = require('../channels');
                return this.$store.getters.user.alert_channels.map(channel => (channels.filter(c => c.type === channel)[0]))
            }
        }
    }
</script>

<style scoped>

</style>
