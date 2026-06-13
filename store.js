import { createStore } from 'vuex';

export default createStore({
    state() {
        return {
            panierLength: 0,
            filteredProductCount: 0,
            filteredProductCountFrom: "",
            announce: "",
            announceId: 0,
            announceErrorId: 0,
            announceError: "",
            searchResultsStacks: undefined
        };
    },
    mutations: {
        updatePanierLength(state, newLength) {
            state.panierLength = newLength;
        },
        updateFilteredProductCount(state, payload) {
            state.filteredProductCount = payload.count;
            state.filteredProductCountFrom = payload.from
        },
        updateAnnounce(state, announce) {
            state.announce = announce;
            state.announceId++;
        },
        updateErrorAnnounce(state, announceError) {
            state.announceError = announceError;
            state.announceErrorId++;
        },
        updateSearchResultsStacks(state, stacks) {
            state.searchResultsStacks = stacks
        }
    },
    actions: {
        async initializePanier({ commit }) {
            const response = await axios.post('/api/panierLength');
            commit('updatePanierLength', response.data);
        },
        updatePanierLength({ commit }, newLength) {
            commit('updatePanierLength', newLength);
        },
        updateFilteredProductCount({ commit }, payload) {
            commit('updateFilteredProductCount', payload);
        },
        updateAnnounce({ commit }, announce) {
            commit('updateAnnounce', announce)
        },
        updateErrorAnnounce({ commit }, announceError) {
            commit('updateErrorAnnounce', announceError)
        },
        updateSearchResultsStacks({ commit }, stacks) {
            commit('updateSearchResultsStacks', stacks)
        }
    }
});
