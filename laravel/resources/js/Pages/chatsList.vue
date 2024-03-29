<script lang="ts" setup>
import chatItem from './chatItem.vue'
import newChatInput from './newChatInput.vue';
import { ref, watch } from 'vue';
import type { channelObj } from './homePage.vue';
const props = defineProps({
    channelItemsList: Array<channelObj>,
    showList: Boolean
})
const emit = defineEmits(['setChat', 'setShowList', 'newChatAdded'])
//rerender doesn't rerun script code on prop change without watching
</script>
<template>
    <section class="sm:col-span-2 col-start-1 row-start-1 bg-gray-800 text-gray-200 text-lg flex flex-col z-20"
        v-if="props.showList">
        <div class="bg-gray-900  p-3 min-h-14 shadow-gray-800 drop-shadow-lg z-30 flex">Chats
            <button class="ml-auto" @click="emit('setShowList', false)">

                <img src="./assets/exit.svg" class="h-8 my-auto">
            </button>
        </div>
        <div class="flex-grow bg-gray-950 bg-opacity-65" v-if="channelItemsList">
            <div class="flex flex-col overflow-y-auto flex-grow">
                <chatItem v-for="channelObj in props.channelItemsList" :key="channelObj.id"
                    :userName='channelObj.receiver.name' :channel_id='channelObj.id'
                    @setChatChild="(chat) => { console.log('emit'); emit('setChat', chat) }"></chatItem>
            </div>
        </div>
        <div class="flex-grow bg-gray-950 bg-opacity-65 text-gray-400" v-else>
            No Chats yet..
        </div>


        <newChatInput @newChatAdded="(e: any) => { console.log('first emit reached'); emit('newChatAdded', true) }">
        </newChatInput>
    </section>
</template>