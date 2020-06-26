<style lang="scss">
    .tribute-container {
        @apply bg-white rounded shadow text-xs overflow-hidden;
        ul, li {
            list-style: none;
            margin: 0;
            padding: 0;
        }

        li {
            @apply py-1 px-2;
            span {
                @apply font-bold;
            }
        }

        .selected {
            @apply bg-blue-300 px-2;
        }
    }

    .vjs-tree {
        font-family: "Monaco", "Menlo", "Consolas", "Bitstream Vera Sans Mono", monospace;
        font-size: 14px;

        &.is-root {
            position: relative;

            &.has-selectable-control {
                @apply ml-2;
            }
        }

        &.is-mouseover {
            @apply bg-green-100;

            & .vjs-value__string {
                @apply text-green-700;
                //@include mixin-value-style($color-string);
            }
        }

        &.is-highlight-selected {
            @apply bg-green-300;
        }

        .vjs-tree__content {
            @apply .pl-6;

            &.has-line {
                border-left: 1px dotted #ffa500;
            }
        }

        .vjs-tree__brackets {
            cursor: pointer;

            &:hover {
                @apply text-blue-500;
            }
        }

        .vjs-comment {
            @apply text-gray-500;
        }

        .vjs-value__null {
            @apply text-red-500;
            //@include mixin-value-style($color-null);
        }

        .vjs-value__number {
            @apply text-blue-500;
            //@include mixin-value-style($color-number);
        }

        .vjs-value__boolean {
            @apply text-red-500;
            //@include mixin-value-style($color-boolean);
        }

        .vjs-value__string {
            @apply text-green-800;
            //@include mixin-value-style($color-string);
        }
    }
    @import "~vue-multiselect/dist/vue-multiselect.min.css";

</style>

<template>
    <div class="bg-white text-gray-800">
        <div class="flex flex-wrap border border-gray h-full" style="min-height: 400px;">
            <form class="w-1/3 h-auto border-r" @submit.prevent="submitForm">
                <div class="mx-2">
                    <div class="text-sm uppercase text-gray-dark py-2 px-1 flex justify-between">
                        <span>Object to query</span>
                        <span>
                            <button type="button" @click.prevent="clearForm"
                                    class="border border-orange text-orange bg-orange-lightest rounded px-1 focus:outline-none hover:bg-orange-lighter hover:text-orange-dark hover:border-orange-dark">
                                clear
                            </button>
                        </span>
                    </div>
                    <multiselect
                        v-model="model"
                        placeholder="Object to query..."
                        :options="models"
                        :multiple="false"
                        :preselect-first="true"
                        :hide-selected="true"
                        :close-on-select="true"
                    ></multiselect>
                </div>

                <div v-if="model" class="mx-2">
                    <div class="text-sm uppercase text-gray-dark py-2 px-1">Select fields</div>
                    <multiselect
                        v-model="fields"
                        placeholder="Fields to query..."
                        :options="selectedModel.fields || []"
                        :clear-on-select="false"
                        :multiple="true"
                        :hide-selected="true"
                        :close-on-select="true"
                    >
                        <template slot="singleLabel" slot-scope="{ option }">{{ option.name }}</template>
                    </multiselect>
                </div>

                <div v-if="model" class="mx-2">
                    <div class="text-sm uppercase text-gray-dark py-2 px-1">Relations to include</div>
                    <multiselect
                        v-model="includes"
                        placeholder="Relationships..."
                        :options="selectedModel.includes || []"
                        :clear-on-select="false"
                        :close-on-select="true"
                        :multiple="true"
                        :hide-selected="true"
                    >
                        <template slot="singleLabel" slot-scope="{ option }">{{ option.name }}</template>
                    </multiselect>
                </div>

                <div v-if="model" class="flex flex-wrap mx-2 relative text-xs" id="autocomplete-container">
                    <div class="w-full text-sm uppercase text-gray-dark py-2 px-1">Filter data</div>

                    <div class="flex flex-wrap w-full" v-for="(filter, $i) in filters">
                        <div class="flex-1">
                            <div class="block uppercase tracking-wide text-xs mb-2 font-semibold"  v-dark-mode-dark-text>
                                Parameter
                                <div class="text-xs font-normal tracking-tight" v-dark-mode-light-text>
                                    The field who's value we will compare against
                                </div>
                            </div>
                            <select v-model="condition.parameter" class="appearance-none block w-full  rounded py-1 px-2 leading-tight focus:outline-none"  v-dark-mode-input>
                                <option v-for="parameter in parameters" :value="parameter.value">{{ parameter.name }}</option>
                            </select>
                        </div>
                        <div class="flex-1 ml-4">
                            <div class="block uppercase tracking-wide text-xs mb-2 font-semibold" v-dark-mode-dark-text>
                                Comparator
                                <div class="text-xs font-normal tracking-tight" v-dark-mode-light-text>
                                    How we compare the parameter to the value
                                </div>
                            </div>

                            <select v-model="condition.comparator" class="appearance-none block w-full  rounded py-1 px-2 leading-tight focus:outline-none"  v-dark-mode-input>
                                <option v-for="comparator in comparators" :value="comparator.value">{{ comparator.name }}</option>
                            </select>
                        </div>
                        <div class="flex-1 ml-4">
                            <div class="block uppercase tracking-wide text-xs mb-2 font-semibold" v-dark-mode-dark-text>
                                Value
                                <div class="text-xs font-normal tracking-tight" v-dark-mode-light-text>
                                    What we are comparing the transaction to
                                </div>
                            </div>
                            <input v-model="condition.value" class="appearance-none block w-full  rounded py-1 px-2 leading-tight focus:outline-none" type="text" placeholder="STEAMGAMES.COM"  v-dark-mode-input/>
                        </div>
                    </div>

                    <div class="flex justify-end w-full">
                        <button type="button" @click.prevent="addFilter" class="flex items-center border border-blue text-blue mt-2 bg-blue-lightest rounded px-2 py-1 focus:outline-none hover:bg-blue-lighter hover:text-blue-dark hover:border-blue-dark" style="font-size: 1.2em;">
                            <zondicon icon="add-outline" class="w-4 fill-current pr-1"/>
                            filter
                        </button>
                    </div>
                </div>

                <div v-if="model" class="mx-2">
                    <div class="text-sm uppercase text-gray-dark py-2 px-1">Action</div>
                    <multiselect
                        v-model="action"
                        placeholder="get, paginate, or first..."
                        :options="selectedModel.actions || []"
                        :hide-selected="true"
                        :close-on-select="true"
                        :preselect-first="true"
                    >
                    </multiselect>
                </div>

                <div v-if="model" class="flex justify-between mr-2">
                    <select name="page" class="border border-gray bg-white p-0 m-2" v-if="response.data" v-model="page">
                        <option v-for="i in range(1, response.last_page || 1)" :value="i">Page {{ i }}</option>
                    </select>
                    <span v-else></span>

                    <action-button type="submit" :action="getData" icon="save-disk" classes="text-blue-darkest" button-classes="border rounded px-4 py-2 text-black bg-white my-2">
                        Execute
                    </action-button>
                </div>
            </form>
            <div class="w-2/3 bg-gray-lighter">
                <div>
                    <input type="text" v-model="url" class="w-full py-2 px-1 text-gray-dark border-b border-gray-400">
                </div>
                <vue-json-pretty
                    :data="response || {message: 'No model found'}"
                    :show-length="true"
                    :show-line="true"
                    :highlight-selected-node="true"
                    :highlight-mouseover-node="true"
                ></vue-json-pretty>
            </div>
        </div>
    </div>
