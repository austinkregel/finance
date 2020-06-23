<style scoped>
</style>

<template>
    <div class="text-white items-center rounded mb-4" v-dark-mode-white-background>
        <div class="px-2">{{ job.message }}</div>
        <stacktrace v-for="(code, $i) in stack" :key="code.id" :code="code" :first="$i === 0" :show-vendor="showVendorTrace"></stacktrace>
        <div class="p-2 rounded-b border-t flex">
            <span class="rounded-full px-3 py-1 text-sm font-semibold mr-2" v-dark-mode-input>
                #connection-{{ job.connection }}
            </span>
            <span class="rounded-full px-3 py-1 text-sm font-semibold mr-2" v-dark-mode-input>
                #queue-{{ job.queue }}
            </span>
            <span v-for="(arg, $i) in job.args" v-if="arg && arg.length !== 0" class="rounded-full px-3 py-1 text-sm font-semibold mr-2" v-dark-mode-input>
                #{{ $i }}-{{ arg }}
            </span>
            <span class="flex-grow">&nbsp;</span>
            <svg @click="rerunTheJob" :class="{'spin-the-thing': spin}" class="text-grey-darker fill-current rounded-full h-6 w-6" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20"><path d="M10 3v2a5 5 0 0 0-3.54 8.54l-1.41 1.41A7 7 0 0 1 10 3zm4.95 2.05A7 7 0 0 1 10 17v-2a5 5 0 0 0 3.54-8.54l1.41-1.41zM10 20l-4-4 4-4v8zm0-12V0l4 4-4 4z"/></svg>
        </div>
    </div>
</template>

<script>
    import Stacktrace from "./Stacktrace";
    export default {
        components: {
            Stacktrace,
        },
        props: ['job', 'showVendorTrace'],
        data() {
            return {
                spin: false
            }
        },
        computed: {
            stack() {
                return this.job.codestack.filter(code => {
                    if (this.showVendorTrace) {
                        return true
                    }
                    return !code.file.includes('/vendor/');
                })
            }
        },
        methods: {
            rerunTheJob() {
                this.$toasted.show('Attempting to try the job again! Give it time to process!', { type: 'success' })
                this.spin = true;
                axios.post('/api/failed-jobs/rerun-job/' + this.job.id)
            }
        },
        mounted() {
            Prism.highlightAll();
        }
    }
</script>
