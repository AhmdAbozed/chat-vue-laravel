import './assets/main.css'
import './index.css'
import { createApp } from 'vue'
import App from './App.vue'
import SignUp from './components/auth/SignUp.vue'
import Login from './components/auth/LogIn.vue'
import { createRouter, createWebHashHistory } from 'vue-router'
import homePage from './components/homePage.vue'

const routes = [
    { path: '/', component: homePage },
    { path: '/login', component: Login },
    { path: '/signup', component: SignUp },
    
]

// 3. Create the router instance and pass the `routes` option
// You can pass in additional options here, but let's
// keep it simple for now.
const router = createRouter({
    // 4. Provide the history implementation to use. We
    // are using the hash history for simplicity here.
    history: createWebHashHistory(),
    routes, // short for `routes: routes`
})
const app = createApp(App)
app.use(router)
app.mount('#app')
