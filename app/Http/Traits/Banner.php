<?php

namespace App\Http\Traits;

trait Banner {
    public function flash($message, $type) {
        session()->flash('flash.banner', $message);
        session()->flash('flash.bannerStyle', $type);
    }
}
