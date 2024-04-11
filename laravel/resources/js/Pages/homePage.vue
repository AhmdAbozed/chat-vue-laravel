<script lang="ts" setup>
import chatMsg from './components/chatMsg.vue';
import chatsList from '@/Pages/panels/chatsList.vue'
import { EchoObj } from '@/Pages/util/echo';
import { ref, watch, onMounted, provide } from 'vue';
import type { PropType } from 'vue'
import { toRaw } from 'vue'
import type {channelObj, msgObj} from '@/Pages/util/types'
import detailsPanel from '@/Pages/panels/detailsPanel.vue'
type signedInUser = {
}
//prop from inertia.render at backend
const props = defineProps({
    signedUserId: Object as PropType<{ id: number, name: string, email: string }>
})
provide('signedInUser', props.signedUserId)

const messages = ref<Array<msgObj>>();
const currentChannel = ref<channelObj>();
const channelsList = ref<Array<channelObj>>();
const showList = ref(true);
const showDetails = ref(false);

provide('currentChannelInjection', currentChannel)

onMounted(async () => {
    const chats = await getChannels();
    channelsList.value = chats;
    console.log(chats)
    currentChannel.value = chats[0]
})
watch(currentChannel, async (newChannel) => {
    if (newChannel) {
        const newMsgs = await getMsgs();
        if (window.innerWidth < 650) showList.value = false;
        messages.value = newMsgs;
        //EchoObj.private("chat." + newChannel.id).stopListening(".NewMsgSent");
        EchoObj.private("chat." + newChannel.id)
            .listen(".NewMsgSent", async (e: any) => {
                console.log("caught something?")
                console.log(e);
                const newMsgs = await getMsgs();
                messages.value = newMsgs;
            })
    }

})

async function getChannels() {
    const options: RequestInit = {
        method: "GET",
        headers: {
            "Accept": "application/json",
            'Content-Type': 'application/json',
            'Access-Control-Allow-Credentials': 'true',
        },
        credentials: "include",
    }
    const endpoint = location.protocol + "//" + location.host + "/_api/chats/";
    console.log(endpoint)
    const res = await fetch(endpoint, options);
    if (res.status === 200) {
        const toJson = await res.json();
        console.log(toJson)
        return toJson
    }
    else return;
}
async function getMsgs() {
    console.log("currentChannel is: ", currentChannel.value)
    if (currentChannel.value) {
        const options: RequestInit = {
            method: "GET",
            headers: {
                'Content-Type': 'application/json',
                'Access-Control-Allow-Credentials': 'true',
            },
            credentials: "include",
        }
        const endpoint = location.protocol + "//" + location.host + "/_api/chats/" + currentChannel!.value!.id + "/messages";
        console.log(endpoint)
        const res = await fetch(endpoint, options);
        if (res.status === 200) {
            const toJson = await res.json();
            return toJson
        }
    }

}
async function sendMsg(e: Event) {
    e.preventDefault();
    if (!currentChannel.value || !currentChannel.value.id) {
        return
    }
    const target = e.target as any
    const submission = { channel_id: currentChannel.value.id, content: target.elements.message.value }
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
    const endpoint = location.protocol + "//" + location.host + "/_api/chats/" + currentChannel.value.id + "/messages";
    const res = await fetch(endpoint, options);
    const json = await res.json()
    if (res.status === 200) {
        console.log(json);
    }
}
</script>
<template>
    <div class="grid grid-rows-1 sm:flex grid-cols-1 h-svh">
        <chatsList :showList="showList" :channelItemsList="channelsList"
            @newChatAdded="async () => { console.log('second emit reached'); const chats = await getChannels(); channelsList = chats; }"
            @setChat="(chat: any) => { console.log(chat); currentChannel = chat; }"
            @setShowList="(newShowList: any) => { console.log('changing showlist: ' + newShowList); showList = newShowList; }">
        </chatsList>
        <section class="text-gray-300 sm:w-full flex flex-col col-start-1 row-start-1">
            <div class="bg-gray-900 p-3 h-14 shadow-gray-800 drop-shadow-lg z-10 flex items-center">

                <button @click="() => { showList = true }" v-if="!showList">
                    <img src="./assets/menu.svg" class="h-8 opacity-90">
                </button>
                <div class="flex cursor-pointer" @click="">
                    <img src="./assets/prof3.svg" class="h-12 my-auto">
                    <div class="flex flex-col ml-1">
                        <div class="text-lg translate-y-1">{{ currentChannel?.name }}</div>
                        <div class="text-sm text-gray-400">Click here for details</div>
                    </div>
                </div>


            </div>
            <div class="bg-gray-800 h-full items-start flex flex-col-reverse flex-nowrap overflow-auto"
                v-if="currentChannel">
                <chatMsg :msgObj="messageObj" :receiverName="currentChannel.name" v-for="messageObj in messages"
                    :key="messageObj.id">
                </chatMsg>
            </div>
            <div class="bg-gray-800 h-full items-start flex flex-col-reverse flex-nowrap overflow-auto" v-else>
                <p class="mx-auto mb-5 text-gray-400 text-lg">Select a Conversation To Begin Chatting </p>
            </div>
            <form class="flex p-2 bg-gray-900" @submit="sendMsg">

                <input type="text"
                    class="pl-3 text-gray-300 h-8 flex-grow bg-gray-700 border-2 border-gray-800 rounded-full"
                    name="message">
                <input type="submit" value="Submit" class="ml-1 text-gray-400">
            </form>
        </section>
        <detailsPanel :channelItemObj="currentChannel" v-if="currentChannel"/>
    </div>
</template>