<template>
    <div class="border-t border-grey-light cursor-pointer">
        <div @click="toggleDisplay" class="text-sm w-full p-2 block" v-dark-mode-light-text>
            <span class="text-monospace">{{ code.file }} &mdash; {{ code.frame ||  '{main}'}}</span>
        </div>
        <div class="overflow-hidden">
            <pre class="language-php text-sm text-base line-numbers language-php" style="margin: -1.7em -1em;" :data-line="code.line + 1" :data-start="code.line - 5 < 1 ? 1 : code.line - 5" v-show="display">
                <code class="w-full block -mb-6" v-text="Object.values(code.code).join('')"></code>
            </pre>
        </div>
    </div>
</template>

<script>
    export default {
        props: ['code', 'first', 'showVendor'],
        data() {
            return {
                display: false,
                loaded: false
            }
        },
        methods: {
            toggleDisplay() {
                this.display = !this.display;
                if (!this.loaded) {
                    this.loaded = true;
                    this.showTrace();
                }
            },
            showTrace() {
                console.log(this.code)
            }
        },
        mounted() {
            if (this.first)
                this.display = this.first
        }
    }
</script>
