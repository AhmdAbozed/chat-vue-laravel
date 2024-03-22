<script lang="ts" setup>
import chatMsg from './chatMsg.vue';
import chatsList from './chatsList.vue'
import { EchoObj } from './util/echo';
import { ref, isProxy, watch, onBeforeMount, onMounted } from 'vue';
import { router } from '@inertiajs/vue3'

const messages = ref();
const currentChat = ref();
const chatItemsList = ref();
const showList = ref(true);
onMounted(async () => {
    const chats = await getChats();
    chatItemsList.value = chats;
    currentChat.value =
        chats[0]
})
watch(currentChat, async (newChat) => {
    const newMsgs = await getMsgs();
    if (window.innerWidth < 650) showList.value = false;
    messages.value = newMsgs;
    EchoObj.leaveAllChannels();
    EchoObj.private("chat." + newChat.channel_id)
        .listen(".NewMsgSent", async (e: any) => {
            console.log("caught something?")
            console.log(e);
            const newMsgs = await getMsgs();
            console.log(newChat.receiver)
            messages.value = newMsgs;
        })
})

async function getChats() {
    const options: RequestInit = {
        method: "GET",
        headers: {
            "Accept": "application/json",
            'Content-Type': 'application/json',
            'Access-Control-Allow-Credentials': 'true',
        },
        credentials: "include",
    }
    const endpoint = location.protocol + "//" + location.host + "/api/chats/";
    console.log(endpoint)
    const res = await fetch(endpoint, options);
    if (res.status === 200) {
        const toJson = await res.json();
        return toJson
    }
    else return;
}
async function getMsgs() {
    const options: RequestInit = {
        method: "GET",
        headers: {
            'Content-Type': 'application/json',
            'Access-Control-Allow-Credentials': 'true',
        },
        credentials: "include",
    }
    const endpoint = location.protocol + "//" + location.host + "/api/chats/" + currentChat.value.channel_id + "/messages";
    console.log(endpoint)
    const res = await fetch(endpoint, options);
    if (res.status === 200) {
        const toJson = await res.json();
        return toJson
    }
}
async function sendMsg(e: Event) {
    e.preventDefault();
    if (!currentChat.value || !currentChat.value.channel_id) {
        return
    }
    const target = e.target as any
    const submission = { channel_id: currentChat.value.channel_id, content: target.elements.message.value }
    const options: RequestInit = {
        method: "POST",
        headers: {
            'Content-Type': 'application/json',
            'Access-Control-Allow-Credentials': 'true',
            //without decoding, %3D in token isn't converted to =, which causes token mismatch
            'X-XSRF-TOKEN': decodeURIComponent(document.cookie.split("; ").find((row) => row.startsWith("XSRF-TOKEN="))!.split("=")[1]!)
        },
        credentials: "include",
        body: JSON.stringify(submission)
    };

    (e.target! as HTMLFormElement).reset();
    const endpoint = location.protocol + "//" + location.host + "/api/chats/" + currentChat.value.channel_id + "/messages";
    const res = await fetch(endpoint, options);
    const json = await res.json()
    if (res.status === 200) {
        console.log(json);
    }
}
async function testauth() {
    console.log("testing")
    const options: RequestInit = {
        method: "GET",
        headers: {
            "Accept": "application/json",

            'Content-Type': 'application/json',
            'Access-Control-Allow-Credentials': 'true',
        },
        credentials: "include",

    }
    const endpoint = location.protocol + "//" + location.host + "/api/testauth/";
    console.log(endpoint)
    const res = await fetch(endpoint, options);

}
testauth()
</script>
<template>
    <div class="grid grid-rows-1 sm:flex grid-cols-1 h-svh">
        <chatsList :showList="showList" :chatsList="chatItemsList"
            @setChat="(chat: any) => { console.log(chat); currentChat = chat; }"
            @setShowList="(newShowList: any) => { console.log('changing showlist: ' + newShowList); showList = newShowList; }">
        </chatsList>
        <section class="sm:w-full flex flex-col col-start-1 row-start-1">
            <div class="bg-gray-900 p-3 h-14 shadow-gray-800 drop-shadow-lg z-10 flex items-center">
                <button @click="() => { showList = true }" v-if="!showList">
                    <img src="./assets/menu.svg" class="h-8 opacity-90">
                </button>

            </div>
            <div class="bg-gray-800 h-full items-start flex flex-col-reverse flex-nowrap overflow-auto">
                <chatMsg :msgObj="messageObj" :rightSide="currentChat.receiver.name === messageObj.name"
                    v-for="messageObj in messages" :key="messageObj.message.id">
                </chatMsg>
            </div>
            <form class="flex p-2 bg-gray-900" @submit="sendMsg">

                <input type="text"
                    class="pl-3 text-gray-300 h-8 flex-grow bg-gray-700 border-2 border-gray-800 rounded-full"
                    name="message">
                <input type="submit" value="Submit" class="ml-1 text-gray-400">
            </form>
        </section>
    </div>
</template>