</template>

<script lang="js">
    import Multiselect from 'vue-multiselect'
    import VueJsonPretty from 'vue-json-pretty'

    export default {
        components: {
            VueJsonPretty,
            Multiselect,
        },
        data() {
            return {
                modelFields: [],
                Object,
                action: null,
                model: null,
                includes: [],
                fields: [],
                filters: [],
                filter: '',
                page: 1,
                comparators: require('../condition-comparator'),
                parameters: require('../condition-parameters'),

                vueOptions: {
                    autocompleteMode: true,
                    selectClass: 'selected',
                    menuContainer: document.getElementById('autocomplete-container'),
                },
                response: {
                    press: "The execute button..."
                }
            }
        },
        methods: {
            tribute(key) {
                return this.selectedModel[key].map(item => ({value: item}))
            },

            getData() {
                return axios.get(this.url).then(({data}) => {
                    if (!data) {
                        data = []
                    }
                    let count = data.length;
                    this.response={}
                    let limit = 400;
                    if (count > limit) {
                        this.response = data.splice(1, 400)

                        this.$toasted.error({
                            title: 'SCOPE THAT QUERY!',
                            message: 'Hey now. You requested too much data... Try filtering down your query to not have us blow up your browser.',
                            animateInside: false,
                            timeout: 10 * 1000
                        });
                    } else {
                        this.response = data;
                    }

                })
            },

            clearForm() {
                this.action = null;
                this.includes = [];
                this.fields = [];
                this.filters = [];
                this.filter = '';
                this.page = 1;
            },

            addFilter() {
                this.filters.push({
                    name: '',

                    value: ''
                })
            },

            submitForm() {
                this.getData()
            },

            range(min, max) {
                let arrayRange = [];
                for (let i = min; i <= max; i++) {
                    arrayRange.push(i);
                }
                return arrayRange;
            }
        },
        computed: {
            models: () => ([
                'transactions',
                'groups'
            ]),
            selectedModel() {
                return this.modelFields[this.model] || {
                    [this.model]: {
                        fields: [],
                        includes: [],
                        sorts: [],
                        filters: [],
                        actions: [
                            'get',
                            'first',
                            'paginate'
                        ],
                    }
                };
            },
            filterTribute() {
                if (!this.selectedModel) {
                    return []
                }
                this.selectedModel.filters;
                return this.tribute('filters')
            },
            filterObject() {
                return this.filters.reduce((filters, filter) => {
                    if (!filter.value || !filter.text) {
                        return filters;
                    }

                    filters[filter.value] = filter.text;
                    return filters;
                }, {});
            },
            pagePart() {
                if (!this.response.data) {
                    return {}
                }

                return {
                    page: this.page
                }
            },
            url() {
                return buildUrl('/abstract-api/' + this.model, Object.assign({
                    fields: this.fields.length > 0 ? {
                        [this.model]: this.fields.join(',')
                    } : null,
                    include: this.includes,
                    action: this.action,
                    filter: this.filterObject,
                }, this.pagePart))
            },
            actionInputs() {
                return this.models.reduce((models, model) => {
                    return this.modelFields[model].actions.reduce((actions, action) => {
                        return actions;
                    }, models);
                }, {})
            }
        },
        mounted() {
            this.models.map(model => {
                axios.get('/abstract-api/' + model + '/fields').then(({data}) => {
                    this.modelFields = {
                        ...this.modelFields,
                        [model]: data
                    };
                });
            });

            let that = this;

            let keybindingHandler = (e) => {
                if (!(e.keyCode == 13 && e.metaKey)) return;

                that.getData()
            };

            document.body.removeEventListener('keydown', keybindingHandler);
            document.body.addEventListener('keydown', keybindingHandler)
        }
    }
</script>
