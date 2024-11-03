<template>
    <div>
        <div>
            <textarea v-model="chatMessage"></textarea><br />
            <button type="button" v-on:click="sendChatMessage()">送信</button>
        </div>

        <div v-for="m in chatMessages" :key="m.id">
            <span v-text="m.text"></span>
        </div>
    </div>
</template>

<script setup>
import axios from "axios";
import { onMounted, ref } from "vue";
const chatMessages = ref([]);
const chatMessage = ref();

const sendChatMessage = () => {
    const formData = new FormData();
    formData.append("message", chatMessage.value);

    axios
        .post('api/chat-send', formData)
        .then((response) => {
            
        })
        .catch((error) => {
            alert("API ERROR");
        })
        .finally();
};
</script>
