<script lang="ts" setup>
import { ref, inject, watchEffect, watch, computed } from 'vue'
import type {PropType} from 'vue'
import type {channelObj} from '@/Pages/util/types'
import { EchoObj } from '../util/echo';
const props = defineProps({
    channelItemObj: Object as PropType<channelObj>
})
const emit = defineEmits(['setChatChild'])
const unreadCount = ref(props.channelItemObj!.unreadCount)

const activeChannel = inject('currentChannelInjection') as any
const activeStatus = ref(false)
//when active, both msg listen and unread listen catch event
function listenToChannel() {
    EchoObj.private("chat." + props.channelItemObj!.id).listen(".NewMsgSent", async (e: any) => {
        //if opened channel, don't increment unread
        if (!activeStatus.value) {
            unreadCount.value! += 1
            console.log(unreadCount.value)
        } else {
            unreadCount.value! = 0
        }
    })
}

listenToChannel()

watch(activeChannel, () => {
    //is this channel the active channel or not? for highlighting
    activeStatus.value = activeChannel.value.id == props.channelItemObj!.id
    if (activeStatus.value) {
        unreadCount.value! = 0
    }
}, { immediate: true })

</script>
<template>
    <div>
        <div :class="`${activeStatus ? 'bg-gray-600' : 'bg-gray-700'} w-full min-h-16 mb-4 shadow-md flex`"
            @click="() => { emit('setChatChild', { id: props.channelItemObj!.id, name: props.channelItemObj!.name, type: props.channelItemObj!.type} as channelObj) }">
            <img src="../assets/prof3.svg" class="h-12 my-auto">
            <div class="flex flex-col my-auto translate-y-0.5 ml-1">
                <div class="text-gray-100">{{ props.channelItemObj!.name }}</div>
            </div>

            <div class="text-white rounded-full w-7 h-7 bg-gray-400 ml-auto my-auto mr-2 flex justify-center" v-if="unreadCount">{{ unreadCount }}</div>
        </div>
    </div>
</template>