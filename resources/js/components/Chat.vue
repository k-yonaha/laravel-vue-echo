<template>
    <div>
        <v-virtual-scroll
            ref="virtualScroll"
            :height="300"
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
        <div>
            <textarea v-model="chatMessage"></textarea><br />
            <button type="button" v-on:click="sendChatMessage()">送信</button>
            <v-icon :icon="mdiSendCircle" />
        </div>
    </div>
</template>

<script setup>
import axios from "axios";
import { onMounted, ref, watch } from "vue";
import { mdiSendCircle } from '@mdi/js';
const chatMessages = ref([]);
const chatMessage = ref();
const virtualScroll = ref(null);

// メッセージ取得
const getMessages = () => {
    axios
        .get("api/chat")
        .then((response) => {
            console.log(response);
            chatMessages.value = response.data;
        })
        .catch((error) => {
            alert("API ERROR");
        })
        .finally();
};

// メッセージ送信
const sendChatMessage = () => {
    const formData = new FormData();
    formData.append("message", chatMessage.value);

    axios
        .post("api/chat", formData)
        .then((response) => {
            chatMessage.value = "";
        })
        .catch((error) => {
            alert("API ERROR");
        })
        .finally();
};

const scrollToBottom = () => {
    if (virtualScroll.value) {
        const element = virtualScroll.value.$el;
        element.scrollTop = element.scrollHeight;
    }
};

onMounted(() => {
    getMessages();
    scrollToBottom();
    // メッセージが送信されると発火する
    Echo.channel("chat").listen("SendChatMessage", (e) => {
        console.log(e);
        chatMessages.value.push(e.message);
    });
});

watch(chatMessages, () => {
  scrollToBottom();
});
</script>

<style scoped>
.message-time {
    font-size: 0.55rem;
    color: grey;
}
</style>
