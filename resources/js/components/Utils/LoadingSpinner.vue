
<template>
    <div>
        <slot v-if="!loading"></slot>
        <card class="w-full text-center" v-else>
            <div class="flex flex-col">
                <zondicon
                    icon="radar" class="fill-current text-green-600 w-16 h-16 rotate mx-auto"
                ></zondicon>
                <span class="font-bold text-grey-600 pt-4">
                    Searching for {{ description }}...
                </span>
            </div>
        </card>
    </div>
</template>

<script>
    export default {
        props: {
            loading: {
                type: Boolean,
                default: true,
            },
            description: {
                type: String
            }
        },
        data() {
            return {}
        },
        mounted() {
        }
    }
</script>
