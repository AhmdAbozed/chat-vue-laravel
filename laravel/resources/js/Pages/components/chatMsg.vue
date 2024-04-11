<script lang="ts" setup>
import {inject} from 'vue'
import type {PropType} from 'vue'
import type {msgObj} from '@/Pages/util/types'
const props = defineProps({
    msgObj: Object as PropType<msgObj>,
    receiverName: String
})
//If msg belongs to current user, append it to leftside, otherwise rightside
const signedUserId = (inject('signedInUser') as any).id
</script>
<template>
    <div class="flex" v-if="(signedUserId === msgObj?.user_id)">
        <img src="../assets/prof3.svg" class="h-10">
        <div class="bg-gray-700 pb-1  pt-1 px-3 rounded-xl my-1 min-w-32 max-w-lg">
            <div class="text-xs text-gray-400">{{props.msgObj!.user}}</div>
            <div class="text-gray-100 break-words">{{ props.msgObj!.content || "message goes here" }}</div>
            <div class="text-xs text-gray-400">{{ (new Date(props.msgObj!.updated_at)).toLocaleTimeString([], {year:"numeric" ,month: "2-digit", day: "2-digit",hour: "2-digit", minute: "2-digit" }) || "12/12/2012, 12:12 PM" }}</div>
        </div>
    </div> 
    <div class="flex ml-auto mr-2" v-else>
        <div class="bg-gray-700 pb-1  pt-1 px-3 rounded-xl my-1 min-w-32 max-w-lg">
            <div class="text-xs text-gray-400">{{props.msgObj!.user}}</div>
            <div class="text-gray-100 break-words">{{ props.msgObj!.content || "message goes here" }}</div>
            <div class="text-xs text-gray-400 ">{{ (new Date(props.msgObj!.updated_at)).toLocaleTimeString([], {year:"numeric" ,month: "2-digit", day: "2-digit",hour: "2-digit", minute: "2-digit" }) || "12/12/2012, 12:12 PM" }}</div>
        </div>
        <img src="../assets/prof3.svg" class="h-10">
    </div>
</template>