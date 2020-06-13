
<template>
    <div class="flex items-center" :class="xs ? 'w-4 h-4' : 'w-20'">
        <button @click="click"  class="border flex items-center pb-1 rounded-lg p-1" :class="'border-' + color + ' ' + (xs ? 'h-5 m-0' : 'h-8 mr-3')">
            <zondicon
                icon="refresh" :class="'fill-current mx-auto text-'+ color + ' ' + (refreshing ? 'rotate' : '') + (xs ? ' w-3 h-3': ' w-6 h-6' )"
            ></zondicon>
        </button>
    </div>
</template>

<script>
    export default {
        props: {
            color: {
                type: String,
                default: 'bg'
            },
            refreshing: {
                type: Boolean,
                default: true
            },
            xs: {
                type: Boolean,
                default: false
            },
            click: {
                type: Function,
                default: () => {
                    console.error("You need to override the default click prop. <refresh-button :click=\"() => console.log('Hello World')\" />")
                }
            }
        }
    }
</script>
