<script lang="ts" setup>
const props = defineProps({
    msgObj: Object,
    rightSide: Boolean
})
</script>
<template>
    <div class="flex" v-if="!rightSide">
        <img src="./assets/prof3.svg" class="h-10">
        <div class="bg-gray-700 pb-1  pt-1 px-3 rounded-xl my-1 min-w-32 max-w-lg">
            <div class="text-xs text-gray-400">{{props.msgObj!.name}}</div>
            <div class="text-gray-100 break-words">{{ props.msgObj!.message.content || "message goes here" }}</div>
            <div class="text-xs text-gray-400">{{ (new Date(props.msgObj!.message.updated_at)).toLocaleTimeString([], {year:"numeric" ,month: "2-digit", day: "2-digit",hour: "2-digit", minute: "2-digit" }) || "12/12/2012, 12:12 PM" }}</div>
        </div>
    </div> 
    <div class="flex ml-auto mr-2" v-else>
        <div class="bg-gray-700 pb-1  pt-1 px-3 rounded-xl my-1 min-w-32 max-w-lg">
            <div class="text-xs text-gray-400">{{props.msgObj!.name}}</div>
            <div class="text-gray-100 break-words">{{ props.msgObj!.message.content || "message goes here" }}</div>
            <div class="text-xs text-gray-400 ">{{ (new Date(props.msgObj!.message.updated_at)).toLocaleTimeString([], {year:"numeric" ,month: "2-digit", day: "2-digit",hour: "2-digit", minute: "2-digit" }) || "12/12/2012, 12:12 PM" }}</div>
        </div>
        <img src="./assets/prof3.svg" class="h-10">
    </div>
</template>