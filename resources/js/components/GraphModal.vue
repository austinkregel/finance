<template>
    <div>
        <button v-if="true" @click="openModal" class="p-2 ml-2 focus:outline-none rounded-lg flex items-center hover:shadow" v-dark-mode-button>
            <zondicon icon="add-outline" class="w-6 h-6 fill-current" />
            <span class="ml-2 font-medium">New Metric</span>
        </button>

        <button v-else class="outline-none" @click="openModal">
            <svg class="w-4 h-4 mr-2 absolute right-0" fill="currentColor" viewBox="0 0 20 20"><path d="M13.586 3.586a2 2 0 112.828 2.828l-.793.793-2.828-2.828.793-.793zM11.379 5.793L3 14.172V17h2.828l8.38-8.379-2.83-2.828z"></path></svg>
        </button>

        <modal ref="graphingModal" @closed="closeModal">
            <div class="flex flex-wrap">
                <div class="w-full mt-4 flex flex-col">
                    <label class="block uppercase tracking-wide text-xs font-bold mb-2" v-dark-mode-dark-text>
                        Graph types
                    </label>
                    <select v-model="form.type" class="appearance-none block w-full rounded py-1 px-2 leading-tight focus:outline-none"  v-dark-mode-input>
                        <option v-for="type in types" :value="type.type" class="w-full mt-2 cursor-pointer">
                            {{ type.name }}
                        </option>
                    </select>
                </div>

                <div v-if="type" class="w-full">
                    <div class="" v-for="(field, $i) in type.fields">
                        <div class="block uppercase tracking-wide text-xs mb-2 font-semibold">
                            {{ field.name }}

                            <div>
                                <input v-if="field.type === 'string'" v-model="form.value" class="appearance-none block w-full rounded py-1 px-2 leading-tight focus:outline-none" type="text" placeholder="STEAMGAMES.COM" v-dark-mode-input/>

                                <select v-else-if="field.type === 'tag'" v-model="form.value" class="appearance-none block w-full rounded py-1 px-2 leading-tight focus:outline-none"  v-dark-mode-input>
                                    <option v-for="tag in $store.getters.groups.data" :value="tag.id" class="w-full mt-2 cursor-pointer">{{ tag.name.en }}</option>
                                </select>

                                <select v-else-if="field.type === 'duration'" v-model="form.duration" class="appearance-none block w-full rounded py-1 px-2 leading-tight focus:outline-none"  v-dark-mode-input>
                                    <option value="7d" class="w-full mt-2 cursor-pointer">7 days</option>
                                    <option value="14d" class="w-full mt-2 cursor-pointer">14 days</option>
                                    <option value="1m" class="w-full mt-2 cursor-pointer">1 month</option>
                                    <option value="1y" class="w-full mt-2 cursor-pointer">1 year</option>
                                    <option value="mtd" class="w-full mt-2 cursor-pointer">Month To Date</option>
                                    <option value="ytd" class="w-full mt-2 cursor-pointer">Year To Date</option>
                                </select>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="w-full py-4">
                    <button @click.prevent="saveGraph" class="py-2 px-4 border-transparent focus:outline-none rounded-lg flex items-center hover:shadow" v-dark-mode-button>
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
        props: ['addItem'],
        data() {
            return {
                form: {
                    type: '',
                    duration: '',
                    value: '',
                },
                saving: false,
                stateTest: [{"id":1,"type":"trend:tag","duration":"1m","value":"1","x":2,"y":1,"width":2,"height":1},{"id":2,"type":"trend:tag","duration":"1m","value":"2","x":3,"y":0,"width":1,"height":1},{"id":3,"type":"trend:tag","duration":"1m","value":"3","x":2,"y":0,"width":1,"height":1},{"id":4,"type":"trend:tag","duration":"1m","value":"4","x":1,"y":0,"width":1,"height":1},{"id":5,"type":"trend:tag","duration":"1m","value":"5","x":0,"y":1,"width":2,"height":1},{"id":6,"type":"trend:tag","duration":"1m","value":"6","x":0,"y":0,"width":1,"height":1}],
                types: [
                    {
                        name: "Graph a trend (line graph)",
                        type: "trend:tag",
                        fields: [
                            {
                                name: "Group",
                                type: 'tag',
                            },
                            {
                                name: "Over the past...",
                                type: 'duration',
                            },
                        ]
                    },
                    {
                        name: "Display a metric (summed number)",
                        type: "value:tag",
                        fields: [
                            {
                                name: "Group",
                                type: 'tag',
                            },
                            {
                                name: "Over the past...",
                                type: 'duration',
                            },
                        ]
                    }
                ]
            }
        },
        computed: {
            type() {
                return this.types.filter(type => this.form.type === type.type)[0];
            }
        },
        methods: {
            closeModal() {
                this.$refs.graphingModal.hide();
            },
            openModal() {
                this.$refs.graphingModal.show();
            },
            async saveGraph() {
                this.saving = true;

                this.addItem(this.form);

                this.saving = false;

                setTimeout(() => this.closeModal(), 300);
            },
        }
    }
</script>

<style scoped>

</style>
