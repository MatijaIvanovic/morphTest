import {defineStore} from 'pinia';


export const useAuthStore = defineStore('auth', {
 state: () => ({
    isLoggedIn:false,
    token:null as string |null,
    checkLoginStatus(){
      const token = localStorage.getItem('token');
      if(token){
          this.isLoggedIn =true;
          this.token = token;
      }else{
          this.isLoggedIn=false;
          this.token = null;
      }

  }
 }),
 actions: {
    login(token: string){
        this.isLoggedIn = true;
        this.token = token;
        localStorage.setItem('token', token);

    },

    logout(){
        this.isLoggedIn = false;
        this.token = null;
        localStorage.removeItem('token');
    },

    checkLoginStatus(){
        const token = localStorage.getItem('token');
        if(token){
            this.isLoggedIn =true;
            this.token = token;
        }else{
            this.isLoggedIn=false;
            this.token = null;
        }

    }
 }
    
});
