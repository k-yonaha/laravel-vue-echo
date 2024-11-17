<template>
    <div>
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
                    <v-chip color="blue" variant="elevated" class="ml-2">
                        {{ item.message }}
                    </v-chip>
                </v-row>
                <v-row
                    v-else
                    no-gutters
                    class="justify-start align-center mt-4"
                >
                    <v-col cols="1">
                        <v-avatar color="indigo">
                            <v-icon dark :icon="mdiAccountCircle"> </v-icon>
                        </v-avatar>
                    </v-col>
                    <v-col cols="11">
                        <v-row
                            no-gutters
                            class="justify-start align-center"
                        >
                            <v-col cols="12">
                                <span class="message-user">{{
                                    item.user.name
                                }}</span></v-col
                            >
                            <v-col cols="12">
                                <!-- メッセージ部分 -->
                                <v-chip color="blue" class="mr-2">
                                    {{ item.message }}
                                </v-chip>
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

        <v-container>
            <v-row>
                <v-col cols="12">
                    <v-text-field
                        v-model="chatMessage"
                        type="text"
                        variant="outlined"
                        clearable
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
                    </v-text-field>
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
