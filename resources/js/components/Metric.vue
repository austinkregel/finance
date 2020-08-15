<template>
    <div v-if="$store.getters.groupsById[item.value]" class="relative">
        <div class="absolute right-0 top-0 p-1">
            <div class="flex flex-wrap items-center w-full justify-end">
                <button class="focus:outline-none text-red-400 mr-2" @click="deleteMetric">
                    <svg class="w-5" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M9 2a1 1 0 00-.894.553L7.382 4H4a1 1 0 000 2v10a2 2 0 002 2h8a2 2 0 002-2V6a1 1 0 100-2h-3.382l-.724-1.447A1 1 0 0011 2H9zM7 8a1 1 0 012 0v6a1 1 0 11-2 0V8zm5-1a1 1 0 00-1 1v6a1 1 0 102 0V8a1 1 0 00-1-1z" clip-rule="evenodd"></path></svg>
                </button>
                <select v-model="duration" class="appearance-none block rounded py-1 px-2 leading-tight focus:outline-none"  v-dark-mode-input>
                    <option value="7d" class="w-full mt-2 cursor-pointer">7 days</option>
                    <option value="14d" class="w-full mt-2 cursor-pointer">14 days</option>
                    <option value="1m" class="w-full mt-2 cursor-pointer">1 month</option>
                    <option value="1y" class="w-full mt-2 cursor-pointer">1 year</option>
                    <option value="mtd" class="w-full mt-2 cursor-pointer">Month To Date</option>
                    <option value="ytd" class="w-full mt-2 cursor-pointer">Year To Date</option>
                </select>
            </div>
        </div>
        <graph v-if="type==='trend'" :chart-data="chartData"></graph>
        <value v-else-if="type==='value'" :item="item" :data="data"></value>
    </div>
</template>

<script>

    import { findLocalStorage, setLocalStorage } from "../LocalStorage";

    export default {
        // type => trend
        // duration => 1m
        // value => bills
        props: ['item'],
        data() {
            return {
                data: [],
                duration: '7d',
            }
        },
        methods: {
            fetchData({ type, duration, value }) {
                axios.get('/api/data/' + type  + '?duration=' + duration + '&scope=' + value).then(({ data }) => {
                    this.data = data;
                })
            },
            deleteMetric() {
                const graphs = findLocalStorage('cool-graphs');

                setLocalStorage('cool-graphs', graphs.filter(graph => graph.id != this.item.id))

                Bus.$emit('updateItems')
            }
        },
        computed: {
            chartData() {
                const currentSize = Object.values(this.data['current'] || []).length;

                return {
                    labels: Object.keys(this.data['current'] || []),
                    datasets: [
                        {
                            label: this.$store.getters.groupsById[this.item.value].name.en + ' ' + this.item.duration,
                            backgroundColor: 'rgba(54,162,235,0.75)',
                            borderColor: 'rgba(54,162,235,0.75)',
                            fill: false,
                            data: Object.values(this.data['current'] || []).map(Math.abs),
                        },
                        {
                            label: this.$store.getters.groupsById[this.item.value].name.en + ' last ' + this.item.duration,
                            backgroundColor: 'rgba(255,99,132,0.69)',
                            borderColor: 'rgba(255,99,132,0.69)',
                            data: Object.values(this.data['previous'] || []).splice(0, currentSize).map(Math.abs),
                            fill: false,
                        }
                    ]
                }
            },
            type() {
                return this.item.type.split(':')[0]
            },
        },
        watch: {
            duration(newDuration, oldDuration) {
                const graphs = findLocalStorage('cool-graphs', []);

                setLocalStorage('cool-graphs', graphs.map((graph) => {
                    if (graph.id !== this.item.id) {
                        return graph;
                    }

                    graph.duration = newDuration;

                    return graph;
                }))
                this.fetchData({
                    ...this.item,
                    duration: newDuration,
                });
            }
        },
        mounted() {
            try {
                this.duration = this.item.duration;
                this.fetchData(this.item);
            } catch (e) {
                this.$toasted.error(e, {
                    theme: "custom"
                })
            }
        }
    }
</script>
