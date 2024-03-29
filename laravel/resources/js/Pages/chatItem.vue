<script lang="ts" setup>
import { ref, inject, watchEffect, watch, computed} from 'vue'
import type { channelObj } from './homePage.vue';
const props = defineProps({
    channel_id: Number,
    userName: String
})
const emit = defineEmits(['setChatChild'])


const activeChannel = inject('currentChannelInjection')  as any
const activeStatus = ref(false)

watch(activeChannel, ()=>{
    //is this channel the active channel or not? for highlighting
    activeStatus.value = activeChannel.value.id == props.channel_id
}, {immediate: true})

</script>
<template>
    <div>
        <div :class="`${activeStatus ? 'bg-gray-600' : 'bg-gray-700'} w-full min-h-16 mb-4 shadow-md flex`"
            @click="() => { console.log('click'); emit('setChatChild', { id: channel_id, receiver: { name: userName } } as channelObj) }">
            <img src="./assets/prof3.svg" class="h-12 my-auto">
            <div class="flex flex-col my-auto translate-y-0.5 ml-1">
                <div class="text-gray-100">{{ props.userName }}</div>
            </div>
        </div>
    </div>
</template>