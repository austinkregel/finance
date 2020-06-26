<template>
    <div>
        <button v-if="true" @click="openModal" class="p-2 ml-4 focus:outline-none rounded-lg flex items-center hover:shadow" v-dark-mode-button>
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
                        <option v-for="type in types" class="w-full mt-2 cursor-pointer">
                            {{ type.name }}
                        </option>
                    </select>
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
        props: ['types'],
        data() {
            return {
                form: {
                    type: '',

                },
                saving: false,
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

                await this.$store.dispatch('saveGraph', this.form);

                this.saving = false;

                setTimeout(() => this.closeModal(), 300);
            },
        }
    }
</script>

<style scoped>

</style>
