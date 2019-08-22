<style scoped>

</style>

<template>
    <div>
        <div class="flex flex-wrap items-center justify-between">
            <h4 class="text-xl">Accounts</h4>
            <button id="link-button" class="bg-blue-500 text-white p-2 rounded ml-auto mb-3 shadow">
                Link new account
            </button>
        </div>

        <div class="shadow p-4 bg-white rounded">
            <div v-if="accounts.length > 0">
                <div class="flex flex-wrap" v-for="account in accounts">
                    <div>{{ account.name }}</div>
                </div>
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
                accounts: []
            }
        },
        methods: {
            async getAccounts() {
                let {data: accounts } = await axios.get('/api/accounts?action=get');

                this.accounts = accounts;
            }
        },
        mounted() {
            this.getAccounts();
            var handler = Plaid.create({
                clientName: 'Kregel API',
                env: process.env.MIX_PLAID_ENV,
                key: process.env.MIX_PLAID_PUBLIC_KEY,
                product: ['transactions'],
                // webhook: this.url,
                selectAccount: false,
                onSuccess(public_token, metadata) {
                    console.log({public_token, metadata})
                    axios.post('/api/plaid/exchange_token', {
                        public_token: public_token,
                        institution: metadata.institution.institution_id
                    })
                        .then((res) => {
                            this.getAccounts();
                        })
                        .catch((res) => {
                            console.log(res)
                        });
                }
            });

            $('#link-button').on('click', function(e) {
                handler.open();
            });
        }
    }
</script>
