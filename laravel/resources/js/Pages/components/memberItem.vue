<script lang="ts" setup>
import { ref, inject, watchEffect, watch, computed } from 'vue'
import type {PropType} from 'vue'
import type {channelObj} from '@/Pages/util/types'
import { EchoObj } from '../util/echo';
const props = defineProps({
    channelItemObj: Object as PropType<channelObj>
})
const emit = defineEmits(['newMemberAdded'])
const unreadCount = ref(props.channelItemObj!.unreadCount)


</script>
<template>
    <div>
        <div class="bg-gray-700 w-full min-h-16 mb-4 shadow-md flex"
            @click="() => { emit('newMemberAdded', { id: props.channelItemObj!.id, name: props.channelItemObj!.name} as channelObj) }">
            <img src="../assets/prof3.svg" class="h-12 my-auto">
            <div class="flex flex-col my-auto translate-y-0.5 ml-1">
                <div class="text-gray-100">{{ props.channelItemObj!.name }}</div>
            </div>

            <div class="text-white rounded-full w-7 h-7 bg-gray-400 ml-auto my-auto mr-2 flex justify-center" v-if="unreadCount">{{ unreadCount }}</div>
        </div>
      
    </div>
</template>