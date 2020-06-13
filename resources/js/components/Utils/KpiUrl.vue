<style scoped>

</style>

<template>
    <card-loading-animation :loading="loading">
        <card :slot-scope="$data">
            <div>
                <div class="text-gray-800 font-bold flex items-center justify-between">
                    {{ useDescription }}
                    <refresh-button xs :click="refreshData" :refreshing="refreshing" color="blue-700"></refresh-button>
                </div>
                <div class="text-gray-800 text-4xl font-bold pt-2">
                    {{ label }}{{ value }}
                </div>

                <div class="flex items-center" v-if="!noLabel">
                    <zondicon v-if="shouldShowIcon && icon !== 'metric'" :icon="icon" class="fill-current w-6 h-6" :class="iconClass"></zondicon>
                    <svg v-else-if="shouldShowIcon && icon === 'metric'" class="fill-current" :class="iconClass + ' ' + colors +' '+ (isIncrease ? 'rotate-180' : '')" width="20" height="12">
                        <path d="M2 3a1 1 0 0 0-2 0v8a1 1 0 0 0 1 1h8a1 1 0 0 0 0-2H3.414L9 4.414l3.293 3.293a1 1 0 0 0 1.414 0l6-6A1 1 0 0 0 18.293.293L13 5.586 9.707 2.293a1 1 0 0 0-1.414 0L2 8.586V3z"/>
                    </svg>
                    <span class="text-sm font-bold" :class="{'pl-2': shouldShowIcon, [colors]: true}" v-if="increaseDecreaseLabel">
                        <span v-if="increaseDecreaseLabel !== 'Constant'">{{ increaseOrDecrease.replace('-','') }}%</span><span v-else>&mdash;</span>
                        {{ increaseDecreaseLabel }}
                    </span>
                </div>
            </div>
        </card>
    </card-loading-animation>
</template>

<script>
    export default {
        props: {
            url: {
                type: String,
                default: null
            },
            previousUrl: {
                type: String,
                default: null
            },
            label: {
                type: String,
                default: ''
            },
            description: {
                type: String,
                default: 'Pass in the proper "description" to describe this KPI.'
            },
            isPlural: {
                type: Boolean,
                default: false
            },
            noLabel: {
                type: Boolean,
                default: false
            },
            icon: {
                type: String,
                default: null
            },
            iconClass: {
                type: String,
                default: ''
            },
            iconLabel: {
                type: Boolean,
                default: false
            },
            inverse: {
                type: Boolean,
                default: false
            }
        },
        data() {
            return {
                value: 0,
                previousValue: 0,
                loading: true,
                refreshing: false,
            }
        },
        watch: {
            url () {
                this.loading = true;
                this.getData();
            }
        },
        computed: {
            plural() {
                return this.value !== 1;
            },
            useDescription() {
                if (this.plural && this.isPlural) {
                    return this.titleCase(this.description + 's')
                }

                return this.titleCase(this.description);
            },
            isIncrease() {
                return this.value > this.previousValue;
            },
            shouldShowIcon() {
                return this.icon !== null && this.increaseDecreaseLabel !== '' && this.increaseDecreaseLabel !== 'Constant'
            },
            increaseDecreaseLabel() {
                switch (Math.sign(this.increaseOrDecrease)) {
                    case 1:
                        return 'Increase';
                    case 0:
                        return 'Constant';
                    case -1:
                        return 'Decrease';
                }

                return '';
            },
            increaseOrDecrease() {
                if (this.previousValue === 0 || this.previousValue == null) return '0'
                return (((this.value - this.previousValue) / this.previousValue) * 100).toFixed(2)
            },
            colors() {
                if (this.inverse) {
                    switch (Math.sign(this.increaseOrDecrease)) {
                        case 1:
                            return 'text-red-700';
                        case -1:
                            return 'text-green-700';
                    }
                } else {
                    switch (Math.sign(this.increaseOrDecrease)) {
                        case 1:
                            return 'text-green-700';
                        case -1:
                            return 'text-red-700';
                    }
                }
                return 'text-yellow-700'
            }
        },
        methods: {
            titleCase(description) {
                return description.toLowerCase()
                    .split(' ')
                    .map(part => (part.charAt(0).toUpperCase() + part.slice(1)))
                    .join(' ')
            },
            async getData() {
                if (this.url) {
                    let {data: value} = await axios.get(this.url)
                    this.value = value;
                }

                if (this.previousUrl) {
                    let {data: previousValue} = await axios.get(this.previousUrl)
                    this.previousValue = previousValue;
                }

                this.loading = false;
            },
            refreshData() {
                this.refreshing = true;
                this.getData()
                    .then(e => this.refreshing = false)
            }
        },
        mounted() {
            this.loading = true;
            this.getData();
        }
    }
</script>
