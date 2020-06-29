<template>
    <div class="px-4">
        <div class="text-lg font-semibold leading-loose">
            {{ $store.getters.groupsById[item.value].name.en }}
        </div>

        <div class="tracking-wide flex flex-wrap items-center leading-tight">
            <span>$</span>
            <span class="text-6xl">{{ format(value) }}</span>
        </div>
        <div class="tracking-wide flex flex-wrap items-center pt-2" :class="{ 'text-red-300': !isIncrease && $store.getters.darkMode, 'text-red-600': !isIncrease && !$store.getters.darkMode }">
            <svg class="fill-current" :class="(!isIncrease ? 'rotate-180' : '')" width="20" height="12">
                <path d="M2 3a1 1 0 0 0-2 0v8a1 1 0 0 0 1 1h8a1 1 0 0 0 0-2H3.414L9 4.414l3.293 3.293a1 1 0 0 0 1.414 0l6-6A1 1 0 0 0 18.293.293L13 5.586 9.707 2.293a1 1 0 0 0-1.414 0L2 8.586V3z"/>
            </svg>

            <span class="text-lg ml-2">${{ previousValue }} was spent last {{ item.duration }}</span>
        </div>
    </div>
</template>

<script>
    export default {
        props: ['data', 'item'],
        methods: {
            format(value) {
                return Math.round(value * 100) / 100;
            },
            abs(value) {
                return Math.abs(value)
            },
            titleCase(description) {
                return description.toLowerCase()
                    .split(' ')
                    .map(part => (part.charAt(0).toUpperCase() + part.slice(1)))
                    .join(' ')
            },
        },
        computed: {
            isIncrease() {
                return this.value < this.previousValue;
            },
            previousValue() {
                return this.format(this.data['previous'] || 0);
            },
            value() {
                return this.format(this.data['current'] || 0);
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
                if (this.previousValue === 0 || this.previousValue == null) return '0.0'
                return (((this.value - this.previousValue) / this.previousValue) * 100).toFixed(2)
            },
            colors() {
                switch (Math.sign(this.increaseOrDecrease)) {
                    case 1:
                        return 'text-green-700';
                    case -1:
                        return 'text-red-700';
                }

                return 'text-yellow-700'
            }
        }
    }
</script>

<style>
    .rotate-180 {
        transform: rotate(180deg);
    }
</style>

