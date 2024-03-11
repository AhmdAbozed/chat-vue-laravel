<script lang="ts" setup>
const props = defineProps({
    msg: String,
    date: String,
})
//translate-y-9 translate-x-12
</script>
<template>
    <div class="flex">
        <img src="../assets/prof3.svg" class="h-10">
        <div class="bg-gray-700 pb-1  pt-1 px-3 rounded-xl my-1 min-w-32 max-w-lg">
            <div class="text-gray-100 break-words">{{ msg || "message goes here" }}</div>
            <div class="text-xs">{{ date || "12/12/2012, 12:12 PM" }}</div>

        </div>
    </div>
</template>