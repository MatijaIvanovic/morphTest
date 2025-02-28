
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
const isSelect=ref(false);
const selectedVideos = ref([]);
const props =defineProps({
  searchQuery: String
});

const listItems= async ()=>{
  console.log('starting the communcation with the API');
  try{
    const response = await axios.get('/api/videos');
    console.log('API response:', response.data);
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
    const response = await axios.get('/api/search', {params:{search:searchQuery}});
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


      const response = await axios.post('/api/favorites', {
        videoId:videoId
      },{
        headers:{
          Authorization:`Bearer ${token}`
        }
      });
      
      alertType.value = "success";
      alertMessage.value = response.data.message;
    } catch (error) {
      if (error.response && error.response.status === 422) {
        alertType.value = "error";
        alertMessage.value = error.response.data.message;
      }else {
        alertType.value = "error";
        alertMessage.value = "An error occurred while adding to favorites.";
      }
    }

  showAlert.value = true;
  setTimeout(() => {
    showAlert.value = false;
  }, 3000);
}
const select =() =>{
  isSelect.value=!isSelect.value;

}

const handleAddToFavorites = () => {
  
  if(selectedVideos.value.length>0){
    selectedVideos.value.forEach(videoId=>{
      addFavorites(videoId);
    });
    selectedVideos.value = [];
    isSelect.value=false;

  }else{
    alertType.value = "error";
    alertMessage.value= 'No videos selected!';
    showAlert.value = true;
    setTimeout(()=>{
      showAlert.value=false;
    },3000);
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
    <div class="col-span-3 flex justify-end">
      <button v-if="!isSelect" @click="select()"class="bg-blue-500 text-white px-4 py-2 rounded-md shadow-md hover:bg-blue-600 focus:outline-none focus:ring-2 focus:ring-blue-300">
        Select
      </button>
     <div v-if="isSelect">
        <button v-if="selectedVideos.length>0" @click="handleAddToFavorites()" class="bg-blue-500 text-white px-4 py-2 rounded-md shadow-md hover:bg-blue-600 focus:outline-none focus:ring-2 focus:ring-blue-300">
          Add to favorites
        </button>
        <button v-if=" selectedVideos.length===0" @click="select()" class="bg-blue-500 text-white px-4 py-2 rounded-md shadow-md hover:bg-blue-600 focus:outline-none focus:ring-2 focus:ring-blue-300">
          Cancel
        </button></div> 
    </div>
    
    <div v-for="item in items" :key="item.id" class="relative p-4 border rounded-lg">
      <img :src="item.thumbnailUrl" alt="Video Thumbnail" class="w-full h-auto rounded">
      <h2 class="text-lg font-bold">{{ item.title }}</h2>
      <p class="text-sm text-black">By: {{ item.channelTitle }}</p>
      <p class="text-sm text-gray-600">Duration: {{ item.duration }}</p>

    
      <button v-if="!isSelect" @click="addFavorites(item.id)" class="absolute bottom-2 right-2 text-b text-2l bg-gray-200 p-2 rounded-full hover:bg-blue-500 hover:text-white transition-all">
        Add to favorites
      </button>
      <input
        v-if="isSelect"
        type="checkbox"
        :value="item.id"
        v-model="selectedVideos"
        class="absolute bottom-2 right-2 w-6 h-6 rounded-md border-2 border-gray-400 bg-white focus:ring-2 focus:ring-blue-500"
      />

        
    </div>
  </div>
</template>

