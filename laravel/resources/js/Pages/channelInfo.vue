<script lang="ts" setup>
import { ref, watch } from 'vue';
import type { channelObj } from './homePage.vue';
const props = defineProps({
    channelItemsList: Array<channelObj>,
    showList: Boolean
})
const emit = defineEmits(['setChat', 'setShowList', 'newChatAdded'])
const displayRequests = ref(false);
//rerender doesn't rerun script code on prop change without watching
</script>
<template>
</template>