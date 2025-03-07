
<script setup>
import { ref, watch, onMounted } from "vue";
import axios from "axios";
/* const items = ref([]); */
import { useAuthStore } from '../stores/authStore'
import lodash, { debounce } from 'lodash'

const authStore = useAuthStore();
const alertMessage = ref("");
const showAlert = ref(false);
const alertType = ref("success");
const items=ref([]);
const isSelect=ref(false);
const selectedVideos = ref([]);
const player = ref('');

const isModalOpen =ref(false);
const currentVideoId= ref('');
 
const props =defineProps({
  searchQuery: String
});

const listItems= async ()=>{
  try{
    const response = await axios.get('/api/videos');
    items.value= response.data;
  }catch(Error ){
    console.error("Error message: ", Error);

  }
}; 
onMounted(()=>{
  authStore.checkLoginStatus();
 
  listItems();
})


const playVideo = async(video_id)=>{
  currentVideoId.value= video_id;
  isModalOpen.value= true;

  try{
    const token = localStorage.getItem('token')
    const response = await axios.post('/api/watched',
      {video_id:video_id},
    {
      headers:{
        Authorization:`Bearer ${token}`
      }
    });
    console.log(response.data.message);

  }catch(error){
    console.log('ERROR!!!!!!', error);
  }
}

const closeModal = ()=>{
  isModalOpen.value=false;
  currentVideoId.value='';
}

const search= async(searchQuery)=>{

  try{
    const response = await axios.get('/api/search', {params:{search:searchQuery}});
    items.value= response.data;

  }catch(Error){
    console.error('Error message: ', Error);
  }
}

const debounceSrch = debounce((newQuery)=>{
  if(newQuery.trim()){
    search(newQuery);
  }
},500)

watch(()=> props.searchQuery,(newQuery)=>{
  if(newQuery.trim()){
      debounceSrch(newQuery);
  }
  else{listItems();}

})




const addFavorites = async(videoId, videoTitle, videoDuration, channelTitle, thumbnailUrl,)=>{

    try{
      const token = localStorage.getItem('token');


      const response = await axios.post('/api/favorites', {
        video_id:videoId,
        title:videoTitle,
        duration: videoDuration,
        thumbnail_url: thumbnailUrl,
        channel_name:channelTitle,
      },{
        headers:{
          Authorization:`Bearer ${token}`
        }
      });
      
      return response;
    } catch (error) {
      return error;
      }

}
const select =() =>{
  isSelect.value=!isSelect.value;

}
const checkIfAlreadyFavorite= async(video_id)=>{
  const token = localStorage.getItem('token');

  try{
    const response = await axios.get(`/api/favorites?video_id=${video_id}`,{
      headers:{
        Authorization:`Bearer ${token}`
      }
    });
    return response.data.exists;
  }catch(error){
    console.error('Error Checking favorite status:', error);
    return false;
  }

}

const handleAddToFavorites = async(video_id = null) => {
  if(video_id){
    const video= items.value.find((item)=>item.video_id === video_id);
    if(!video){
      console.error('Video not found for the ID: ',video_id);
      return;
    }
    const isAlreadyFavorite = await checkIfAlreadyFavorite(video.video_id); 
    if(isAlreadyFavorite){
      alertMessage.value= "This video is already in favorites!";
      alertType.value = 'error';
      
    }
    else{const response = await addFavorites(
      video.video_id,
      video.title,
      video.duration,
      video.channel_name,
      video.thumbnail_url
    );

    if (response && response.status === 200) {
      alertMessage.value = "Video added to favorites!";
      alertType.value = 'success';
    } else {
      alertMessage.value = "Failed to add video to favorites.";
      alertType.value = 'error';
    }}
  }

  else if(selectedVideos.value.length>0){
    try{
      const token = localStorage.getItem('token');
      let successCount = 0;
      let failureCount = 0;

      for(const video_id of selectedVideos.value){
        try{
          const video = items.value.find(item=> item.video_id=== video_id);
          const isAlreadyFavorite = await checkIfAlreadyFavorite(video.video_id)
          if(isAlreadyFavorite){
            failureCount++;
            continue;
          }
          const response = await addFavorites(
            video.video_id, video.title, video.duration, video.channel_name,video.thumbnail_url
          );

          if(response && response.status ===200){
            successCount++;
          }else{
            failureCount++;
          }
          }catch(error){
          failureCount++;
        }
      }
      alertMessage.value = `${successCount} videos added Successfully! ${failureCount} failed!`
      alertType.value= successCount>0?"success":"error";
    }catch(error){
      console.error('Error adding to favorites', error);
      alertType.value= "error";
      alertMessage.value = 'An error occurred while adding to favorites!';
    }

   

  }

  showAlert.value=true;
  setTimeout(()=>{
    showAlert.value=false
    },3000);

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
      <button 
        v-if="!isSelect" 
        @click="select()" 
        class="bg-blue-500 text-white px-4 py-2 rounded-md shadow-md hover:bg-blue-600 focus:outline-none focus:ring-2 focus:ring-blue-300"
      >
        Select
      </button>

      <div v-if="isSelect">
        <button 
          v-if="selectedVideos.length > 0" 
          @click="handleAddToFavorites()" 
          class="bg-blue-500 text-white px-4 py-2 rounded-md shadow-md hover:bg-blue-600 focus:outline-none focus:ring-2 focus:ring-blue-300"
        >
          Add to favorites
        </button>

        <button 
          v-if="selectedVideos.length === 0" 
          @click="select()" 
          class="bg-blue-500 text-white px-4 py-2 rounded-md shadow-md hover:bg-blue-600 focus:outline-none focus:ring-2 focus:ring-blue-300"
        >
          Cancel
        </button>
      </div> 
    </div>

    <div v-for="item in items" :key="item.video_id" class="relative p-4 border rounded-lg">
      <img 
        @click="playVideo(item.video_id)" 
        :src="item.thumbnail_url" 
        alt="Video Thumbnail" 
        class="w-full h-auto rounded"
      >
      <!-- <img 
        :src="item.thumbnail_url" 
        alt="Video Thumbnail" 
        class="w-full h-auto rounded"
      > -->
      <h2 class="text-lg font-bold text-black">{{ item.title }}</h2>
      <p class="text-sm text-black">By: {{ item.channel_name }}</p>
      <p class="text-sm text-gray-600">Duration: {{ item.duration }}</p>

      <button 
        v-if="!isSelect" 
        @click="handleAddToFavorites(item.video_id)" 
        class="absolute bottom-2 right-2 text-b text-black text-2l bg-gray-200 p-2 rounded-full hover:bg-blue-500 hover:text-white transition-all"
      >
        Add to favorites
      </button>

      <input
        v-if="isSelect"
        type="checkbox"
        :value="item.video_id"
        v-model="selectedVideos"
        class="absolute bottom-2 right-2 w-6 h-6 rounded-md border-2 border-gray-400 bg-white focus:ring-2 focus:ring-blue-500"
      />

      <div v-if="isModalOpen" class="fixed inset-0 bg-black bg-opacity-50 flex justify-center items-center z-50">
        <div class="bg-white p-4 rounded-lg relative">
          <button @click="closeModal" class="absolute top-2 right-2 text-white bg-red-500 rounded-full px-2 py-1">X</button>
          <iframe id="player" :src="'https://www.youtube.com/embed/' + currentVideoId" sandbox="allow-scripts allow-same-origin" frameborder="0" allowfullscreen></iframe>

          
        </div>
      </div>
     <!-- <iframe :src="'https://www.youtube.com/embed/' + item.video_id" v-if="player===item.video_id"></iframe> -->

    </div>
  </div>
</template>