<script lang="ts" setup>

import { ref } from 'vue';
import getXsrf from './util/xsrf'

const formProcessing = ref(false)
const notFoundAlert = ref(false)


const emit = defineEmits(['newChatAdded'])

async function createNewChat(event: Event) {
    try {
        event.preventDefault();
        formProcessing.value = true;
        //xsrf may be unnecessary, but needed for post by default
        const xsrf = await getXsrf();
        const target = event.target as any
        const submission = { receiverName: target.elements.receiverName.value }
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
        }
        const endpoint = location.protocol+"//"+location.host+"/_api/chats";
        console.log(endpoint)
        const res = await fetch(endpoint, options);
        formProcessing.value = false;
        console.log(res.status)

        if (res.status == 200) {
            console.log(res.status)
            emit('newChatAdded', true)
            return true
        }
        else {
            notFoundAlert.value = true;
            setTimeout(() => {
                notFoundAlert.value = false;
            }, 3000);
            return false
        }
    }
    catch (e) {
        console.log(e)
        formProcessing.value = false;

    }
}
</script>
<template>
    <div class=" text-gray-400 px-2 py-1 rounded-lg absolute bottom-12 ml-2 bg-gray-950 bg-opacity-65 animate-fadeInOut" role="alert" v-if="notFoundAlert">
            <span class="block sm:inline">User not found</span>
        </div>
    <form class="flex p-2 bg-gray-900 border-r-2 border-opacity-40 border-gray-950" @submit="createNewChat">
        <input type="text" class="h-8 flex-grow bg-gray-700 border-2 border-gray-800 rounded-full min-w-0 pl-2" placeholder="Add User.." name="receiverName">
        <input type="submit" value="Submit"  hidden>
    </form>
</template>