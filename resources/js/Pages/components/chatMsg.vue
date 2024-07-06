<script lang="ts" setup>
import {inject} from 'vue'
import type {PropType} from 'vue'
import type {fileHeaderData, msgObj} from '@/Pages/util/types'
import {formatFileSize, isImage} from '@/Pages/util/misc'
const props = defineProps({
    msgObj: Object as PropType<msgObj>,
    fileHeaderData: Object as PropType<fileHeaderData>,
    channelId: Number
});
//If msg belongs to current user, append it to leftside, otherwise rightside
const signedUser:any = inject('signedInUser');
function getFileUrl(){
    return props.fileHeaderData?.fileUrl + '/file/abozedchatapp/channel'+props.channelId+'/'+props.msgObj!.file_name+'?Authorization='+props.fileHeaderData?.fileToken+'&b2ContentDisposition=attachment'
};
</script>
<template>
    <div :class="`${signedUser.id === msgObj?.user_id ? '' : 'ml-auto mr-2'} flex `">
        <img src="../assets/prof3.svg" class="h-10" v-if="(signedUser.id === msgObj?.user_id)">
        <div class="">     
            <div class="bg-gray-700 pb-1  pt-1 px-3  rounded-lg mt-1 z-10 relative flex flex-col ">
                <div class="text-xs text-gray-400">{{props.msgObj!.user}}</div>
                <div class="text-gray-100 [overflow-wrap:anywhere] max-w-xl">{{ props.msgObj!.content || "message goes here" }}</div>
                
                <div class="text-xs text-gray-400">{{ (new Date(props.msgObj!.updated_at)).toLocaleTimeString([], {year:"numeric" ,month: "2-digit", day: "2-digit",hour: "2-digit", minute: "2-digit" }) || "12/12/2012, 12:12 PM" }}</div>
                
            </div>
            <div :class="`flex ${isImage(props.msgObj.file_name) ? 'flex-col' : 'flex-row'} bg-slate-900 rounded-b-xl -translate-y-1 p-2`" v-if="props.msgObj?.file_name">
                    <img src="../assets/file.svg" class="w-6" alt="" srcset="" v-if="!isImage(props.msgObj.file_name)">
                    <img :src="getFileUrl()" class="object-contain  max-w-xl w-full"  alt="" srcset="" v-if="isImage(props.msgObj.file_name)">
                    <a class="text-gray-300 flex flex-row w-full" :href="getFileUrl()" target="_blank" download>
                        <div class="mr-2">
                            <div>{{props.msgObj?.file_name}}</div>
                            <div class="text-gray-500 text-xs">{{formatFileSize(props.msgObj.file_size!)}}</div>
                        </div>
                        <img src="../assets/d6.svg" class="w-8 ml-auto" alt="" srcset="">
                    </a>
            </div>
        </div>
        <img src="../assets/prof3.svg" class="h-10" v-if="!(signedUser.id === msgObj?.user_id)">
        
    </div> 
</template>