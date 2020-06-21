<template>
    <div>
        <button @click="openModal" class="p-2 focus:outline-none rounded-full flex items-center hover:shadow" v-dark-mode-button>
            <category-icon class="w-6 h6" :category="transaction.category"></category-icon>
        </button>

        <modal ref="groupingModal" @closed="closeModal">
            <div>
                <pre>{{ transaction }}</pre>
            </div>
        </modal>
    </div>
</template>

<script>
    export default {
        props: ['transaction'],
        methods: {
            closeModal() {
                this.$refs.groupingModal.hide();
            },
            openModal() {
                this.$refs.groupingModal.show();
            }
        }
    }
</script>

<style scoped>

</style>
