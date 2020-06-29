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
    Chart.defaults.scale.ticks.display = true;
    Chart.defaults.scale.gridLines.display = false;

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
                }
            }
        }
    }
</script>
