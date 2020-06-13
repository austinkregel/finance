<style scoped>

</style>

<template>
    <div>
        <div class="flex flex-wrap items-center justify-between mb-4">
            <h4 class="text-xl">Accounts</h4>
            <button id="link-button" class="bg-blue-500 text-white p-2 rounded shadow">
                Link new account
            </button>
        </div>

        <div class="shadow p-4 bg-white rounded">
            <div v-if="accessTokens.length > 0">
                Linked to:
                <account-row v-for="accessToken in accessTokens" :access-token="accessToken" :key="accessToken.id" />
            </div>
            <div v-else class="text-center italic">
                No accounts connected...
            </div>
        </div>
    </div>
</template>

<script>
    export default {
        props: [],
        data() {
            return {
                accessTokens: []
            }
        },
        methods: {
            async getAccounts() {
                let { data: accessTokens } = await axios.get('/abstract-api/access_tokens?action=get&include=user,institution,accounts');

                this.accessTokens = accessTokens;
            },
            date(date) {
                return dayjs(date);
            },
        },
        mounted() {
            const that = this;
            var handler = Plaid.create({
                clientName: 'Kregel API',
                env: process.env.MIX_PLAID_ENV,
                key: process.env.MIX_PLAID_KEY,
                product: ['transactions'],
                // webhook: this.url,
                selectAccount: false,
                onSuccess: (public_token, metadata) => {
                    axios.post('/api/plaid/exchange_token', {
                        public_token: public_token,
                        institution: metadata.institution.institution_id
                    })
                        .then((res) => {
                            Bus.$emit('fetchAccounts')
                        })
                }
            });

            Bus.$on('fetchAccounts', () => this.getAccounts());
            Bus.$emit('fetchAccounts')


            $('#link-button').on('click', function(e) {
                handler.open();
            });
        }
    }
</script>
