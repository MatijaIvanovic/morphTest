<script setup>
import { useAuthStore } from '../stores/authStore';
import { onMounted, ref } from 'vue';
import axios from 'axios';
import { useRouter } from 'vue-router';

const items = ref([]);
const authStore = useAuthStore();
const alertType= ref('success');
const alertMessage = ref("");
const showAlert =ref(false);
const router = useRouter();
const isSortedByAddedAt = ref(true);
const isSortedByDuration = ref(false);

onMounted(() => {
  authStore.checkLoginStatus();
  

  listItems();
});

  const orderByAddedTime= async()=>{
    console.log("starting value ", isSortedByAddedAt.value);
    try{
      const token = localStorage.getItem('token');
      const response = await axios.get(`/api/orderByCreatedAt?orderBy=${String(isSortedByAddedAt.value)}`,{
        headers:{
          Authorization:`Bearer ${token}`
        }});
      items.value=response.data;
      isSortedByAddedAt.value=!isSortedByAddedAt.value;
      console.log(isSortedByAddedAt.value);
    }catch(error){
      console.log(error);
    }
  }
  
 

  const orderByDuration = async()=>{
    try{
      const token = localStorage.getItem('token');
      const response = await axios.get(`/api/orderByDuration?orderBy=${String(isSortedByDuration.value)}`,{
        headers:{
          Authorization:`Bearer ${token}`
        }
      });
      items.value = response.data;
      isSortedByDuration.value =!isSortedByDuration.value;
      
    }catch(error){
      console.log(error);
    }
  }

  const listItems = async () => {
    try {

      const token = localStorage.getItem('token');
      const response = await axios.get('/api/listfavorites',
        {headers:{
          Authorization:`Bearer ${token}`
        }});
      items.value = response.data;
    } catch (error) {
      console.error("Error message: ", error);
    }
  };

const deleteFavorites = async(videoId)=>{

  try{

    const token = localStorage.getItem('token');
    
    const response = await axios.delete(`/api/delete?video_id=${videoId}`, {
        headers:{
          Authorization:`Bearer ${token}`
        }
      });
      
    if(response.data===1){
      alertType.value='success';
      alertMessage.value = 'Successfully deleted!';
      
      listItems();
    }else{
      alertType.value='error';
      alertMessage.value = "Couldn't delete1";
      console.log(videoId);
    }
    
    setTimeout(()=>{
      showAlert.value=false;
    },3000);
  
  }catch(Error){
    console.error("!!error!! ", Error)
  }
}


</script>

<template>
  <div>
    <div 
  v-if="showAlert" 
  class="fixed top-4 right-4 text-white px-4 py-2 rounded-md shadow-md transition-opacity duration-300 z-50"
  :class="alertType === 'success' ? 'bg-green-500' : 'bg-red-500'"
>
  {{ alertMessage }}
</div>
<ul role="list" class="divide-y divide-gray-100" v-if="items.length > 0">
  <div class="flex space-x-2 mb-6 ml-auto justify-end">
  <button @click="orderByDuration()" class="bg-blue-400 text-white px-4 py-2 rounded-md hover:bg-blue-600">
    Order by duration
  </button>
  <button @click="orderByAddedTime()" class="bg-blue-400 text-white px-4 py-2 rounded-md hover:bg-blue-600">
    Order by time added
  </button>
</div>



  <li v-for="item in items" :key="item.video_id" class="flex items-center justify-between gap-x-4 py-5">
    <div class="flex items-center gap-x-4">
      <img class="w-16 h-16 object-cover rounded-md" :src="item.thumbnail_url" alt="Video Thumbnail" />
      
      <div class="flex flex-col min-w-0 flex-grow gap-y-1">
        <p class="text-lg font-semibold text-gray-900">{{ item.title }}</p>
        <p class="text-sm text-gray-600">{{ item.channel_name }}</p>
      </div>
    </div>
    <div class="flex items-center justify-end gap-x-6">
      <p class="text-xs text-gray-500">{{ item.duration }}</p>
      <p class="text-xs text-gray-400">{{ item.created_at }}</p>

      <button @click="deleteFavorites(item.video_id)" class="rounded-full bg-red-100 px-3 py-1 text-xs font-semibold text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 hover:bg-red-700 hover:text-white">
        Delete
      </button>
    </div>
  </li>
</ul>

    
    <div v-else>No favorites found.</div>
  </div>
</template>
