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

    public function markVideoAsCompleted(): void
    {
        auth()->user()->videos()->attach($this->video);
    }

    public function markVideoAsNotCompleted(): void
    {
        auth()->user()->videos()->detach($this->video);
    }

    public function render()
    {
        return view('livewire.video-player');
    }
}
