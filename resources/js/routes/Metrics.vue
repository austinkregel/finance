<template>
    <div>
        <div class="flex flex-wrap justify-end mx-2">
            <graph-modal :types="types" :add-item="addItem" />
        </div>
        <dashboard id="dashboard">
            <dash-layout v-for="layout in layouts" v-bind="layout" :key="layout.breakpoint">
                <dash-item v-for="item in layout.items" v-bind.sync="item" :key="item.id" @resizeEnd="(data) => resizeItem(item, data)" @moveEnd="(data) => resizeItem(item, data)">
                    <div class="h-full w-full rounded shadow" v-dark-mode-white-background>
                        <metric
                            :item="item"
                        />
                    </div>
                </dash-item>
            </dash-layout>
        </dashboard>
    </div>
</template>

<script>
    import { Dashboard, DashLayout, DashItem } from "vue-responsive-dash";
    import {findLocalStorage, initLocalStorage, setLocalStorage} from "../LocalStorage";
    import GraphModal from "../components/GraphModal";
    initLocalStorage('cool-graphs', []);
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
            };
        },
        computed: {
            layouts() {
                return [
                    {
                        breakpoint: "sm",
                        breakpointWidth: 640,
                        numberOfCols: 3,
                        items: this.items
                    }
                ]
            }
        },
        methods: {
            addItem(itemData) {
                this.items.push(Object.assign({
                    ...itemData,
                    id: this.items.length + 1
                }));
                setLocalStorage('cool-graphs', this.items);
            },
            generateKpiItem(itemData) {
                const {type} = itemData;
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

            },
            resizeItem(item, data) {
                const items = this.items.map(dashboardItem => {
                    if (dashboardItem.id !== item.id) {
                        return dashboardItem;
                    }

                    dashboardItem.x = data.x;
                    dashboardItem.y = data.y;
                    dashboardItem.width = data.width;
                    dashboardItem.height = data.height;
                    console.log(item.id, dashboardItem.id)

                    return dashboardItem;
                });

                this.items = items;

                console.log({items})

                setLocalStorage('cool-graphs', items);
            }
        },
        mounted() {
            Bus.$off('addItem');
            Bus.$on('addItem', (item) => {
                this.addItem(item);
            })

            Bus.$off('updateItems');
            Bus.$on('updateItems', () => this.items = findLocalStorage('cool-graphs', []));
        }
    }
</script>
