<script lang="ts" setup>
import { onMounted } from 'vue';
import chatItem from './chatItem.vue'
import newChatInput from './newChatInput.vue';
import { ref } from 'vue';
import { useRouter } from 'vue-router'
const router = useRouter()
const props = defineProps({
    chatsList: Array<any>,
})
const emit = defineEmits(['setChat'])
console.log("chatslist: "+props.chatsList)
</script>
<template>
    <section class="col-span-2 bg-gray-800 text-gray-200 text-lg flex flex-col">
        <div class="bg-gray-900  p-3 min-h-14 shadow-gray-800 drop-shadow-lg z-10">Chats</div>
        <div class="flex-grow bg-gray-950 bg-opacity-65" v-if="chatsList">
            <div class="flex flex-col overflow-y-auto flex-grow">
                <chatItem v-for="chatObj in props.chatsList" :key="chatObj.channel_id" :name='chatObj.receiver[0].name'
                    :channel_id='chatObj.channel_id'
                    @setChatChild="(chat) => { console.log('emit'); emit('setChat', chat) }"></chatItem>
            </div>
        </div>
        <div class="flex-grow bg-gray-950 bg-opacity-65 text-gray-400" v-else>
            No Chats yet..
        </div>


        <newChatInput></newChatInput>
    </section>
</template>