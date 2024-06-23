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
            @click="() => { emit('setChatChild', props.channelItemObj?.id) }">
            <img src="@/Pages/assets/prof3.svg" class="h-12 my-auto" v-if="props.channelItemObj!.type === 'private'">
            <img src="@/Pages/assets/g1.svg" class="h-11 my-auto" v-else-if="props.channelItemObj!.type === 'group'">
            <div class="flex flex-col my-auto translate-y-0.5 ml-1 overflow-x-hidden overflow-ellipsis">
                <div class="text-gray-100">{{ props.channelItemObj!.name }}</div>
            </div>
            <div class="text-white rounded-full w-7 h-7 bg-gray-400 ml-auto my-auto mr-2 flex justify-center" v-if="props.channelItemObj?.unreadCount">{{ props.channelItemObj?.unreadCount }}</div>
        </div>
    </div>
</template>