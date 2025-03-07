<script setup lang="ts">
  import { onMounted,nextTick, ref } from 'vue';
  import axios from 'axios';
  import { useRouter } from 'vue-router'
import { useAuthStore } from '../stores/authStore';

  const router = useRouter();
  const email = ref('');
  const password = ref('');
  const errorMessage = ref('');

  const authStore= useAuthStore();
  onMounted(()=>{
    if(authStore.isLoggedIn){
      router.push('/');
    }
    
  })

  const login = async() => {
    errorMessage.value='';
    try{

      const response = await axios.post('/api/login', { 
        email: email.value.trim() , 
        password: password.value.trim()}); 
      if(response.data.token){

        authStore.login(response.data.token);
      
        nextTick(()=>{
          router.push('/');
        });
        
      }
      else{
        errorMessage.value = response.data.message;
      }
    }catch(error: any){
      console.error('Login failed', error);
      errorMessage.value = error.response?.data?.message;
    }
  };



</script>




<template>
    
    <div class="flex min-h-full flex-1 flex-col justify-center px-6 py-12 lg:px-8">
      <div class="sm:mx-auto sm:w-full sm:max-w-sm">
        <img class="mx-auto h-10 w-auto" src="https://tailwindui.com/plus-assets/img/logos/mark.svg?color=indigo&shade=600" alt="Your Company" />
        <h2 class="mt-10 text-center text-2xl/9 font-bold tracking-tight text-gray-900">Sign in to your account</h2>
      </div>
  
      <div class="mt-10 sm:mx-auto sm:w-full sm:max-w-sm">
        <form class="space-y-6" @submit.prevent="login" method="POST">
          <div>
            <label for="email" class="block text-sm/6 font-medium text-gray-900">Email address</label>
            <div class="mt-2">
              <input type="email" v-model="email" name="email" id="email" autocomplete="email" required class="block w-full rounded-md bg-white px-3 py-1.5 text-base text-gray-900 outline outline-1 -outline-offset-1 outline-gray-300 placeholder:text-gray-400 focus:outline focus:outline-2 focus:-outline-offset-2 focus:outline-indigo-600 sm:text-sm/6" />
            </div>
          </div>
  
          <div>
            <div class="flex items-center justify-between">
              <label for="password" class="block text-sm/6 font-medium text-gray-900">Password</label>
              
            </div>
            <div class="mt-2">
              <input type="password" v-model="password" id="password" autocomplete="current-password" required class="block w-full rounded-md bg-white px-3 py-1.5 text-base text-gray-900 outline outline-1 -outline-offset-1 outline-gray-300 placeholder:text-gray-400 focus:outline focus:outline-2 focus:-outline-offset-2 focus:outline-indigo-600 sm:text-sm/6" />
            </div>
          </div>
  
          <div>
            <button type="submit" class="flex w-full justify-center rounded-md bg-indigo-600 px-3 py-1.5 text-sm/6 font-semibold text-white shadow-sm hover:bg-indigo-500 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-indigo-600">Sign in</button>
          </div>
        </form>
        <p v-if="errorMessage" class="error text-red-500">{{ errorMessage }}</p>
  
        <p class="mt-10 text-center text-sm/6 text-gray-500">
          Not a member?
          {{ ' ' }}
          <router-link to="/auth/signup" class="font-semibold text-indigo-600 hover:text-indigo-500">Signup here!</router-link>
        </p>
      </div>
    </div>
  </template>
  