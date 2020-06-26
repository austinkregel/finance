
<template>
    <button @click="execAction" :class="'flex items-center focus:outline-none ' + buttonClasses">
        <zondicon v-if="!loading" :icon="icon" :class="classes"/>
        <zondicon v-else icon="refresh" class="text-blue rotate fill-current w-4 h-4 mr-2"/>
        <slot></slot>
    </button>
</template>

<script>
    export default {
        props: {
            action: {
                type: Function,
                default: () => {
                    console.error('You must pass an :action prop to the action button.')
                }
            },
            icon: {
                type: String,
                default: 'close-outline'
            },
            classes: {
                type: String,
                default: 'text-red fill-current w-4 h-4 mr-2'
            },
            buttonClasses: {
                type: String,
                default: ''
            }
        },
        data() {
            return {
                loading: false,
            }
        },
        methods: {
            execAction() {
                this.loading = true;
                let response = this.action()
                if (!response instanceof Promise) {
                    console.error('You must return a promise on your action passed to your action button!')
                }
                response.then(() => {
                    this.loading = false
                }).catch(() => {
                    this.loading = false
                });
            }
        },
        mounted() {
        }
    }
</script>
