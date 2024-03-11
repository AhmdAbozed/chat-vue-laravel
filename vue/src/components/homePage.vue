<script lang="ts" setup>
import chatMsg from './chatMsg.vue';
import chatItem from './chatItem.vue'
</script>
<template>
    <div class="grid grid-rows-1 grid-cols-8 h-svh">
        <section class="col-span-2 bg-gray-800 text-gray-200 text-lg flex flex-col">
            <div class="bg-gray-900  p-3 min-h-14 shadow-gray-800 drop-shadow-lg z-10">Chats</div>
            <div class="flex flex-col overflow-y-auto flex-grow bg-gray-900">
                <chatItem></chatItem>
                <chatItem></chatItem>
                <chatItem></chatItem>
            </div>
        </section>
        <section class="col-span-6 flex flex-col">
            <div class="bg-gray-900 p-3 h-14 shadow-gray-800 drop-shadow-lg z-10"></div>
            <div class="bg-gray-800 h-full items-start flex flex-col-reverse flex-nowrap">
                <chatMsg></chatMsg>
                <chatMsg></chatMsg>
                <chatMsg></chatMsg>
            </div>
            <div class="flex p-2">
                <input type="text" class="h-8 flex-grow bg-gray-700 border-2 border-gray-800 rounded-full">
                <input type="button" value="Submit">
            </div>
        </section>
    </div>
</template>