<script lang="ts" setup>

const formProcessing = ref(false)
import { ref } from 'vue';
import getXsrf from '@/util/xsrf'
async function createNewChat(event: Event) {
    try {
        event.preventDefault();
        formProcessing.value = true;
        //xsrf may be unnecessary
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
        const endpoint = "http://127.0.0.1:8000/api/chats";
        console.log(endpoint)
        const res = await fetch(endpoint, options);
        formProcessing.value = false;
        console.log(res.status)

        if (res.status == 200) {
            console.log(res.status)

        }
        else {
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
    <form class="flex p-2 bg-gray-900 border-r-2 border-opacity-40 border-gray-950" @submit="createNewChat">
        <input type="text" class="h-8 flex-grow bg-gray-700 border-2 border-gray-800 rounded-full min-w-0 pl-2" placeholder="Add User.." name="receiverName">
        <input type="submit" value="Submit"  hidden>
    </form>
</template>