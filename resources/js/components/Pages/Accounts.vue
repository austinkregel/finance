<style scoped>

</style>

<template>
    <div class="container mx-auto">
        <div class="flex flex-wrap justify-center">
            <div v-for="account in accounts" class="w-64">
                <div class="rounded-lg m-4 p-4 shadow"
                     :class="{ 'bg-green-100 text-green-900': account.balance >= 0, 'bg-red-100 text-red-900': account.balance < 0 }"
                     :style="'color: ' + textColor(account.institution.primary_color) + '; background: ' + account.institution.primary_color"
                >
                    <div class="flex flex-col items-center">
                        <div class="rounded-full w-32 h-32 items-center flex justify-center">
                            <img :src="account.institution.logo" alt="Logo" class="w-24 h-24"/>
                        </div>
                        <div class="leading-3 pt-2">{{ account.name }}</div>
                        <div class="text-xs leading-3">a {{ account.institution.name }} account</div>
                        <div class="text-2xl font-bold">${{ account.available }} <span class="text-sm">/ ${{ account.balance }}</span></div>
                        <div>{{ lastUpdated(account.updated_at) }}</div>
                        <div>{{ account.type }} - {{ account.subtype }}</div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
    export default {
        props: [],

        data() {
            return {
            }
        },
        computed: {
            accounts() {
                return this.$store.getters.accounts.data;
            }
        },
        methods: {
            lastUpdated(time) {
                return moment(time).format('LLL')
            },
            /** @see https://stackoverflow.com/a/35970186 */
            textColor(hex, bw = true) {
                if (hex.indexOf('#') === 0) {
                    hex = hex.slice(1);
                }
                // convert 3-digit hex to 6-digits.
                if (hex.length === 3) {
                    hex = hex[0] + hex[0] + hex[1] + hex[1] + hex[2] + hex[2];
                }
                if (hex.length !== 6) {
                    throw new Error('Invalid HEX color.');
                }
                var r = parseInt(hex.slice(0, 2), 16),
                    g = parseInt(hex.slice(2, 4), 16),
                    b = parseInt(hex.slice(4, 6), 16);
                if (bw) {
                    // http://stackoverflow.com/a/3943023/112731
                    return (r * 0.299 + g * 0.587 + b * 0.114) > 186
                        ? '#000000'
                        : '#FFFFFF';
                }
                // invert color components
                r = (255 - r).toString(16);
                g = (255 - g).toString(16);
                b = (255 - b).toString(16);
                // pad each with zeros and return
                return "#" + padZero(r) + padZero(g) + padZero(b);
            }
        },

        mounted() {
        }
    }
</script>
