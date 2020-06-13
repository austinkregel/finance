<style scoped>
    .toggle__dot {
        top: -.15rem;
        left: -.15rem;
        transition: all 0.3s ease-in-out;
    }

    input:checked ~ .toggle__dot {
        transform: translateX(100%);
        background-color: #48bb78;
    }
</style>

<template>
    <label for="toogleA" class="flex items-center cursor-pointer" >
        <div class="relative">
            <input id="toogleA" type="checkbox" class="hidden" @click="toggle"/>
            <div class="toggle__line w-6 h-3 bg-gray-400 rounded-full shadow-inner"></div>
            <div class="toggle__dot absolute w-4 h-4 bg-white rounded-full shadow inset-y-0 left-0"></div>
        </div>
        <div class="ml-4 text-gray-700" style="font-size: 14px">
            {{ label }}
        </div>
    </label>
</template>

<script>
    export default {
        props: ['value', 'label'],
        methods: {
            toggle() {
                this.$emit('input', !this.value)
            }
        },
    }
</script>
