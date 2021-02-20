export default {
    state: {
        activities: {
            loading: true,
            data: [],
            links: {},
            meta: {
                current_page: 0
            }
        }
    },
    getters: {
        activities: (state) => state.activities,
    },
    mutations: {
        activityLoading(state, value) {
            state.activities.loading = value;
        },
        setActivities(state, activities) {
            state.activities = {
                ...state.activities,
                ...activities
            }
        }
    },
    actions: {
        async fetchActivities({ dispatch, state, commit, getters }, { page = 1, filter }) {
            state.activities.loading = true;

            const { data: activities } = await axios.get(buildUrl('/abstract-api/activities', {
                filter,
                sort: '-created_at',
                page,
                include: 'subject.institution,causer',
            }));

            commit('setActivities', {
                ...activities,
                data: state.activities.data.concat(activities.data),
                loading: false,
            })
        }
    }
}
