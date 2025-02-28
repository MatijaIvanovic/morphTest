<?php

namespace App\Http\Controllers;

use App\Models\FavoriteList;
use DeepCopy\Filter\Filter;
use Error;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Tymon\JWTAuth\Facades\JWTAuth;

use function PHPUnit\Framework\isArray;

class FavoriteListController extends Controller
{
    public function formatDuration($isoDuration){

        preg_match('/PT(?:(\d+)H)?(?:(\d+)M)?(?:(\d+)S)?/', $isoDuration, $matches);

        $hours = isset($matches[1]) ? (int)$matches[1] : 0;
        $minutes = isset($matches[2]) ? (int)$matches[2] : 0;
        $seconds = isset($matches[3]) ? (int)$matches[3] : 0;


        if($hours>0){
            return sprintf('%d:%02d:%02d', $hours, $minutes, $seconds);
        }else{
            return sprintf('%02d:%02d', $minutes, $seconds);
        }
    } 
    public function addToFavorites(Request $request){


        try{
            $user =JWTAuth::parseToken()->authenticate();
        }catch(\Exception $e){
            return response()->json(['message'=>'Not a valid token!']);
        }


        $videos_id = $request->input('videoId');
        
        try{
            FavoriteList::create(['user_id'=>$user->id,'video_id'=>$videos_id,'added_at'=>now()]);
        
            return response()->json(['message'=>'successfully added!'], 200);
        }catch (QueryException $e) {
            return response()->json(['message' => 'This connection already exists!'], 422);
        }
    
    }
    

    public function listFavorites(){
        
        $apiUrl = env('API_URL').'videos';
        $apiKey= env('API_KEY');
        try{
            $user= JWTAuth::parseToken()->authenticate();
        }catch(\Exception $e){
            return response()->json(['message'=>'Not a valid token!']);
        }
        
        $videos=FavoriteList::where(['user_id'=>$user->id])->pluck('video_id')->toArray();
        
        
        

        $favoriteVideos=[];

        $response = Http::get($apiUrl,[
            'part'=>implode(',',['snippet','contentDetails']),
            'id'=>implode(',',$videos),
            'key'=>$apiKey
        ]);
        $items= $response->json()['items'];
        
        foreach($items as $item){

            $addedAt = FavoriteList::where(['user_id'=>$user->id, 'video_id'=>$item['id']])->value('added_at');
            $favoriteVideos[]=[
                'id'=>$item['id'],
                'title' => $item['snippet']['title'],
                'thumbnaillUrl' => $item['snippet']['thumbnails']['high']['url'],
                'channel' => $item['snippet']['channelTitle'],
                'duration'=>$this->formatDuration($item['contentDetails']['duration']),
                'addedAt'=> $addedAt,
            ];
        }
        

        return response()->json($favoriteVideos);


    }

    public function delete(Request $request){

        try{
            $user =JWTAuth::parseToken()->authenticate();
        }catch(\Exception $e){
            return response()->json(['message'=>'Not a valid token!']);
        }

        $id = $request->input('videoId');

        $response = FavoriteList::where(['user_id'=>$user->id,'video_id'=>$id])->delete();

        return $response;
    }
}
