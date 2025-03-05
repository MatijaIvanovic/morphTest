<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class YtController extends Controller
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

    public function getVideos(){

        $apiUrl=env('API_URL').'videos';
        $apiKey= env('API_KEY');

        $response = Http::get($apiUrl,[
            'chart'=>'mostPopular',
            'part'=>implode(',',['snippet','contentDetails']),
            'key'=>$apiKey,
            'maxResults' => 50
        ]);
        $items= $response->json()['items'];
        $videos=[];
        
        foreach($items as $item){
            $videos[]=[
                'video_id'=> $item['id'],
                'title' => $item['snippet']['title'],
                'channel_name' =>$item['snippet']['channelTitle'],
                'thumbnail_url'=>$item['snippet']['thumbnails']['medium']['url'],
                'duration'=>$this->formatDuration($item['contentDetails']['duration']),
            ];
        }


        return response()->json($videos);
    }

    public function searchVideos(Request $request){
        $search = $request->input('search');
        $apiUrl=env('API_URL').'search';
        $apiKey= env('API_KEY');
        
        $response = Http::get($apiUrl,[
            'part'=>'snippet',
            'q' => $search,
            'key'=>$apiKey,
            'maxResults'=>15,
            'type'=>'video'
        ]);  
        
    
        $items = $response->json()['items'];
        $videos=[];

        foreach($items as $item){

            $videos[]=[
                'id'=> $item['id']['videoId'],
                'title' => $item['snippet']['title'],
                'channelTitle' =>$item['snippet']['channelTitle'],
                'thumbnailUrl'=>$item['snippet']['thumbnails']['high']['url'],
                'duration'=>'N/A',
            ];
        }
        return response()->json($videos);
    }

    

    
}

