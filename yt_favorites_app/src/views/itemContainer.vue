
<script setup>
import { ref, watch, onMounted } from "vue";
import axios from "axios";
/* const items = ref([]); */
import { useAuthStore } from '../stores/authStore'


const authStore = useAuthStore();
const alertMessage = ref("");
const showAlert = ref(false);
const alertType = ref("success");
const items=ref([]);

const props =defineProps({
  searchQuery: String
});

const listItems= async ()=>{
  
  try{
    const response = await axios.get('http://yt_favorites.test/videos');
    items.value= response.data;
  }catch(Error ){
    console.error("Error message: ", Error);

  }
}; 
onMounted(()=>{
  authStore.checkLoginStatus();
 
  listItems();
})

const search= async(searchQuery)=>{

  try{
    const response = await axios.get('http://yt_favorites.test/search', {params:{search:searchQuery}});
    items.value= response.data;

  }catch(Error){
    console.error('Error message: ', Error);
  }
}

watch(()=> props.searchQuery,(newQuery)=>{
  if(newQuery.trim()){
    search(newQuery);
  }
  else{
    listItems();
  }
})

const addFavorites = async(videoId)=>{

    try{
      const token = localStorage.getItem('token');


      const response = await axios.post('http://yt_favorites.test/favorites', {
        videoId:videoId
      },{
        headers:{
          Authorization:`Bearer ${token}`
        }
      });
      
      if (response.data.message === "This connection already exists!") {
      alertType.value = "error";
      } else {
      alertType.value = "success"; 
    }
    alertMessage.value = response.data.message;
    showAlert.value = true;

      setTimeout(() => {
      showAlert.value = false;
    }, 3000);
    }catch(error){
      console.error('Error adding to favorites: ', error);
    }
}

</script>


<template>
  <div 
  v-if="showAlert" 
  class="fixed top-4 right-4 text-white px-4 py-2 rounded-md shadow-md transition-opacity duration-300 z-50"
  :class="alertType === 'success' ? 'bg-green-500' : 'bg-red-500'"
>
  {{ alertMessage }}
</div>
  <div class="grid grid-cols-3 gap-4">
    <div v-for="item in items" :key="item.id" class="relative p-4 border rounded-lg">
      
      <img :src="item.thumbnailUrl" alt="Video Thumbnail" class="w-full h-auto rounded">
      <h2 class="text-lg font-bold">{{ item.title }}</h2>
      <p class="text-sm text-black">By: {{ item.channelTitle }}</p>
      <p class="text-sm text-gray-600">Duration: {{ item.duration }}</p>
      <!-- Heart icon positioned at the bottom right -->
      <button @click="addFavorites(item.id)" class="absolute bottom-2 right-2 text-b text-2xl bg-gray-200 p-2 rounded-full hover:bg-blue-500 hover:text-white transition-all">
        +
      </button>
    </div>
  </div>
</template>

