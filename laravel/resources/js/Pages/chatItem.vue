<script lang="ts" setup>
const props = defineProps({
    channel_id: Number,
    name: String
})
const emit = defineEmits(['setChatChild'])
</script>
<template>
    <div>
        <div class="bg-gray-700 w-full min-h-16 mb-4 shadow-md flex" @click="()=>{console.log('click');emit('setChatChild', {channel_id: channel_id, receiver: name})}">
            <img src="./assets/prof3.svg" class="h-12 my-auto">
            <div class="flex flex-col my-auto translate-y-0.5 ml-1">
                <div class="text-gray-100">{{ props.name }}</div>
            </div>
        </div>
    </div>
</template>