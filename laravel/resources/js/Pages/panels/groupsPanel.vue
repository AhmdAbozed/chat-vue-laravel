<script lang="ts" setup>
import { ref, watch } from 'vue';
import getXsrf from '../util/xsrf';
import type {channelObj} from '@/Pages/util/types'
const props = defineProps({
    channelItemsList: Array<channelObj>,
    showList: Boolean
})
const emit = defineEmits(['setChat', 'setShowList', 'newChatAdded'])
const displayRequests = ref(false);
const groupNotFound = ref(false);
const groupExists = ref(false);
const formProcessing = ref(false);

async function createJoinRequest(event: Event) {
    try {
        event.preventDefault();
        formProcessing.value = true;
        const xsrf = await getXsrf();
        const target = event.target as any
        const submission = { channelName: target.elements.channelName.value }
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
        const endpoint = location.protocol+"//"+location.host+"/_api/requests";
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
            groupNotFound.value = true;
            setTimeout(() => {
                groupNotFound.value = false;
            }, 3000);
            return false
        }
    }
    catch (e) {
        console.log(e)
        formProcessing.value = false;

    }
}


async function createNewGroup(event: Event) {
    try {
        event.preventDefault();
        formProcessing.value = true;
        const xsrf = await getXsrf();
        const target = event.target as any
        const submission = { channelName: target.elements.channelName.value }
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
        const endpoint = location.protocol+"//"+location.host+"/_api/chats/groups";
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
            groupExists.value = true;
            setTimeout(() => {
                groupExists.value = false;
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
    <section class="col-start-1 row-start-1  text-gray-200 text-lg flex flex-col z-50 m-auto">
   
        <img src="../assets/add3.svg" class="h-24 my-auto">
        <div class="text-2xl font-semibold justify-center self-center text-white mt-16">Create a new group</div>
        <div class=" text-gray-400 px-2 py-1 rounded-lg absolute bottom-12 ml-2 bg-gray-950 bg-opacity-65 animate-fadeInOut"
            role="alert" v-if="groupExists">
            <span class="block sm:inline">Group already exists</span>
        </div>

        <form class="flex p-2 flex-col bg-gray-900" @submit="createNewGroup">
            <input type="text" class="h-8 flex-grow bg-gray-700 rounded-full min-w-0 pl-2"
                placeholder="New Group Name.." name="channelName">
            <input type="submit" value="Create Group"
                class="h-8 mt-3 mb-4 text-white disabled:opacity-50 hover:bg-blue-700 focus:ring-4 focus:outline-none focus:ring-blue-300 font-medium rounded-full text-lg px-5 text-center dark:bg-blue-600 dark:hover:bg-blue-700 dark:focus:ring-blue-800">
        </form>
        <div class="text-lg  justify-center pl-2 self-center text-gray-300 border-t-2 border-gray-500 pt-2 w-full">Or join an existing one..</div>


        <div class=" text-gray-400 px-2 py-1 rounded-lg absolute bottom-12 ml-2 bg-gray-950 bg-opacity-65 animate-fadeInOut"
            role="alert" v-if="groupNotFound">
            <span class="block sm:inline">Group not found</span>
        </div>

        <form class="flex p-2 flex-col bg-gray-900" @submit="createJoinRequest">
            <input type="text" class="h-8 flex-grow bg-gray-700 rounded-full min-w-0 pl-2"
                placeholder="Group Name.." name="channelName">
            <input type="submit" value="Join"
                class="h-8 mt-3 mb-8 text-white disabled:opacity-50 hover:bg-blue-700  dark:bg-blue-600 focus:ring-4 focus:outline-none focus:ring-blue-300 font-medium rounded-full text-lg px-5 text-center dark:hover:bg-blue-700 dark:focus:ring-blue-800">
        </form>
    </section>
</template>