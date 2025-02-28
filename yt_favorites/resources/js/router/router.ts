import {createRouter, createWebHistory} from 'vue-router';
import login from '../views/login.vue';
import home from '../views/home.vue';
import signup from '../views/signup.vue';
import favorites from '../views/favorites.vue';
import singout from '../views/logout.vue';

const routes = [
    {path:'/', component: home},
    {path:'/auth/login', component:login},
    {path:'/auth/signup', component:signup},
    {path:'/user/favorites', component:favorites},
    {path:'/auth/logout', component:singout}
];


const router = createRouter({
    history:createWebHistory(),
    routes,
});



export default router;