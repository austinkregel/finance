<template>
    <component
        is="LineChart"
        :chart-data="chartData"
        :options="options"
    ></component>
</template>
<script>
    import { LineChart } from '@vue-responsive-dash/chartjs'
    import { findLocalStorage } from "../LocalStorage";
    Chart.defaults.global.defaultFontColor = findLocalStorage('darkMode') ?  'white' : 'black';

    export default {
        props: ['chartData'],
        data() {
            return {
                options: {
                    legend: {
                        display: false,
                    },
                    tooltips: {
                        enabled: false
                    },
                    animation : false,
                    bezierCurve : true,

                    responsive: false,
                    maintainAspectRatio: false,
                    scales: {
                        xAxes: [{
                            ticks: {
                                display: false //this will remove only the label
                            }
                        }]
                    }

                }
            }
        }
    }
</script>
