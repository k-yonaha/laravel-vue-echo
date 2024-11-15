import { createStore } from 'vuex';
import auth from './auth';

const store = createStore({
    modules: {
        auth, // authモジュールを追加
    },
});
export default store;