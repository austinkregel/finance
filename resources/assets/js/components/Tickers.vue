<style scoped>

</style>

<template>
    <div>
        <div class="panel panel-default">
            <div class="panel-heading">
                My ticker list
            </div>
            <div class="panel-body">
                <table class="table">
                    <tr>
                        <th>Ticker</th>
                        <th>Price</th>
                    </tr>
                    <tr v-for="ticker in tickers">
                        <td>{{ ticker.ticker }}</td>
                        <td>{{ ticker.last_price }}</td>
                    </tr>
                </table>
            </div>
        </div>
    </div>
</template>

<script type="text/babel">
    export default {
        data() {
            return {
                tickers_data: {data: []},
                tickers: []
            }
        },
        methods: {
            getTickers() {
                axios.get('/api/tickers')
                    .then(res => {
                        this.tickers = res.data
                    });
            }
        },
        mounted() {
            Bus.$on('UpdateTickers', () => this.getTickers())
            this.getTickers();
        }
    }
</script>
