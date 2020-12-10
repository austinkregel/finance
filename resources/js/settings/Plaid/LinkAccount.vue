<style scoped>

</style>

<template>
    <div>
        <div class="flex flex-wrap items-center justify-between mb-4">
            <h4 class="text-xl">Accounts</h4>
            <button @click="() => linkAccount()" class="text-white p-2 rounded shadow" :class="{ 'bg-blue-500': !$store.getters.darkMode, 'bg-blue-700': $store.getters.darkMode }">
                Link new account
            </button>
        </div>

        <div class="shadow p-4 rounded" :class="{'bg-gray-700': $store.getters.darkMode, 'bg-white': !$store.getters.darkMode}">
            <div v-if="accessTokens">
                <account-row
                    :dark-mode="$store.getters.darkMode"
                    v-for="accessToken in accessTokens.data"
                    :access-token="accessToken"
                    :key="accessToken.id"
                />
            </div>
            <div v-else class="text-center italic">
                No accounts connected...
            </div>
        </div>
    </div>
</template>

<script>
    export default {
        props: ['darkMode'],
        data() {
            return {
                handler: null,
            }
        },
        methods: {
            date(date) {
                return dayjs(date);
            },
            async setupPlaid(accessToken = null) {
                if (this.handler) {
                    return;
                }
                const fetchLinkToken = async () => {
                    const { data } = await axios.post(!accessToken ? '/api/plaid/create-link-token' : '/api/plaid/update-access-token',  accessToken ? {
                        access_token: accessToken?.id
                    } : {});
                    return data.link_token;
                };

                const configs = {
                    token: await fetchLinkToken(),
                    onSuccess: async (public_token, {institution: {institution_id}}) => {
                        if (accessToken) {
                            return;
                        }

                        await axios.post('/api/plaid/exchange-token', {
                            public_token: public_token,
                            institution: institution_id
                        });

                        await this.getAccounts();
                    },
                    onExit: async function (err, metadata) {
                        if (err != null && err.error_code === 'INVALID_LINK_TOKEN') {
                            this.handler.destroy();
                            this.handler = Plaid.create({
                                ...configs,
                                token: await fetchLinkToken(),
                            });
                        }
                        if (err != null) {
                            console.log(err.message || err.error_code, this.$toasted);
                            return;
                        }
                    },
                };
                this.handler = Plaid.create(configs)
            },
            async linkAccount(account) {
                await this.setupPlaid(account);
                this.handler.open();
            }
        },
        computed: {
            accessTokens() {
                return this.$store.getters.accessTokens
            }
        },
        async mounted() {
            Bus.$off('setupPlaid');
            Bus.$on('setupPlaid', async (account) => {
                await this.linkAccount(account);
                await this.$store.dispatch('fetchAccounts')
            })

        }
    }
</script>
