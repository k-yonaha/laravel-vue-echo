import axios from "axios";

const namespaced = true;

const state = {
    user: JSON.parse(localStorage.getItem("user")) || null,
    isLogin: localStorage.getItem("user") ? true : false,
};
const mutations = {
    setUser(state, user) {
        state.user = user;
        state.isLogin = true;
        localStorage.setItem("user", JSON.stringify(user));
    },
    deleteAuthData(state) {
        state.user = null;
        state.isLogin = false;
        localStorage.removeItem("user");
    },
};
const actions = {
    async login({ commit }, authData) {
        try {
            await axios.get("/sanctum/csrf-cookie");

            const response = await axios.post("/api/login", authData);
            commit("setUser", response.data.user);
            return {
                success: true,
                message: response.data.message,
            };
        } catch (error) {
            if (error.response && error.response.status === 422) {
                // バリデーションエラーが発生した場合の処理
                return {
                    success: false,
                    error: "ログインに失敗しました。メールアドレスまたはパスワードが正しくありません。",
                };
            }
            return {
                success: false,
                error: "ログインに失敗しました。サーバーに問題が発生しました。",
            };
        }
    },
    logout({ commit }) {
        commit("deleteAuthData");
    },
};
const getters = {
    isAuthenticated: (state) => !!state.user,
    user: (state) => state.user,
};
export default {
    namespaced,
    state,
    mutations,
    actions,
    getters,
};
