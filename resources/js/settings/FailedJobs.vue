<template>
    <div>
        <div class="flex items-center">
            <div class="mb-6 flex-grow">
                Failed Job Manager
            </div>
            <button class="h-8 text-white px-2 rounded shadow ml-4" v-dark-mode-button @click.prevent="refreshJobList">
                Refresh Job List
            </button>
            <button class="h-8 text-white px-2 rounded shadow ml-4" v-dark-mode-button @click.prevent="toggleVendorTrace">
                {{ showVendorTrace ? 'Hide' : 'Show' }} Vendor Trace
            </button>
        </div>

        <div v-if="!loading">
            <trace-card v-for="job in failedJobs.data" :job="job" :show-vendor-trace="showVendorTrace" :key="job.id"></trace-card>
        </div>
        <div v-else class="flex items-center justify-center">
            <svg class="h-16 w-16 fill-current rotate" v-dark-mode-light-gray-text viewBox="0 0 20 20"><path fill-rule="evenodd" d="M4 2a1 1 0 011 1v2.101a7.002 7.002 0 0111.601 2.566 1 1 0 11-1.885.666A5.002 5.002 0 005.999 7H9a1 1 0 010 2H4a1 1 0 01-1-1V3a1 1 0 011-1zm.008 9.057a1 1 0 011.276.61A5.002 5.002 0 0014.001 13H11a1 1 0 110-2h5a1 1 0 011 1v5a1 1 0 11-2 0v-2.101a7.002 7.002 0 01-11.601-2.566 1 1 0 01.61-1.276z" clip-rule="evenodd"></path></svg>
        </div>

        <nav class="flex">
            <button rel="prev"
                    class="btn btn-link py-3 px-4"
                    :class="{
                        'text-primary dim': hasPreviousPage,
                        'text-80 opacity-50': !hasPreviousPage,
                    }"
                    :disabled="!hasPreviousPage"
                    @click.prevent="loadPreviousPage()">
                Previous
            </button>

            <button rel="next"
                    class="ml-auto btn btn-link py-3 px-4"
                    :class="{
                        'text-primary dim': hasNextPage,
                        'text-80 opacity-50': !hasNextPage,
                    }"
                    :disabled="!hasNextPage"
                    @click.prevent="loadNextPage()">
                Next
            </button>
        </nav>
    </div>
</template>
    <script>
        import TraceCard from "./FailedJobs/TraceCard";
        export default {
            components: {
                TraceCard,
            },
            data() {
                return {
                    failedJobs: [],
                    showVendorTrace: false,
                    loading: true,
                    page: 1,
                }
            },
            computed: {
                path() {
                    return '/abstract-api/failed_jobs?sort=-failed_at&page=' + this.page;
                },
                hasNextPage() {
                    return !!this.failedJobs.next_page_url;
                },
                hasPreviousPage() {
                    return !!this.failedJobs.prev_page_url;
                },
            },
            methods: {
                getFailedJobs() {
                    this.failedJobs = [];
                    this.loading = true;
                    axios.get(this.path)
                        .then(res => {
                            this.failedJobs = res.data;
                            this.loading = false;
                        })
                        .catch(err => {
                            this.$toasted.show(err, { type: 'error' })
                        });
                },
                loadNextPage() {
                    this.page++;
                    this.getFailedJobs();
                },
                loadPreviousPage() {
                    this.page--;
                    this.getFailedJobs();
                },
                refreshJobList() {
                    this.page = 1;
                    this.getFailedJobs();
                },
                toggleVendorTrace() {
                    this.showVendorTrace = !this.showVendorTrace;
                },
            },
            mounted() {
                RunPrism()
                this.getFailedJobs();
            }
        }

</script>

<style scoped>

</style>
