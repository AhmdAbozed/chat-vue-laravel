<script lang="ts" setup>
import type { PropType } from 'vue';
import {ref} from 'vue'
const props = defineProps({
    signedUser: Object as PropType<{ id: number, name: string, email: string, upgraded: boolean }>
})
const duration = ref('monthly'); 
const openGateway = () => {
    open("https://secure.2checkout.com/checkout/buy?merchant=255009297353&tpl=default&prod=1B708CQUVU&qty=1", "_blank")
}
</script>
<template>
    <div class='flex min-h-screen bg-gray-900 pt-7 px-1'>
        <div class="mx-auto flex flex-col">
            <p class="text-white text-lg leading-10 font-semibold">
                Your Subscription
            </p>

            <div>
                <p class="text-gray-400 text-sm  font-medium">
                    Upgrade now to have unlimited access to all features
                </p>
            </div>

            <div class=" mt-7 flex ">
                <button @click="duration = 'monthly'"
                    class="border-r-2 border-blue-600 text-white  hover:bg-blue-700 focus:ring-4 focus:outline-none  text-center bg-blue-600 dark:hover:bg-blue-500 focus:ring-blue-800 text-sm font-semibold py-4 px-6 rounded-l-lg">
                    Monthly
                </button>
                <button @click="duration = 'annually'"
                    class="border-none text-white  focus:ring-4 focus:outline-none  text-center bg-blue-700 hover:bg-blue-500 focus:ring-blue-800 text-sm font-semibold py-4 px-6 rounded-r-lg">
                    Annual
                </button>
            </div>

            <div class="mt-5 grid grid-cols-2 max-w-4xl gap-3">
                <div key="1"
                    class="w-full  rounded-lg flex flex-col shadow border md:mt-0 sm:max-w-md xl:p-0 bg-gray-800 border-gray-700">
                    <div class="pt-4 pl-6 pb-6 ">
                        <div class="flex justify-end">
                            <div class="bg-blue-600 rounded-lg flex justify-center align-center px-3 mr-3">
                                <p class="text-white text-xs leading-7  font-bold ">
                                    Starter
                                </p>
                            </div>
                        </div>

                        <div class="text-white">
                            <p class=" sm:text-4xl text-3xl my-2 font-bold">
                                Starter
                            </p>
                        </div>

                        <div class="text-gray-400">
                            <p>
                                Unlimited Private Chat
                            </p>
                            <p>
                                5 Groups Max
                            </p>
                            <p>
                                10 Members Max
                            </p>
                        </div>
                    </div>

                    <div class="text-gray-400 py-6 pl-6 pr-3 ">
                        <div>
                            <p class=" text-sm mb-1 font-medium">
                                Suitable for small communities and private chat with individuals only
                            </p>
                        </div>
                    </div>
                    <p class="text-4xl text-white font-semibold pl-6 mt-auto">
                        Free
                    </p>

                    <div class=" mt-3 pb-6 px-6">
                        <button class="bg-blue-600 rounded-lg py-3 px-4 text-white text-sm font-semibold">{{
                            props.signedUser?.upgraded
                                ? 'Downgrade -' : 'Current Plan' }}
                        </button>
                    </div>
                </div>
                <div key="1"
                    class="w-full  rounded-lg flex flex-col  shadow border md:mt-0 sm:max-w-md xl:p-0 bg-gray-800 border-gray-700 transition-transform">
                    <div class="pt-4 pl-6 pb-6">
                        <div class="flex justify-end">
                            <div class="bg-blue-600 rounded-lg flex justify-center align-center px-3 mr-3">
                                <p class="text-white text-xs leading-7  font-bold">
                                    Pro
                                </p>
                            </div>
                        </div>

                        <div class="text-white">
                            <p class=" sm:text-4xl text-3xl my-2 font-bold">
                                Pro
                            </p>
                        </div>

                        <div class="text-gray-400 font-medium">
                            <p>
                                Unlimited Private Chat
                            </p>
                            <p>
                                Unlimited Groups
                            </p>
                            <p>
                                Unlimited Members
                            </p>
                        </div>
                    </div>

                    <div class="text-gray-400 py-6 pl-6 pr-3 ">
                        <div>
                            <p class=" text-sm mb-1 font-medium">
                                Suitable for Communities of any size
                            </p>
                        </div>
                    </div>
                    <p class="text-4xl text-white font-semibold pl-6 mt-auto">
                        <span class="inline-block  text-2xl -translate-y-3 -translate-x-1">$</span>{{duration == 'monthly' ? '9.99' : '99.99'}} <span
                            class="text-xl inline-block -translate-x-2">{{duration == 'monthly' ? '/Mo' : '/Yr'}}</span>
                    </p>

                    <div class=" mt-3 pb-6 px-6">
                        <button @click="openGateway()" :disabled="props.signedUser?.upgraded"
                            class="bg-blue-600 rounded-lg py-3 px-4 text-white text-sm font-semibold mt-auto">{{
                            props.signedUser?.upgraded
                                ? 'Current Plan' : 'Upgrade +' }}
                        </button>
                    </div>

                </div>
            </div>
        </div>
    </div>
</template>