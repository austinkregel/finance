<template>
    <li  class="flex items-center p-2 rounded-lg mr-4" :class="{'shadow-lg': $route.fullPath === path, 'bg-white': $route.fullPath === path && !$store.getters.darkMode, 'bg-gray-700': $route.fullPath === path && $store.getters.darkMode }" >
        <slot name="image"></slot>
        <slot name="text"></slot>
    </li>
</template>

<script>
    export default {
        props: ['path']
    }
</script>

<style scoped>

</style>
