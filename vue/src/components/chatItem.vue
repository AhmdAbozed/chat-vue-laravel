<script lang="ts" setup>
const props = defineProps({
    name: String,
    lastMsg: String,
})
</script>
<template>
    <div>
        <div class="bg-gray-700 w-full min-h-16 mb-4 shadow-md flex">
            <img src="../assets/prof3.svg" class="h-12 my-auto">
            <div class="flex flex-col my-auto translate-y-0.5 ml-1">
                <div class="text-gray-100">{{ name || "name goes here" }}</div>
                <div class="text-xs">{{ lastMsg || "last msg goes here" }}</div>

            </div>
        </div>
    </div>
</template>