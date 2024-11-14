<template>
    <div>
        <v-virtual-scroll
            ref="virtualScroll"
            :height="virtualScrollHeight"
            :items="chatMessages"
        >
            <template v-slot:default="{ item }">
                <v-row no-gutters class="justify-end align-center mt-4">
                    <!-- 日時部分 (小さい文字) -->
                    <span class="message-time">{{
                        item.created_at_formatted
                    }}</span>

                    <!-- メッセージ部分 -->
                    <v-chip variant="outlined" class="ml-2">
                        {{ item.message }}
                    </v-chip>
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
import { mdiSendCircle } from "@mdi/js";
import { useStore } from "vuex";
import { useRouter } from 'vue-router';

const router = useRouter();
const store = useStore();
const chatMessages = ref([]);
const chatMessage = ref();
const virtualScroll = ref(null);
const virtualScrollHeight = ref(window.innerHeight * 0.8);
const sendLoading = ref(false);

// メッセージ取得
const getMessages = async () => {
    await axios
        .get("api/chat")
        .then((response) => {
            // chatMessages.value = response.data;
            chatMessages.value = [...response.data];
        })
        .catch((error) => {
            alert("API ERROR");
            store.dispatch("auth/logout");
            router.push('login');
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
            alert("API ERROR");
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
        // chatMessages.value.push(e.message);
        chatMessages.value = [...chatMessages.value, e.message];
    });
});

watch(chatMessages, async (value) => {
    await nextTick();
    setTimeout(scrollToBottom, 50);
});
</script>

<style scoped>
.message-time {
    font-size: 0.55rem;
    color: grey;
}
</style>
