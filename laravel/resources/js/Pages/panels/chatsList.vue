<script lang="ts" setup>
import chatItem from '../components/chatItem.vue'
import groupsPanel from './groupsPanel.vue'
import newChatInput from '../components/newChatInput.vue';
import { ref, watch } from 'vue';
import type {channelObj} from '@/Pages/util/types'
const props = defineProps({
    channelItemsList: Array<channelObj>,
    showList: Boolean
})
const emit = defineEmits(['setChat', 'setShowList', 'newChatAdded'])
const displayGroup = ref(false);
</script>
<template>
    <section class="sm:w-96 sm:max-w-[25vw] col-start-1 row-start-1 bg-gray-800 text-gray-200 text-lg flex flex-col z-20 "
        v-if="props.showList">
        <div class="bg-gray-900  p-3 min-h-14 shadow-gray-800 drop-shadow-lg z-30 flex">Chats

            <button class="ml-auto" @click="displayGroup = !displayGroup">

                <img src="../assets/group2.svg" class="h-6 my-auto">
            </button>
            <button class="ml-3" @click="displayGroup=false; emit('setShowList', false)">

                <img src="../assets/exit.svg" class="h-8 my-auto">
            </button>
        </div>
        <div class="flex-grow flex flex-col relative">
            <div class="flex-grow flex flex-col bg-gray-900 absolute animate-slideIn h-full z-10 overflow-hidden" v-if="displayGroup">
                <groupsPanel></groupsPanel>
            </div>
            <div class=" flex-grow bg-gray-925 " v-if="channelItemsList">
                <div class="flex flex-col overflow-y-auto flex-grow ">
                    <chatItem v-for="channelObj in props.channelItemsList" :key="channelObj.id"
                        :channelItemObj="channelObj"
                        @setChatChild="(chat) => { console.log('emit'); emit('setChat', chat) }"></chatItem>
                </div>
            </div>
            <div class="flex-grow bg-gray-925 text-gray-400" v-else>
                No Chats yet..
            </div>


            <newChatInput @newChatAdded="(e: any) => { console.log('first emit reached'); emit('newChatAdded', true) }">
            </newChatInput>

        </div>


    </section>
</template>