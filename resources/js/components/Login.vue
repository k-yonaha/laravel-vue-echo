<template>
    <v-sheet
        class="bg-deep-purple pa-12 justify-center align-content-center"
        rounded
        style="min-height: 100vh"
    >
        <v-card class="mx-auto px-6 py-8" max-width="344">
            <v-form v-model="form" @submit.prevent="onSubmit">
                <v-alert v-if="loginError" closable type="error" class="mb-4">
                    {{ loginError }}
                </v-alert>
                <v-text-field
                    v-model="email"
                    :readonly="loading"
                    :rules="[required]"
                    class="mb-2"
                    label="メールアドレス"
                    placeholder=""
                    clearable
                ></v-text-field>

                <v-text-field
                    v-model="password"
                    :readonly="loading"
                    :type="show ? 'text' : 'password'"
                    :rules="[required]"
                    label="パスワード"
                    placeholder="パスワードを入力してください"
                    clearable
                    :append-inner-icon="show ? mdiEye : mdiEyeOff"
                    @click:append-inner="show = !show"
                ></v-text-field>

                <br />

                <v-btn
                    :disabled="!form"
                    :loading="loading"
                    color="success"
                    size="large"
                    type="submit"
                    variant="elevated"
                    block
                >
                    ログイン
                </v-btn>
            </v-form>
        </v-card>
    </v-sheet>
</template>

<script setup>
import { onMounted, ref } from "vue";
import { mdiEye, mdiEyeOff } from "@mdi/js";
import { useStore } from "vuex";
import { useRouter } from 'vue-router';

const router = useRouter();

const store = useStore();
const form = ref();
const email = ref();
const password = ref();
const loading = ref(false);
const show = ref(false);
const loginError = ref("");

const required = (value) => !!value || "必須項目です";

const onSubmit = async (event) => {
    loading.value = true;
    loginError.value = "";

    // loginアクションをディスパッチ
    const result = await store.dispatch("auth/login", {
        email: email.value,
        password: password.value,
    });

    if (!result.success) {
        console.log(result)
        loginError.value = result.error;
    }else{
        router.push('/');
    }
    loading.value = false;
};
</script>
