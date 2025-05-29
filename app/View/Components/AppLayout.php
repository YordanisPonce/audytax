<?php

namespace App\View\Components;

use App\Models\Status;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\Component;

class AppLayout extends Component
{
    public $links = [];
    /**
     * Get the view / contents that represents the component.
     *
     * @return \Illuminate\View\View
     */
    public function render()
    {
        if (Auth::check() && !Auth::user()->hasRole('admin')) {
            $this->links = Auth::user()->qualityControls()->simplePaginate(10);
        }

        return view('layouts.app', ['data' => $this->links, 'links' => $this->links]);
    }
}
