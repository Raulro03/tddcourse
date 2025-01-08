<?php

namespace App\Http\Livewire;

use Livewire\Component;

class VideoPlayer extends Component
{
    public $video;
    public $courseVideos;

    public function mount()
    {
        $this->courseVideos = $this->video->course->videos;
    }

    public function render()
    {
        return view('livewire.video-player');
    }
}
