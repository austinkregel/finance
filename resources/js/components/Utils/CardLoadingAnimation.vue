<style scoped>

</style>

<template>
    <div>
        <card v-if="loading" class="w-full items-center text-center items-center ">
            <div style="height: 100px;" class="flex flex-wrap items-center w-full">
                <div class="mx-auto tracking-wide">
                    <loading-animation class="block w-full"></loading-animation>
                    <span class="uppercase font-bold text-xs">Finding your shit...</span>
                </div>
            </div>
        </card>
        <slot v-else></slot>
    </div>
</template>

<script>
    export default {
        props: ['loading'],

        data() {
            return {}
        },

        mounted() {

        }
    }
</script>
