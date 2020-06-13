<style scoped>
</style>

<template>
    <div>
        <card :slot-scope="$data">
            <div>
                <div class="text-grey-darker font-bold" v-text="useDescription"></div>
                <div class="text-grey-darkest text-4xl font-bold pt-2 flex">
                    {{ value }}<span v-if="percent">%</span>
                </div>

                <div class="flex items-center pt-2" v-if="!noLabel">
                    <zondicon v-if="shouldShowIcon && icon !== 'metric'" :icon="icon" class="fill-current w-6 h-6" :class="iconClass"></zondicon>
                    <svg v-else-if="shouldShowIcon && icon === 'metric'" class="fill-current" :class="iconClass + ' ' + colors +' '+ (isIncrease ? 'rotate-180' : '')" width="20" height="12">
                        <path d="M2 3a1 1 0 0 0-2 0v8a1 1 0 0 0 1 1h8a1 1 0 0 0 0-2H3.414L9 4.414l3.293 3.293a1 1 0 0 0 1.414 0l6-6A1 1 0 0 0 18.293.293L13 5.586 9.707 2.293a1 1 0 0 0-1.414 0L2 8.586V3z"/>
                    </svg>
                    <span class="text-gray-600 text-sm font-bold" :class="{'pl-2': shouldShowIcon}" v-if="!decreaseDescription && increaseDecreaseLabel">
                        <span v-if="increaseDecreaseLabel !== 'Constant'">{{ increaseOrDecrease.replace('-','') }}%</span><span v-else>&mdash;</span>
                        {{ increaseDecreaseLabel }}
                    </span>
                    <span class="text-sm font-bold" :class="'text-' + color + '-700'" v-html="decreaseDescription"></span>
                </div>
            </div>
        </card>
    </div>
</template>

<script>
    export default {
        props: {
            value: {
                type: Number | String,
                default: 0
            },
            color: {
                type: String,
                default: 'gray'
            },
            previousValue: {
                type: Number | String,
                default: 0
            },
            description: {
                type: String,
                default: 'Pass in the proper "description" to describe this KPI.'
            },
            decreaseDescription: {
                type: String,
                default: null
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
            },
            percent: {
                type: Boolean,
                default: false
            }
        },
        data() {
            return {}
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
                if (this.decreaseDescription) {
                    return this.decreaseDescription;
                }

                switch (Math.sign(this.increaseOrDecrease)) {
                    case 1:
                        return 'Increase'
                    case 0:
                        return 'Constant'
                    case -1:
                        return 'Decrease'
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
                            return 'text-red'
                        case -1:
                            return 'text-green'
                    }
                } else {
                    switch (Math.sign(this.increaseOrDecrease)) {
                        case 1:
                            return 'text-green'
                        case -1:
                            return 'text-red'
                    }
                }
                return 'text-yellow-darker'
            }
        },
        methods: {
            titleCase(description) {
                return description.toLowerCase()
                    .split(' ')
                    .map(part => (part.charAt(0).toUpperCase() + part.slice(1)))
                    .join(' ')
            }
        },
        mounted() {
        }
    }
</script>
