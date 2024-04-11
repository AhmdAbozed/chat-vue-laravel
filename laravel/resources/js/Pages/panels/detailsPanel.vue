<script lang="ts" setup>
import { ref, watch, onMounted } from 'vue';
import type { PropType } from 'vue';
import getXsrf from '../util/xsrf';
import memberItem from '../components/memberItem.vue';
import type {channelObj, requestObj} from '@/Pages/util/types'
import RequestItem from '../components/requestItem.vue';
const props = defineProps({
    showList: Boolean,
    channelItemObj: Object as PropType<channelObj>
})
const emit = defineEmits(['setChat', 'setShowList', 'newChatAdded']);
const displayRequests = ref(false);
const members = ref();
const requests = ref<Array<requestObj>>();

async function fetchDetails(){
    members.value = [];
    requests.value = [];
    const fetchedMembers = await getGroupMembers(props.channelItemObj!.id);
    const fetchedRequests = await getGroupRequests(props.channelItemObj!.id);
    console.log("fetched requests", fetchedRequests);
    members.value = fetchedMembers;
    requests.value = fetchedRequests;

}
onMounted(fetchDetails)

watch(props, fetchDetails)

async function getGroupMembers($channel_id: number) {
    const options: RequestInit = {
        method: "GET",
        headers: {
            "Accept": "application/json",
            'Content-Type': 'application/json',
            'Access-Control-Allow-Credentials': 'true',
        },
        credentials: "include",
    }
    const endpoint = location.protocol + "//" + location.host + "/_api/chats/" + $channel_id + "/users";
    console.log(endpoint)
    const res = await fetch(endpoint, options);
    if (res.status === 200) {
        const toJson = await res.json();
        console.log(toJson)
        return toJson
    }
    else return;
}


async function getGroupRequests($channel_id: number) {
    const options: RequestInit = {
        method: "GET",
        headers: {
            "Accept": "application/json",
            'Content-Type': 'application/json',
            'Access-Control-Allow-Credentials': 'true',
        },
        credentials: "include",
    }
    const endpoint = location.protocol + "//" + location.host + "/_api/chats/" + $channel_id + "/requests";
    console.log(endpoint)
    const res = await fetch(endpoint, options);
    if (res.status === 200) {
        const toJson = await res.json();
        console.log(toJson)
        return toJson
    }
    else return;
}

</script>
<template>
    <section
        class="col-start-1 row-start-1 min-w-56 bg-gray-950 h-full text-gray-200 text-lg flex flex-col z-50 m-auto">
        <div class="bg-gray-900  p-3 min-h-14 shadow-gray-800 drop-shadow-lg z-30 flex">Details

            <button class="ml-auto mr-1" @click="emit('setShowList', false)">

                <img src="../assets/exit.svg" class="h-8 my-auto">
            </button>
        </div>
        <img src="../assets/add3.svg" class="h-24 mt-6">
        <div class="text-2xl font-semibold justify-center self-center  mt-4">{{ props.channelItemObj?.name }}</div>
        <div class="flex mt-3 text-gray-300 border-t-1 border-gray-950" v-if="channelItemObj?.type == 'group'">
            <button :class="`${!displayRequests ? 'bg-gray-600' : 'bg-gray-925'} w-1/2 h-8 m-auto  text-xl `"
                @click="displayRequests = false">Users</button>
            <button :class="` ${displayRequests ? 'bg-gray-600' : 'bg-gray-925'} w-1/2 h-8 m-auto text-xl`"
                @click="displayRequests = true">Requests({{ requests?.length || 0 }})</button>
        </div>
        <div class=" flex-grow bg-gray-925" v-if="channelItemObj?.type == 'group'">
            <div class="flex flex-col overflow-y-auto flex-grow" v-if="!displayRequests">
                <memberItem v-for="channelObj in members" :key="channelObj.id" :channelItemObj="channelObj"
                    @newMemberAdded="async (member: channelObj) => {
            }">
                </memberItem>
            </div>
            <div class="flex flex-col overflow-y-auto flex-grow" v-else>
                <RequestItem v-for="requestObj in requests" :key="requestObj.id" :requestItemObj="requestObj"
                    @requestResolved="async (member: channelObj) => {
                const fetchedRequests = await getGroupRequests(props.channelItemObj!.id);
                requests = fetchedRequests;
                const fetchedMembers = await getGroupMembers(props.channelItemObj!.id);
                members = fetchedMembers;
            }">
                </RequestItem>
            </div>

        </div>
        <div class="bg-gray-900 h-12 mt-auto border-gray-925"></div>
    </section>
</template>