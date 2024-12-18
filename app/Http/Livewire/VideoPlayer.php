<?php

namespace App\Http\Livewire;

use Livewire\Component;

class VideoPlayer extends Component
{

    public $video;
    public function render()
    {
        return view('livewire.video-player');
    }
}
