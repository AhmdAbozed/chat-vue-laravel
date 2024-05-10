<script lang="ts" setup>
import chatMsg from './components/chatMsg.vue';
import chatsList from '@/Pages/panels/chatsList.vue'
import { EchoObj } from '@/Pages/util/echo';
import { ref, watch, onMounted, provide } from 'vue';
import type { PropType } from 'vue'
import type { channelObj, msgObj, fileHeaderData } from '@/Pages/util/types'
import { getChannels, getMsgs } from '@/Pages/util/homeService'
import detailsPanel from '@/Pages/panels/detailsPanel.vue'

type messagesStateType = {
    file: fileHeaderData,
    messagesArr: Array<msgObj>
}
//prop from inertia.render at backend
const props = defineProps({
    signedUserId: Object as PropType<{ id: number, name: string, email: string }>
})
provide('signedInUser', props.signedUserId)

const messages = ref<messagesStateType>();
const uploadedFile = ref<File | null>(null);
const uploadedImgUrl = ref();
const currentChannel = ref<channelObj>();
const channelsList = ref<Array<channelObj>>();
const showList = ref(true);
const showDetails = ref(false);

provide('currentChannelInjection', currentChannel)

onMounted(async () => {
    const channels = await getChannels();
    channelsList.value = channels;
    currentChannel.value = channels[0]

})

let abortGetMessages: AbortController; 
watch(currentChannel, async (newChannel) => {
    if (newChannel) {
        if(abortGetMessages){
            abortGetMessages.abort();
        }
        abortGetMessages = new AbortController();
        const newMsgs = await getMsgs(newChannel, true, abortGetMessages.signal);
        if (window.innerWidth < 650) showList.value = false;
        messages.value = { messagesArr: newMsgs.messages, file: { fileToken: newMsgs.fileToken, fileUrl: newMsgs.fileUrl } }
        EchoObj.private("chat." + newChannel.id).stopListening(".NewMsgSent");
        EchoObj.private("chat." + newChannel.id)
            .listen(".NewMsgSent", async (e: any) => {
                console.log("caught something?")
                console.log(e);
                const newMsgs = await getMsgs(currentChannel.value!, false, abortGetMessages.signal);
                messages.value = { messagesArr: newMsgs.messages, file: { fileToken: newMsgs.fileToken, fileUrl: newMsgs.fileUrl } };
            })
    }

})
function filesOnChange(event: Event) {
    const file = (event.target as HTMLInputElement).files!
    if (file[0]) {
        if (file[0].type.match('^image/')) uploadedImgUrl.value = (URL.createObjectURL(file[0]))
        uploadedFile.value = file![0];
    }
}
async function sendMsg(e: Event) {
    e.preventDefault();
    if (!currentChannel.value || !currentChannel.value.id) {
        return
    }
    const target = e.target as any
    const submission = new FormData();
    submission.append('message', target.elements.message.value)
    if (uploadedFile.value) { submission.append('file', uploadedFile.value as Blob); uploadedFile.value = null }
    const options: RequestInit = {
        method: "POST",
        headers: {
            'Access-Control-Allow-Credentials': 'true',
            //without decoding, %3D in token isn't converted to =, which causes token mismatch
            'X-XSRF-TOKEN': decodeURIComponent(document.cookie.split("; ").find((row) => row.startsWith("XSRF-TOKEN="))!.split("=")[1]!),
            'Accept': 'multipart/form-data'
        },
        credentials: "include",
        body: submission
    };

    (e.target! as HTMLFormElement).reset();
    const endpoint = location.protocol + "//" + location.host + "/_api/chats/" + currentChannel.value.id + "/messages";
    const res = await fetch(endpoint, options);
    const json = await res.json()
    if (res.status === 200) {
        console.log('message sent',json);
    }
}
function getFileSize(file: File) {
    if (file.size > 100000) return (file.size / (1024 * 1024)).toFixed(2) + "MB";
    else return (file.size / 1024).toFixed(2) + "KB";
}
</script>
<template>
    <div class="grid grid-rows-1 sm:flex grid-cols-1 h-svh">

        <chatsList :showList="showList" :channelItemsList="channelsList"
            @newChatAdded="async () => { console.log('second emit'); const chats = await getChannels(); channelsList = chats; }"
            @setChat="(chat: channelObj) => { if (chat.name != currentChannel?.name) currentChannel = chat; }"
            @setShowList="(newShowList: any) => { console.log('changing showlist: ' + newShowList); showList = newShowList; }">
        </chatsList>

        <section class="bg-gray-800 text-gray-300 flex flex-col col-start-1 row-start-1 min-w-0 flex-grow">

            <section class="bg-gray-900 p-3 h-14 shadow-gray-800 drop-shadow-lg z-10 flex items-center">
                <button @click="() => { showList = true }" v-if="!showList">
                    <img src="./assets/menu.svg" class="h-8 opacity-90">
                </button>
                <div class="flex cursor-pointer" @click="() => { showDetails = true }">
                    <img src="./assets/prof3.svg" class="h-12 my-auto">
                    <div class="flex flex-col ml-1">
                        <div class="text-lg translate-y-1">{{ currentChannel?.name }}</div>
                        <div class="text-sm text-gray-400">Click here for details</div>
                    </div>
                </div>
            </section>

            <section class=" h-full items-start flex flex-col-reverse flex-nowrap overflow-y-auto"
                v-if="currentChannel && messages">
                <chatMsg :msgObj="messageObj" :channelId="currentChannel.id" :fileHeaderData="messages?.file"
                    v-for="messageObj in messages!.messagesArr" :key="messageObj.id">
                </chatMsg>
            </section>

            <section class=" h-full items-start flex flex-col-reverse flex-nowrap overflow-auto" v-else>
                <p class="mx-auto mb-5 text-gray-400 text-lg">Select a Conversation To Begin Chatting </p>
            </section>

            <section class="  bg-gray-925 items-start flex" v-if="uploadedFile">

                <img :src="uploadedImgUrl" class="h-8 my-auto" v-if="uploadedFile.type.match('^image/')">
                <img src="./assets/file.svg" class="h-8 my-auto" v-else>
                <div class="flex flex-col">
                    <p class="text-gray-300 ">{{ uploadedFile?.name }} </p>
                    <p class="text-gray-400 ">{{ getFileSize(uploadedFile!) }} </p>

                </div>
                <button class="ml-auto" @click="() => { uploadedFile = null }">
                    <img src="./assets/exit.svg" class="h-8 ml-auto">
                </button>

            </section>
            <form class="flex p-2 bg-gray-900" @submit="(e) => { sendMsg(e) }">

                <label htmlFor="img-upload" class="cursor-pointer">
                    <img src="./assets/plus5.svg" class="h-8" alt="">
                    <input type="file" id="img-upload" className="input-button" name="img" @change="filesOnChange"
                        hidden>
                    </input>
                </label>

                <input type="text"
                    class="pl-3 text-gray-300 h-8 flex-grow bg-gray-700 border-2 border-gray-800 rounded-full"
                    name="message" placeholder="Enter Message..">
                <input type="submit" value="Submit" class="ml-1 text-gray-400">

            </form>

        </section>
        <detailsPanel @setShowDetails="() => { showDetails = false; }" :channelItemObj="currentChannel"
            v-if="currentChannel && showDetails" />
    </div>
</template>