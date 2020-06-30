export default {
    actions: {
        async runAction({ getters }, { action, data }) {
            await axios.post(buildUrl('/api/actions/' + action), data);
        }
    }
}
