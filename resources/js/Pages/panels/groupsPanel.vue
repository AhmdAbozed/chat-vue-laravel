<script lang="ts" setup>
import { ref, watch } from 'vue';
import getXsrf from '../util/xsrf';
import type { channelObj } from '@/Pages/util/types'
const props = defineProps({
    channelItemsList: Array<channelObj>,
    showList: Boolean
})
const emit = defineEmits(['newChatAdded'])
const displayRequests = ref(false);
const joinMessage = ref('');
const groupMessage = ref('');
const joinProcessing = ref(false);
const creationProcessing = ref(false);

async function createJoinRequest(event: Event) {
    try {
        event.preventDefault();
        joinProcessing.value = true;
        const xsrf = await getXsrf();
        const target = event.target as any
        const submission = { channelName: target.elements.channelName.value }
        const options: RequestInit = {
            method: "POST",
            headers: {
                'Content-Type': 'application/json',
                'Access-Control-Allow-Credentials': 'true',
                //without decoding, %3D in token isn't converted to =, which causes token mismatch
                'X-XSRF-TOKEN': decodeURIComponent(document.cookie.split("; ").find((row) => row.startsWith("XSRF-TOKEN="))!.split("=")[1]!),
                'Accept': 'application/json'
            },
            credentials: "include",

            body: JSON.stringify(submission)
        }
        const endpoint = location.protocol + "//" + location.host + "/_api/requests";
        console.log(endpoint)
        const res = await fetch(endpoint, options);
        joinProcessing.value = false;
        console.log(res.status)

        if (res.status == 200) {
            console.log(res.status)
            joinMessage.value = 'Join Request Sent';
            setTimeout(() => {
                joinMessage.value = '';
            }, 3000);
            emit('newChatAdded', true)
            return true
        }
        else if (res.status == 500) {
            throw (res.status)
        } else {
            joinMessage.value = (await res.json()).message;
            setTimeout(() => {
                joinMessage.value = '';
            }, 3000);
            return false

        }
    }
    catch (e) {
        console.log(e)
        joinProcessing.value = false;
        joinMessage.value = 'Something Went Wrong: ' + e;
        setTimeout(() => {
            joinMessage.value = '';
        }, 3000);
        return false
    }
}


async function createNewGroup(event: Event) {
    try {
        event.preventDefault();
        creationProcessing.value = true;
        const xsrf = await getXsrf();
        const target = event.target as any
        const submission = { channelName: target.elements.channelName.value }
        const options: RequestInit = {
            method: "POST",
            headers: {
                'Content-Type': 'application/json',
                'Access-Control-Allow-Credentials': 'true',
                //without decoding, %3D in token isn't converted to =, which causes token mismatch
                'X-XSRF-TOKEN': decodeURIComponent(document.cookie.split("; ").find((row) => row.startsWith("XSRF-TOKEN="))!.split("=")[1]!),
                'Accept': 'application/json'
            },
            credentials: "include",

            body: JSON.stringify(submission)
        }
        const endpoint = location.protocol + "//" + location.host + "/_api/chats/groups";
        console.log(endpoint)
        const res = await fetch(endpoint, options);
        console.log(res.status)
        creationProcessing.value = false;

        if (res.status == 200) {

            groupMessage.value = 'Group Created';
            setTimeout(() => {
                groupMessage.value = '';
            }, 3000);
            console.log(res.status)
            emit('newChatAdded', true)
            return true
        }
        else if (res.status == 500) {
            throw (res.status)
        }
        else {
            groupMessage.value = (await res.json()).message;
            setTimeout(() => {
                groupMessage.value = '';
            }, 3000);
            return false
        }
    }
    catch (e) {
        console.log(e)
        groupMessage.value = 'Something Went Wrong: ' + e;
        setTimeout(() => {
            groupMessage.value = '';
        }, 3000);
    }
}

</script>
<template>
    <section class="col-start-1 row-start-1  text-gray-200 text-lg flex flex-col z-50 m-auto">

        <img src="../assets/add3.svg" class="h-24 my-auto">
        <div class="text-2xl font-semibold justify-center self-center text-white mt-16">Create a new group</div>
        <div class=" text-gray-200 px-2 py-1 rounded-lg absolute bottom-12 ml-2 bg-gray-950 bg-opacity-65 animate-fadeInOut"
            role="alert" v-if="groupMessage">
            <span class="block sm:inline">{{ groupMessage }}</span>
        </div>

        <form class="flex p-2 flex-col bg-gray-900" @submit="createNewGroup">
            <input type="text" minlength="4"
                class="h-8 flex-grow bg-gray-700 rounded-full min-w-0 pl-2 focus:ring-2 focus:outline-none focus:ring-blue-900"
                required placeholder="New Group Name.." name="channelName">
            <input type="submit" :value="creationProcessing ? 'Creating..' : 'Create Group'"
                :disabled="creationProcessing"
                class="h-8 mt-3 mb-4 text-white disabled:opacity-50  focus:ring-4 focus:outline-none  font-medium rounded-full text-lg px-5 text-center bg-blue-600 hover:bg-blue-700 focus:ring-blue-800">
        </form>
        <div class="text-lg  justify-center pl-2 self-center text-gray-300 border-t-2 border-gray-500 pt-2 w-full">Or
            join an existing one..</div>


        <div class=" text-gray-200 px-2 py-1 rounded-lg absolute bottom-12 ml-2 bg-gray-950 bg-opacity-65 animate-fadeInOut"
            role="alert" v-if="joinMessage">
            <span class="block sm:inline">{{ joinMessage }}</span>
        </div>

        <form class="flex p-2 flex-col bg-gray-900" @submit="createJoinRequest">
            <input type="text"
                class="h-8 flex-grow bg-gray-700 rounded-full min-w-0 pl-2 focus:ring-2 focus:outline-none focus:ring-blue-900"
                placeholder="Group Name.." required name="channelName">
            <input type="submit" :value="joinProcessing ? 'Joining..' : 'Join'"
                class="h-8 mt-3 mb-8 text-white disabled:opacity-50 hover:bg-blue-700  dark:bg-blue-600 focus:ring-4 focus:outline-none  font-medium rounded-full text-lg px-5 text-center dark:hover:bg-blue-700 dark:focus:ring-blue-800">
        </form>
    </section>
</template>