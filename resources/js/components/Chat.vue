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
        <div>
            <textarea v-model="chatMessage"></textarea><br />
            <v-btn
                v-on:click="sendChatMessage()"
                append-icon="mdi-account-circle"
            >
                送信
                <template v-slot:append>
                    <v-icon :icon="mdiSendCircle" color="warning"></v-icon>
                </template>
            </v-btn>
        </div>
    </div>
</template>

<script setup>
import axios from "axios";
import { onMounted, ref, watch, nextTick,} from "vue";
import { useGoTo } from "vuetify";
import { mdiSendCircle } from "@mdi/js";
const goTo = useGoTo();
const chatMessages = ref([]);
const chatMessage = ref();
const virtualScroll = ref(null);
const virtualScrollHeight = ref(window.innerHeight * 0.8);

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
    const container = virtualScroll.value?.$el.querySelector('.v-virtual-scroll__container');
    if (container) {
        const lastItem = container.querySelector('.v-virtual-scroll__item:last-child');
        if (lastItem) {
            lastItem.scrollIntoView({ behavior: 'smooth', block: 'end' });
        }
    }
};

onMounted(async () => {
    await getMessages();
    // // メッセージが送信されると発火する
    Echo.channel("chat").listen("SendChatMessage", (e) => {
        // chatMessages.value.push(e.message);
        chatMessages.value = [...chatMessages.value,e.message];
    });
});

watch(chatMessages, async (value) => {
    await nextTick();
    scrollToBottom();
});
</script>

<style scoped>
.message-time {
    font-size: 0.55rem;
    color: grey;
}
</style>
