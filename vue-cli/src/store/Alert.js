export default {
    namespaced: 'alert',
    state() {
        /**
         * level: 0 - info (default), 1 - success, 2 - Warning
         **/
        return {
            isActive: false,
            level: 0,
            text: ''
        }
    },
    getters: {
        alertStatus(state) {
            return state.isActive;
        },
        alertBody(state) {
            let body = {};

            switch (state.level) {
                case 0:
                    body.level = 'info';
                    break;
                case 1:
                    body.level = 'success';
                    break;
                case 2:
                    body.level = 'warning';
                    break;
            }
            body.text = state.text;

            return body;
        }
    },
    mutations: {
        setLevel(state, text) {
            state.level = text
        },
        setText(state, text) {
            state.text = text
        },
        activate(state) {
            state.isActive = true
        },
        clear(state) {
            state.level = 0;
            state.text = '';
            state.isActive = false;

        }
    },
    actions: {
        alertGenerate({ commit }, { level = 0, text }) {
            commit('setLevel', level);
            commit('setText', text);
            commit('activate');
        },
        alertClear({ commit }) {
            commit('clear')
        }
    }
}
