<script setup lang="ts">
import { ref, reactive } from 'vue';
import getXsrf from '../util/xsrf'
import { Link, router } from '@inertiajs/vue3'
const errorMsg = ref("")
const formProcessing = ref(false)

const form = reactive({
    username: null,
    password: null,
})
async function submitForm(event: Event) {
    try {
        console.log(event.currentTarget)
        console.log(event.target)
        console.log(location.protocol + "//" + location.host + "/")
        event.preventDefault();
        formProcessing.value = true;
        const xsrf = await getXsrf();
        const target = event.target as any
        const submission = { username: form.username, password: form.password }
        const options: RequestInit = {
            method: "POST",
            headers: {
                'Content-Type': 'application/json',
                'Access-Control-Allow-Credentials': 'true',
                //without decoding, %3D in token isn't converted to =, which causes token mismatch
                'X-XSRF-TOKEN': decodeURIComponent(document.cookie.split("; ").find((row) => row.startsWith("XSRF-TOKEN="))!.split("=")[1]!),
                'Accept': 'application/json'
            },
            credentials: "include",

            body: JSON.stringify(submission)
        }
        const endpoint = location.protocol + "//" + location.host + "/_api/users/login";
        const res = await fetch(endpoint, options);
        formProcessing.value = false;

        console.log(res.status)
        if (res.status == 200) {
            router.visit('/')
        }
        else {

            errorMsg.value = (await res.json()).errorMsg!
            return false
        }

        return false;

    }
    catch (e) {
        console.log(e)
        formProcessing.value = false;

    }
}

async function demoAccount() {
    try {
        console.log(location.protocol + "//" + location.host + "/")
        formProcessing.value = true;
        const xsrf = await getXsrf();
        const randomUsername = [...Array(8)].map(() => Math.random().toString(36)[2]).join('');
        const randomPassword = [...Array(8)].map(() => Math.random().toString(36)[2]).join('');
        
        const options: RequestInit = {
            method: "POST",
            headers: {
                'Content-Type': 'application/json',
                'Access-Control-Allow-Credentials': 'true',
                //without decoding, %3D in token isn't converted to =, which causes token mismatch
                'X-XSRF-TOKEN': decodeURIComponent(document.cookie.split("; ").find((row) => row.startsWith("XSRF-TOKEN="))!.split("=")[1]!),
                'Accept': 'application/json'
            },
            credentials: "include",
            body: JSON.stringify({username: randomUsername, password:randomPassword, email: randomUsername+'@mail.com'})
        }
        const endpoint = location.protocol + "//" + location.host + "/_api/users/signup/demo";
        const res = await fetch(endpoint, options);
        formProcessing.value = false;

        console.log(res.status)
        if (res.status == 200) {
            router.visit('/')
        }
        else {

            errorMsg.value = (await res.json()).errorMsg!
            return false
        }

        return false;

    }
    catch (e) {
        console.log(e)
        formProcessing.value = false;

    }
}
</script>

<template><section class="bg-gray-900 h-full">
    <div class="flex flex-col items-center justify-center px-6 py-8 mx-auto md:h-screen lg:py-0 h-full">
        <div class="w-full bg-gray-800 rounded-lg shadow border-gray-700 md:mt-0 sm:max-w-md xl:p-0 transition-transform">
            <div class="p-6 space-y-4 md:space-y-6 sm:p-8">
                <h1 class="text-xl font-bold leading-tight tracking-tight text-white md:text-2xl">
                    Sign in to your account
                </h1>
                <form class="space-y-4 md:space-y-6" METHOD="post" @submit="submitForm">
                    <div>
                        <label for="email" class="block mb-2 text-sm font-medium text-white">Username</label>
                        <input v-model="form.username" type="text" name="username" id="username" minlength="3" required 
                            class="bg-gray-700 border-gray-600 text-white sm:text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5"
                            placeholder="Username">
                    </div>
                    <div>
                        <label for="password" class="block mb-2 text-sm font-medium text-white">Password</label>
                        <input v-model="form.password" type="password" name="password" id="password" minlength="5" required
                            placeholder="••••••••"
                            class="bg-gray-700 border-gray-600 text-white sm:text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5">
                    </div>
                    <div class="flex items-center justify-between">
                        <div class="flex items-start">
                            <div class="flex items-center h-5">
                                <input id="remember" aria-describedby="remember" type="checkbox"
                                    class="w-4 h-4 border border-gray-600 rounded bg-gray-700 focus:ring-3 focus:ring-blue-600 ring-offset-gray-800">
                            </div>
                            <div class="ml-3 text-sm">
                                <label for="remember" class="text-gray-300">Remember me</label>
                            </div>
                        </div>

                        <a href="#" class="text-sm font-medium text-blue-500 hover:underline">Forgot password?</a>
                    </div>

                    <button type="submit" @click="() => { console.log('Ive been clicked') }"
                        class="w-full text-white disabled:opacity-50  focus:ring-4 focus:outline-none  font-medium rounded-lg text-sm px-5 py-2.5 text-center bg-blue-600 hover:bg-blue-700 focus:ring-blue-800"
                        :disabled="formProcessing">Sign in</button>
                    <button @click="(e) => { e.preventDefault(); demoAccount() }"
                        class="w-full text-white disabled:opacity-50  focus:ring-4 focus:outline-none font-medium rounded-lg text-sm px-5 py-2.5 text-center bg-blue-600 hover:bg-blue-700 focus:ring-blue-800"
                        :disabled="formProcessing">Demo Account</button>

                    <div class="flex justify-center text-sm text-red-500">
                        {{ errorMsg }}
                    </div>
                    <p class="text-sm font-light text-gray-400">
                        Don’t have an account yet?
                        <Link href="/signup" class="font-medium text-blue-500 hover:underline">
                        Sign up
                        </Link>
                    </p>
                </form>
            </div>
        </div>
    </div>
</section>
</template>