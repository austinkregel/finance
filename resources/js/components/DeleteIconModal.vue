<template>
    <div>
        <button class="outline-none" @click="deleteItem">
            <svg class="w-4 h-4 mr-6 absolute right-0" fill="currentColor" viewBox="0 0 20 20"><path d="M13.586 3.586a2 2 0 112.828 2.828l-.793.793-2.828-2.828.793-.793zM11.379 5.793L3 14.172V17h2.828l8.38-8.379-2.83-2.828z"></path></svg>
        </button>
    </div>
</template>

<script>
    export default {
        props: ['route'],
        data() {
            return {
                form: {
                    name: '',
                    conditionals: [],
                },
                saving: false
            }
        },
        methods: {
            closeModal() {
                this.$refs.groupingModal.hide();
            },
            openModal() {
                this.form = Object.assign({}, {
                    name: this.tag.name.en,
                    conditionals: this.tag.conditionals.map(item => Object.assign({}, item)),
                })

                this.$refs.groupingModal.show();
            },
            addCondition() {
                this.form.conditionals.push({
                    parameter: 'name',
                    comparator: 'EQUAL',
                    value: '',
                })
            },
            async saveGroup() {
                this.saving = true;

                await this.$store.dispatch('updateGroup', {
                    original: this.tag,
                    updated: this.form
                });

                this.saving = false;

                setTimeout(() => this.closeModal(), 300);
            },
            async deleteCondition(condition) {
                if (!condition.id) {
                    this.form = {
                        ...this.form,
                        conditionals: this.form.conditionals.filter(con => !(
                            con.value === condition.value && con.parameter === condition.parameter && con.comparator === condition.comparator
                        ))
                    }
                    return;
                }
                await this.$store.dispatch('deleteGroupCondition', {
                    tag: this.tag,
                    condition
                });

                setTimeout(() => this.closeModal(), 300);
                await this.$store.dispatch('fetchGroups');
            },
        }
    }
</script>

<style scoped>

</style>
