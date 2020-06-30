export default {
    state: {
        user: {},
    },
    getters: {
        user: (state) => state.user,
        authenticated: (state) => !!state.user,
        notifications: (state) => state.user?.unread_notifications || [],
    },
    actions: {
        async fetchUser({ state }) {
            try {
                const {data: user} = await axios.get('/api/user');

                state.user = user;
            } catch (e) {
                if (e.hasOwnProperty('response') && e.response.status === 401) {
                    state.user = undefined;
                }

                console.log(e)
            }
        },
        async markNotificationAsRead({ dispatch, state }, { id }) {
            await axios.put('/api/read-notification/' + id);
            await dispatch('fetchUser')
        }

    }
}
