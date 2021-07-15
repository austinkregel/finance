<template>
    <div v-if="!$store.getters.groupLoading">
        <div class="flex flex-wrap justify-end mx-2">
            <graph-modal :add-item="addItem" />
        </div>
        <dashboard id="dashboard">
            <dash-layout v-for="layout in layouts" v-bind="layout" :key="'layout-breakpoint-' + layout.breakpoint">
                <dash-item v-for="item in layout.items" v-bind.sync="item" :key="'dash-item-' + item.id" @resizeEnd="(data) => resizeItem(item, data)" @moveEnd="(data) => resizeItem(item, data)">
                    <div class="h-full w-full rounded shadow" v-dark-mode-white-background>
                        <metric
                            :item="item"
                        />
                    </div>
                </dash-item>
            </dash-layout>
        </dashboard>
    </div>
    <div v-else>
        Loading
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
                // Value in this would be the ID of the given tags.
                items: [
                    ...this.standardRoute('subscriptions', 0, 1),
                    ...this.standardRoute('debit/expense', 1, 3),
                    ...this.standardRoute('bills', 2, 5),
                    ...this.standardRoute('utilities', 3, 7),
                ],
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
            standardRoute(tagName, y, id) {
                return [
                    this.value(tagName, {
                        x: 0,
                        y,
                        id: id + 1,
                        width: 1
                    }),

                    this.trend(tagName, {
                        x: 1,
                        y,
                        id,
                        width: 2,
                    }),
                ];
            },
            findTagByName(name, lang = 'en') {
                return this.$store.getters.groups.data.filter(group => group.name[lang] === name)[0];
            },
            value(tagName, {x, y, id, width}) {
                return {
                    type: 'value:tag',
                    duration: 'mtd',
                    value: this.findTagByName(tagName)?.id,
                    width,
                    height: 1,
                    id,
                    x,
                    y
                }
            },
            trend(tagName, {x, y, id, width}) {
                return {
                    type: 'trend:tag',
                    duration: 'mtd',
                    value: this.findTagByName(tagName)?.id,
                    width,
                    height: 1,
                    id,
                    x,
                    y
                }
            },

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
        async mounted() {
            await this.$store.dispatch('fetchGroups');
            Bus.$off('addItem');
            Bus.$on('addItem', (item) => {
                this.addItem(item);
            })

            Bus.$off('updateItems');
            Bus.$on('updateItems', () => this.items = findLocalStorage('cool-graphs', []));
        }
    }
</script>
