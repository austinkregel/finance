<template>
    <div>
        <graph-modal :types="types" />

        <dashboard id="dashboard">
            <dash-layout v-for="layout in layouts" v-bind="layout" :debug="true" :key="layout.breakpoint">
                <dash-item v-for="item in layout.items" v-bind.sync="item" :key="item.id">
                    <div class="content">{{ item }}</div>
                </dash-item>
            </dash-layout>
        </dashboard>
    </div>
</template>

<script>
    import { Dashboard, DashLayout, DashItem } from "vue-responsive-dash";
    import {findLocalStorage, setLocalStorage} from "../LocalStorage";
    import GraphModal from "../components/GraphModal";

    export default {
        components: {
            GraphModal,
            Dashboard,
            DashLayout,
            DashItem
        },
        data() {
            return {
                items: findLocalStorage('cool-graphs', []),
                types: [
                    {
                        name: "Graph a trend",
                        type: "trend",
                        fields: [
                            {
                                name: "Group",
                                value: 'tag.name',
                            },
                            {
                                name: "Over the past...",
                                value: 'duration',
                            },
                        ]
                    },
                    {
                        name: "Display a metric",
                        type: "value",
                        fields: [
                            {
                                name: "Transaction Name",
                                value: 'transaction.name',
                            },
                            {
                                name: "Over the past...",
                                value: 'duration',
                            },
                        ]
                    }
        ]
            };
        },
        computed: {
            layouts() {
                return [
                    {
                        breakpoint: "xl",
                        numberOfCols: 12,
                        items: this.items
                    },
                    {
                        breakpoint: "lg",
                        breakpointWidth: 1200,
                        numberOfCols: 10,
                        items: this.items
                    },
                    {
                        breakpoint: "md",
                        breakpointWidth: 996,
                        numberOfCols: 8,
                        items: this.items
                    },
                    {
                        breakpoint: "sm",
                        breakpointWidth: 768,
                        numberOfCols: 4,
                        items: this.items
                    },
                    {
                        breakpoint: "xs",
                        breakpointWidth: 480,
                        numberOfCols: 2,
                        items: this.items
                    },
                    {
                        breakpoint: "xxs",
                        breakpointWidth: 0,
                        numberOfCols: 1,
                        items: this.items
                    }
                ]
            }
        },
        methods: {
            addItem(itemData) {
                this.items.push({

                });

                setLocalStorage('cool-graphs', this.items);
            },
            generateKpiItem(itemData) {
                const { type } = itemData;
                const defaults = {
                    label: '$',
                    description: 'A key performance indicator',
                    isPlural: false,
                    noLabel: false,
                    icon: 'building',
                    iconClass: '',
                    iconLabel: '',
                    inverse: false,
                }

            }
        }
    }
</script>

<style scoped>
    .content {
        height: 100%;
        width: 100%;
        border: 2px solid #42b983;
        border-radius: 5px;
    }

</style>
