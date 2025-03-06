<?php

namespace App\Jobs;

use App\Models\FavoriteList;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class MarkWatched implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $user_id;
    protected $video_id;
    /**
     * Create a new job instance.
     */
    public function __construct(int $user_id, string $video_id)
    {
        $this->user_id=$user_id;
        $this->video_id=$video_id;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {

        $user = User::find($this->user_id);

        if($user){
            $video = FavoriteList::where(['user_id'=>$this->user_id, 'video_id'=>$this->video_id])->first();
            if($video){
                $video->update(['watched_at'=>now()]);
            }
        }
    }
}
