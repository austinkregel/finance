
<template>
    <div>
        <div class="flex flex-wrap items-center justify-between mb-4">
            <div class="text-xl">Historical Sync</div>
        </div>

        <div class="shadow p-4 rounded" v-dark-mode-light-text v-dark-mode-white-background>
            <div class="italic">There are limiting factors in the amount of information an institution holds and the length of time a user has had an account </div>
            <div v-if="accessTokens.length > 0">
                <div class="flex flex-wrap w-full items-center mt-4" v-for="accessToken in accessTokens">
                    <label class="cursor-pointer flex items-center mt-2 text-sm italic" v-dark-mode-light-text>
                        <div>
                            <input type="checkbox" v-model="tokens" :value="accessToken.id">
                        </div>
                        <div class="flex flex-col ml-4">
                            <div class="font-medium" v-if="accessToken.institution">{{ accessToken.institution.name }}</div>
                            <span>{{ accessToken.accounts.map(account => account.name).join(', ') }}</span>
                        </div>
                    </label>
                </div>
            </div>
            <div v-else class="text-center italic">
                No accounts connected... Please connect an account to run a historical sync...
            </div>

            <div class="mt-4 flex flex-wrap w-full mt-4" v-if="accessTokens.length > 0">
                <div class="w-full text-lg">Choose the furthest date back, you'd like to run this sync for.</div>
                <div class="w-full">
                    <date-picker :dark-mode="$store.getters.darkMode" />
                </div>
                <div class="w-full">
                    <button
                        class="rounded px-4 py-2 text-white mt-4 flex flex-wrap items-center"
                        @click="syncTransactions"
                        :class="{ 'opacity-50': syncing, 'bg-blue-500': !$store.getters.darkMode, 'bg-blue-700': $store.getters.darkMode }"
                    >
                        <svg class="w-5 rotate" v-if="syncing" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M4 2a1 1 0 011 1v2.101a7.002 7.002 0 0111.601 2.566 1 1 0 11-1.885.666A5.002 5.002 0 005.999 7H9a1 1 0 010 2H4a1 1 0 01-1-1V3a1 1 0 011-1zm.008 9.057a1 1 0 011.276.61A5.002 5.002 0 0014.001 13H11a1 1 0 110-2h5a1 1 0 011 1v5a1 1 0 11-2 0v-2.101a7.002 7.002 0 01-11.601-2.566 1 1 0 01.61-1.276z" clip-rule="evenodd"></path></svg>
                        <span :class="{'ml-2': syncing}">Sync</span><span v-if="syncing">ing</span>
                    </button>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
    import DatePicker from './DatePicker'

    export default {
        components: {
            DatePicker,
        },
        props: ['darkMode'],
        data() {
            return {
                accessTokens: [],
                tokens: [],
                date: '',
                syncing: false,
            }
        },
        methods: {
            async getAccessTokens() {
                let { data: accessTokens } = await axios.get('/abstract-api/access_tokens?action=get&include=user,institution,accounts');

                this.accessTokens = accessTokens;
            },
            async syncTransactions() {
                this.syncing = true;
                try {
                    await axios.post('/api/actions/historical-sync', {
                        access_tokens: this.tokens,
                        date: this.date
                    })
                } finally {
                    this.syncing = false;
                }
            }
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
                            Bus.$emit('fetchAccessTokens')
                        })
                }
            });

            Bus.$off('fetchAccessTokens');
            Bus.$on('fetchAccessTokens', () => this.getAccessTokens());
            Bus.$emit('fetchAccessTokens')

            Bus.$off('chosenDate');
            Bus.$on('chosenDate', (value) => this.date = value)
        }
    }
</script>
