<script lang="ts" setup>
import type {PropType} from 'vue';
import { ref, inject, watchEffect, watch, computed } from 'vue'
import type {channelObj, requestObj} from '@/Pages/util/types'
import { EchoObj } from '../util/echo';
import getXsrf from '../util/xsrf';
const props = defineProps({
    requestItemObj: Object as PropType<requestObj>
})
const emit = defineEmits(['requestResolved'])
const processing = ref(false)
const limitExceeded = ref(false)

async function resolveRequest(accept: boolean) {
    try {
        processing.value = true;
        const xsrf = await getXsrf();
        const options: RequestInit = {
            method: "POST",
            headers: {
                'Content-Type': 'application/json',
                'Access-Control-Allow-Credentials': 'true',
                //without decoding, %3D in token isn't converted to =, which causes token mismatch
                'X-XSRF-TOKEN': decodeURIComponent(document.cookie.split("; ").find((row) => row.startsWith("XSRF-TOKEN="))!.split("=")[1]!),
                'Accept':'application/json'
            },
            credentials: "include",

            body: JSON.stringify({accepted: accept})
        }
        const endpoint = location.protocol+"//"+location.host+"/_api/requests/"+props.requestItemObj?.id;
        console.log(endpoint)
        const res = await fetch(endpoint, options);
        processing.value = false;
        console.log(res.status)

        if (res.status == 200) {
            console.log(res.status)
            emit("requestResolved")
            return true
        }
        else if(res.status == 403){
            limitExceeded.value = true;
            setTimeout(() => {
                limitExceeded.value = false;
            }, 3000);
            return false;
        }
        else {
            return false
        }
    }
    catch (e) {
        console.log(e)
        processing.value = false;

    }
}

</script>
<template>
    <div>
        <div class="bg-gray-700 w-full min-h-16 mb-4 shadow-md flex ">
            <img src="../assets/prof3.svg" class="h-12 my-auto">
            <div class="flex flex-row my-auto translate-y-0.5 ml-1 w-full">
                <div class="text-gray-100 mt-1">{{ props.requestItemObj!.name }}</div>
                <button class="ml-auto mr-1 mb-1 hover:opacity-75" @click="()=>{resolveRequest(true)}" :disabled="processing">

                    <img src="../assets/check5.svg" class="h-9 my-auto ">
                </button>
                <button class="mr-1 mb-1 hover:opacity-75" @click="()=>{resolveRequest(false)}" :disabled="processing">

                    <img src="../assets/reject.svg" class="h-[2.4rem] my-auto">
                </button>
            </div>
        </div>
        <div class=" text-gray-400 px-2 py-1 rounded-lg absolute bottom-12 ml-2 bg-gray-950 bg-opacity-65 animate-fadeInOut"
            role="alert" v-if="limitExceeded">
            <span class="block sm:inline">Member limit exceeded, upgrade to add more</span>
        </div>
    </div>
</template>