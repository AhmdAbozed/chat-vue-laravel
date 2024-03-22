<script setup lang="ts">
import getXsrf from '../util/xsrf';
import { Link } from '@inertiajs/vue3'
import { ref } from 'vue';

const formProcessing = ref(false)
const errorMsg = ref("")

async function submitForm(event: Event) {
    try {
        console.log("err")
        event.preventDefault();
        formProcessing.value = true;
        const xsrf = await getXsrf();
        const target = event.target as any
        const submission = { Username: target.elements.username.value, Password: target.elements.password.value, Email: target.elements.email.value }
        const options: RequestInit = {
            method: "POST",
            headers: {
                'Content-Type': 'application/json',
                'Access-Control-Allow-Credentials': 'true',
                'X-XSRF-TOKEN': decodeURIComponent(document.cookie.split("; ").find((row) => row.startsWith("XSRF-TOKEN="))!.split("=")[1]!)

            },
            credentials: "include",

            body: JSON.stringify(submission)
        }
        const endpoint = location.protocol+"//"+location.host+"/api/users/signup"
        const res = await fetch(endpoint, options);
        console.log(res.status)

        //const resJSON = await res.json()


        if (res.status == 200) {
            console.log(res.status)
            //document.getElementById("result")!.innerHTML = "200. Response recieved"
        }
        else if(res.status == 403){
            errorMsg.value = (await res.json())!

            formProcessing.value = false;
            console.log("invalid credentials: " + res.status)
        } else {
            errorMsg.value = ("Something went wrong. Try again later.")!

            formProcessing.value = false;
            
        }
        return res;
    }
    catch (e) {
        console.log(e)
        formProcessing.value = false;

    }
}
</script>

<template>
    <section class="bg-gray-50 dark:bg-gray-900 h-full">
        <div class="flex flex-col items-center justify-center px-6 py-8 mx-auto md:h-screen lg:py-0 bg h-full">

            <div
                class="w-full bg-white rounded-lg shadow dark:border md:mt-0 sm:max-w-md xl:p-0 dark:bg-gray-800 dark:border-gray-700 transition-transform ">
                <div class="p-6 space-y-4 md:space-y-6 sm:p-8">
                    <h1
                        class="text-xl font-bold leading-tight tracking-tight text-gray-900 md:text-2xl dark:text-white">
                        Create an account
                    </h1>
                    <form class="space-y-4 md:space-y-6" action="#" @submit="submitForm">
                        <div>
                            <label for="email"
                                class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Email</label>
                            <input type="email" name="email" id="email" required
                                class="bg-gray-50 border border-gray-300 text-gray-900  sm:text-sm rounded-lg focus:ring-blue-600 focus:border-blue-600 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500"
                                placeholder="Email">
                        </div>
                        <div>
                            <label for="username"
                                class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Username</label>
                            <input type="text" name="username" id="username" required
                                class="bg-gray-50 border border-gray-300 text-gray-900  sm:text-sm rounded-lg focus:ring-blue-600 focus:border-blue-600 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500"
                                placeholder="Username">
                        </div>
                        <div>
                            <label for="password"
                                class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Password</label>
                            <input type="password" name="password" id="password" placeholder="••••••••" required
                                class="bg-gray-50 border border-gray-300  text-gray-900 sm:text-sm rounded-lg focus:ring-blue-600 focus:border-blue-600 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500">
                        </div>
                        <div class="flex items-center justify-between">
                            <div class="flex items-start">

                            </div>
                        </div>
                        <button type="submit" :disabled="formProcessing"
                            class="w-full text-white  disabled:opacity-50 hover:bg-blue-700 focus:ring-4 focus:outline-none focus:ring-blue-300 font-medium rounded-lg text-sm px-5 py-2.5 text-center dark:bg-blue-600 dark:hover:bg-blue-700 dark:focus:ring-blue-800">Sign
                            Up</button>
                        <div class="flex justify-center text-sm text-red-500">
                            {{ errorMsg }}
                        </div>
                        <p class="text-sm font-light text-gray-500 dark:text-gray-400">
                            Already have an Account? <Link href="/login"
                                class="font-medium text-blue-600 hover:underline dark:text-blue-500">Login</Link>
                        </p>
                    </form>
                </div>
            </div>
        </div>
    </section>
</template>