<style scoped>
    .form-group {
        margin-bottom: 0;
    }

    .help-block {
        margin-bottom: 0;
        margin-top: 10px;
    }
</style>

<template>
    <div>
        <div :class="{'panel': true, 'panel-success': form.successful && !form.busy, 'panel-default': form.errors.has('ticker') == false, 'panel-danger': form.errors.has('ticker') === true, 'panel-warning': form.errors.has('errors')}">
            <div class="panel-heading">
                Add a new ticker!
            </div>
            <div class="panel-body">
                <form action="#" @submit.prevent="sendData">
                    <div class="form-group" :class="{'has-error': form.errors.has('ticker')}">
                        <label class="col-md-4 control-label">Tickers</label>

                        <div class="col-md-6">
                            <input type="text" class="form-control" name="ticker" v-model="form.ticker">
                    
                            <span class="help-block text-danger" v-if="form.errors.has('ticker')">
                                {{ form.errors.get('ticker') }}
                            </span>
                                <span class="help-block text-warning" v-if="form.errors.has('errors')" style="color: #8a6d3b;">
                                {{ form.errors.get('errors') }}
                            </span>
                                <span class="help-block text-success" v-if="form.successful && !form.busy">
                                Your ticker has been registered!.
                            </span>
                        </div>
                    </div>
                    <div class="form-group" :class="{'has-error': form.errors.has('target_price')}">
                        <label class="col-md-4 control-label">Target Price</label>
                    
                        <div class="col-md-6">
                            <input type="text" class="form-control" name="target_price" v-model="form.target_price">
                    
                            <span class="help-block" v-show="form.errors.has('target_price')">
                                {{ form.errors.get('target_price') }}
                            </span>
                        </div>
                    </div>
                    <div class="form-group" :class="{'has-error': form.errors.has('track_macd')}">
                        <label class="col-md-4 control-label">Track Macd</label>

                        <div class="col-md-6">
                            <input type="checkbox" name="track_macd" v-model="form.track_macd">

                            <span class="help-block" v-show="form.errors.has('track_macd')">
                                {{ form.errors.get('track_macd') }}
                            </span>
                        </div>
                    </div>
                    <div class="form-group" :class="{'has-error': form.errors.has('buttonID')}">
                        <div class="col-md-6">
                            <button class="btn btn-primary" type="submit">
                                Save the ticker
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</template>

<script type="text/babel">
    export default {
        data() {
            return {
                form: new SparkForm({
                    ticker: '',
                })
            }
        },
        methods: {
            sendData() {
                Spark.post('/api/add-stock', this.form)
                    .then(res => {
                        Bus.$emit('UpdateTickers')
                        this.form.ticker = '';
                        $('#ticker').focus()
                    })
                    .catch(errors => {
                        this.form.ticker = '';

                        this.form.errors.set(errors);
                    })
            },
        }
    }
</script>