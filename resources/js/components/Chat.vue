<template>
    <div>
        <v-container>
            <v-row>
                <v-virtual-scroll
                    ref="virtualScroll"
                    :height="virtualScrollHeight"
                    :items="chatMessages"
                    class="mx-2"
                >
                    <template v-slot:default="{ item }">
                        <v-row
                            v-if="item.user_id === user.id"
                            no-gutters
                            class="justify-end align-center mt-4"
                        >
                            <!-- 日時部分 (小さい文字) -->
                            <span class="message-time">{{
                                item.created_at_formatted
                            }}</span>

                            <!-- メッセージ部分 -->
                            <div
                                class="bg-blue ml-2 rounded-xl px-4 py-3"
                                rounded="pill"
                            >
                                <span
                                    v-html="formattedMessage(item.message)"
                                ></span>
                            </div>
                        </v-row>
                        <v-row
                            v-else
                            no-gutters
                            class="justify-start align-center mt-4"
                        >
                            <v-col
                                cols="auto"
                                md="1"
                                sm="1"
                                xs="4"
                                class="px-2"
                            >
                                <v-avatar color="indigo">
                                    <v-icon :icon="mdiAccountCircle"> </v-icon>
                                </v-avatar>
                            </v-col>
                            <v-col cols="auto" md="11" sm="9" xs="8">
                                <v-row
                                    no-gutters
                                    class="justify-start align-center"
                                >
                                    <v-col cols="12">
                                        <span class="message-user">{{
                                            item.user.name
                                        }}</span></v-col
                                    >
                                    <v-col cols="">
                                        <!-- メッセージ部分 -->
                                        <div
                                            class="bg-blue rounded-xl px-4 py-3 align-self-start"
                                            rounded="pill"
                                            style="width: fit-content"
                                        >
                                            <span
                                                v-html="
                                                    formattedMessage(
                                                        item.message
                                                    )
                                                "
                                            ></span>
                                        </div>
                                        <!-- 日時部分 (小さい文字) -->
                                        <span class="message-time">{{
                                            item.created_at_formatted
                                        }}</span>
                                    </v-col>
                                </v-row>
                            </v-col>
                        </v-row>
                    </template>
                </v-virtual-scroll>
            </v-row>
            <v-row>
                <v-col cols="12">
                    <v-form v-model="form">
                        <v-textarea
                            v-model="chatMessage"
                            type="text"
                            variant="outlined"
                            clearable
                            :rules="[textFieldMaxLength]"
                            rows="1"
                            auto-grow
                        >
                            <template v-slot:append-inner>
                                <v-fade-transition leave-absolute>
                                    <v-progress-circular
                                        v-if="sendLoading"
                                        color="info"
                                        size="24"
                                        indeterminate
                                    ></v-progress-circular>
                                    <v-icon
                                        v-else
                                        :icon="mdiSendCircle"
                                        color="warning"
                                        size="x-large"
                                        v-on:click="sendChatMessage()"
                                    ></v-icon>
                                </v-fade-transition>
                            </template>
                        </v-textarea>
                    </v-form>
                </v-col>
            </v-row>
        </v-container>
    </div>
</template>

<script setup>
import axios from "axios";
import { onMounted, ref, watch, nextTick } from "vue";
import { useGoTo } from "vuetify";
import { mdiSendCircle, mdiAccountCircle } from "@mdi/js";
import { useStore } from "vuex";
import { useRouter } from "vue-router";

const router = useRouter();
const store = useStore();
const chatMessages = ref([]);
const chatMessage = ref();
const virtualScroll = ref(null);
const virtualScrollHeight = ref(window.innerHeight * 0.8);
const sendLoading = ref(false);
const user = store.getters["auth/user"];
const form = ref(false);

// メッセージ取得
const getMessages = async () => {
    await axios
        .get("api/chat")
        .then((response) => {
            // chatMessages.value = response.data;
            chatMessages.value = [...response.data];
        })
        .catch((error) => {
            console.log(error);
        })
        .finally();
};

// メッセージ送信
const sendChatMessage = async () => {
    if (!form.value) return;
    sendLoading.value = true;
    const formData = new FormData();
    formData.append("message", chatMessage.value);

    await axios
        .post("api/chat", formData)
        .then((response) => {
            chatMessage.value = "";
            sendLoading.value = false;
        })
        .catch((error) => {
            alert("メッセージ送信に失敗しました");
            console.log(error);
        })
        .finally();
};

const scrollToBottom = () => {
    const container = virtualScroll.value.$el.querySelector(
        ".v-virtual-scroll__container"
    );
    if (container) {
        const lastItem = container.querySelector(
            ".v-virtual-scroll__item:last-child"
        );
        if (lastItem) {
            lastItem.scrollIntoView({ behavior: "smooth", block: "end" });
        }
    }
};

onMounted(async () => {
    await getMessages();
    // // メッセージが送信されると発火する
    Echo.channel("chat").listen("SendChatMessage", (e) => {
        chatMessages.value = [...chatMessages.value, e.message];
    });
});

watch(chatMessages, async (value) => {
    await nextTick();
    // v-virtual-scrollの反映が遅いので遅延させる
    setTimeout(scrollToBottom, 50);
});

const textFieldMaxLength = (value) => {
    const message = value || "";
    return message.length <= 255 || "255文字以内で入力してください";
};

function formattedMessage(message) {
    return message.replace(/\n/g, "<br>");
}
</script>

<style scoped>
.message-user {
    font-size: 0.75rem;
    color: grey;
}
.message-time {
    font-size: 0.55rem;
    color: grey;
}
</style>
