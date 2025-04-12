<script lang="ts" setup>
import channelItem from '../components/channelItem.vue'
import groupsPanel from './groupsPanel.vue'
import newChatInput from '../components/newChatInput.vue';
import { ref, watch } from 'vue';
import type { channelObj } from '@/Pages/util/types'
const props = defineProps({
    channelItemsList: Array<channelObj>,
    showList: Boolean
})
const emit = defineEmits(['setChannel', 'setShowList', 'newChatAdded'])
const displayGroup = ref(false);
</script>
<template>
    <section
        class="sm:w-96 sm:max-w-[25vw] col-start-1 row-start-1 bg-gray-800 text-gray-200 text-lg flex flex-col z-20 "
        v-if="props.showList">
        <div class="bg-gray-900  p-3 min-h-14 shadow-gray-800 drop-shadow-lg z-30 flex">Chats

            <button class="ml-auto" @click="displayGroup = !displayGroup">

                <img src="../assets/group2.svg" class="h-6 my-auto">
            </button>
            <button class="ml-3" @click="displayGroup = false; emit('setShowList', false)">

                <img src="../assets/exit.svg" class="h-8 my-auto">
            </button>
        </div>
        <div class=" flex flex-col relative flex-grow min-h-0">
            <div class="flex-grow flex flex-col bg-gray-900 absolute animate-slideIn h-full z-10 overflow-hidden"
                v-if="displayGroup">
                <groupsPanel
                    @newChatAdded="(e: any) => { console.log('first newchat emit reached(group)'); emit('newChatAdded', true); }">
                </groupsPanel>
            </div>
            <div class="flex flex-grow flex-col bg-gray-925 overflow-y-auto" v-if="channelItemsList">
                <channelItem v-for="channelObj in channelItemsList" :key="channelObj.id"
                    :channelIndex="channelItemsList.indexOf(channelObj)" :channelItemObj="channelObj"
                    @setChatChild="(channelId: number) => { console.log('emit', channelId); emit('setChannel', channelId) }">
                </channelItem>
            </div>
            <div class="flex-grow bg-gray-925 text-gray-400" v-else>
                No Chats yet..
            </div>

            <newChatInput
                @newChatAdded="(e: any) => { console.log('first emit reached'); emit('newChatAdded', true); }">
            </newChatInput>

        </div>


    </section>
</template>