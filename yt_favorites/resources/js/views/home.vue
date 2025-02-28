<script setup>

import itemContainer from './itemContainer.vue'
import {ref} from 'vue';

 const searchQuery = ref('');


</script>

<template>
         <div class="flex-1 flex justify-center bg-gray-50 mb-8">
              <div class="w-full max-w-lg">
                <input 
                  v-model="searchQuery"
                  type="search" 
                  name="search" 
                  aria-label="Search" 
                  class="block w-full rounded-md bg-gray-100 border-b py-1.5 pl-10 pr-3 text-base text-gray-900 outline-none placeholder:text-gray-400 focus:outline focus:outline-2 focus:outline-offset-2 focus:outline-white/40 sm:text-sm/6" 
                  placeholder="Search"
                />
              </div>
            </div>

          <itemContainer :searchQuery="searchQuery"/>
        


</template>