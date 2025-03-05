import {defineStore} from 'pinia';
import axios from 'axios';

export const useAuthStore = defineStore('auth', {
 state: () => ({
    isLoggedIn:false,
    token:null as string |null,
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

    async checkLoginStatus(){
        const token = localStorage.getItem('token');

        if(!token){
            this.logout();
            return;
        }

        try{
            await axios.get('/api/check-token',{
                headers: {Authorization: `Bearer ${token}`},
            });
            this.isLoggedIn =true;
            this.token = token;
        }
        catch(error){
            console.error('Invalid token, logging out... ', error);
            this.logout();
        }

    }
 }
    
});
