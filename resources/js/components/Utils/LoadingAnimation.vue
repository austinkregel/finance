<style scoped>

</style>

<template>
    <span>
        {{ animation }}
    </span>
</template>

<script>
    export default {
        name: 'loading-animation',
        data() {
            return {
                frames: ["┏( ಠ‿ಠ)┛","┏( ಠ‿ಠ)┓","┗(ಠ‿ಠ )┓","┗(ಠ‿ಠ )┛"],
                index: 0,
                interval: null
            }
        },
        computed: {
            animation() {
                return this.frames[this.index];
            }
        },
        mounted() {
            this.interval = setInterval(() => {
                if (this.index >= this.frames.length-1) {
                    return this.index = 0;
                }
                this.index++;
            }, 200);
        },
        beforeDestroy() {
            clearInterval(this.interval)
        }
    }
</script>
