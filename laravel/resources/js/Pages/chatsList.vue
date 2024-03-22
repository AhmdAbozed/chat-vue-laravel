<script lang="ts" setup>
import { onMounted } from 'vue';
import chatItem from './chatItem.vue'
import newChatInput from './newChatInput.vue';
import { ref, watch } from 'vue';
const props = defineProps({
    chatsList: Array<any>,
    showList: Boolean
})
const emit = defineEmits(['setChat', 'setShowList'])
const displayFlag = ref(props.showList)

//rerender doesn't rerun script code on prop change, so i use watch to update it
watch(props, () => {
    displayFlag.value = props.showList
})

</script>
<template>
    <section class="sm:col-span-2 col-start-1 row-start-1 bg-gray-800 text-gray-200 text-lg flex flex-col z-20"
        v-if="props.showList">
        <div class="bg-gray-900  p-3 min-h-14 shadow-gray-800 drop-shadow-lg z-30 flex">Chats
            <button class="ml-auto" @click="emit('setShowList', false)" >

                <img src="./assets/exit.svg" class="h-8 my-auto">
            </button>
        </div>
        <div class="flex-grow bg-gray-950 bg-opacity-65" v-if="chatsList">
            <div class="flex flex-col overflow-y-auto flex-grow">
                <chatItem v-for="chatObj in props.chatsList" :key="chatObj.channel_id" :name='chatObj.receiver.name'
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