<script lang="ts" setup>
import chatMsg from './components/chatMsg.vue';
import channelsList from '@/Pages/panels/channelsList.vue'
import { EchoObj } from '@/Pages/util/echo';
import { ref, watch, onMounted, provide, toRaw } from 'vue';
import type { PropType } from 'vue'
import type { channelObj } from '@/Pages/util/types'
import { getChannels, getMsgs } from '@/Pages/util/homeService'
import { getFileSize } from '@/Pages/util/misc'
import detailsPanel from '@/Pages/panels/detailsPanel.vue'

//prop from inertia.render at backend
const props = defineProps({
    signedUserId: Object as PropType<{ id: number, name: string, email: string }>
})
provide('signedInUser', props.signedUserId)

const channels = ref<Array<channelObj>>();
const currentChannel = ref<channelObj>();
const uploadedFile = ref<File | null>(null);
const uploadedImgUrl = ref();

const showList = ref(true);
const showDetails = ref(false);
const isLoadingMessages = ref(false)

provide('currentChannelInjection', currentChannel)

onMounted(async () => {
    const fetchedChannels = await getChannels();
    fetchedChannels.forEach((channel: channelObj) => {
        listenToMsgChannel(channel, false);
    });
    channels.value = fetchedChannels;
    currentChannel.value = fetchedChannels[0]

})
async function setMessages(channel: channelObj, withFileToken: boolean, reset_unread: boolean) {
    console.log('getting message')
    const newMsgs = await getMsgs(channel, withFileToken, reset_unread);
    channel.messages = newMsgs.messages;
    if(withFileToken){
        channel.file = { fileToken: newMsgs.fileToken, fileUrl: newMsgs.fileUrl };
    }
}


watch(currentChannel, async (newChannel) => {
    //When switching to a new channel, fetch messages to reset unread and update listener to also update messages
    if (newChannel) {
        isLoadingMessages.value = true
        setMessages(newChannel, true, true)

        if (window.innerWidth < 650) showList.value = false;
        listenToMsgChannel(newChannel!, true);
        const newChannelObj = channels.value!.find((channel: channelObj) => channel.id == newChannel.id);
        newChannelObj!.unreadCount = 0
        //reset unread on openning channel, also resets on NewMsgSent while channel is open
    }
})
function listenToMsgChannel(chatChannel: channelObj, updateMessages: boolean) {
    EchoObj.private("chat." + chatChannel.id).stopListening(".NewMsgSent");
    EchoObj.private("chat." + chatChannel.id)
        .listen(".NewMsgSent", async (e: any) => {
            console.log("caught something?")
            console.log(e);
            const newChannelObj = channels.value!.find((channel: channelObj) => channel.id == e.channel_id)
            const isCurrentChannel = currentChannel.value?.id == newChannelObj!.id
            if (updateMessages) {
                setMessages(newChannelObj!, false, isCurrentChannel);
            }
          
            if (isCurrentChannel) {
                newChannelObj!.unreadCount = 0;
            } else {
                newChannelObj!.unreadCount += 1;
            }
        })
}
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
        console.log('message sent', json);
    }
}
</script>
<template>
    <div class="grid grid-rows-1 sm:flex grid-cols-1 h-svh">

        <channelsList :showList="showList" :channelItemsList="channels"
            @newChatAdded="async () => { console.log('second emit'); const chats = await getChannels(); channels = chats; }"
            @setChannel="(channelId: number) => { currentChannel = channels!.find((channel: channelObj) => channel.id == channelId) }"
            @setShowList="(newShowList: any) => { console.log('changing showlist: ' + newShowList); showList = newShowList; }">
        </channelsList>

        <section class="bg-gray-800 text-gray-300 flex flex-col col-start-1 row-start-1 min-w-0 flex-grow">

            <section class="bg-gray-900 p-3 h-14 shadow-gray-800 drop-shadow-lg z-10 flex items-center">
                <button @click="() => { showList = true }" v-if="!showList">
                    <img src="@/Pages/assets/menu.svg" class="h-8 opacity-90">
                </button>
                <div class="flex cursor-pointer" @click="() => { showDetails = true }" v-if="currentChannel">
                    <img src="@/Pages/assets/prof3.svg" class="h-12 my-auto" v-if="currentChannel!.type === 'private'">
                    <img src="@/Pages/assets/g1.svg" class="h-11 my-auto" v-else-if="currentChannel!.type === 'group'">
                    <div class="flex flex-col ml-1">
                        <div class="text-lg translate-y-1">{{ currentChannel?.name }}</div>
                        <div class="text-sm text-gray-400">Click here for details</div>
                    </div>
                </div>
                <button
                    class="bg-blue-950 rounded-full text-sm pt-[0.35rem] pb-2 px-3 ml-auto my-auto hover:bg-blue-800 text-blue-100"><a
                        href="./upgrade">Upgrade</a></button>
            </section>

            <section class=" h-full items-start flex flex-col-reverse flex-nowrap overflow-y-auto mb-2"
                v-if="currentChannel && currentChannel.messages && currentChannel.file">
                <chatMsg :msgObj="messageObj" :channelId="currentChannel.id" :fileHeaderData="currentChannel.file!"
                    v-for="messageObj in currentChannel.messages!" :key="messageObj.id">
                </chatMsg>
            </section>
            <section class=" h-full items-start flex flex-col-reverse flex-nowrap overflow-auto"
                v-else-if="isLoadingMessages">
                <img src="@/Pages/assets/roller.svg" class="mx-auto mb-10 opacity-20 w-4/12 max-w-32"></img>
            </section>
            <section class=" h-full items-start flex flex-col-reverse flex-nowrap overflow-auto" v-else>
                <p class="mx-auto mb-5 text-gray-400 text-lg">Select a Conversation To Begin Chatting </p>
            </section>

            <section class="  bg-gray-925 items-start flex" v-if="uploadedFile">

                <img :src="uploadedImgUrl" class="h-8 my-auto" v-if="uploadedFile.type.match('^image/')">
                <img src="@/Pages/assets/file.svg" class="h-8 my-auto" v-else>
                <div class="flex flex-col">
                    <p class="text-gray-300 ">{{ uploadedFile?.name }} </p>
                    <p class="text-gray-400 ">{{ getFileSize(uploadedFile!) }} </p>

                </div>
                <button class="ml-auto" @click="() => { uploadedFile = null }">
                    <img src="@/Pages/assets/exit.svg" class="h-8 ml-auto">
                </button>

            </section>
            <form class="flex p-2 bg-gray-900" @submit="(e) => { sendMsg(e) }">

                <label htmlFor="img-upload" class="cursor-pointer">
                    <img src="@/Pages/assets/plus5.svg" class="h-8" alt="">
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