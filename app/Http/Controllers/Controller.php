<?php

namespace App\Http\Controllers;

use App\Providers\RouteServiceProvider;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;

class Controller extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;

    private function lastUrlKey(): string
    {
        return (new \ReflectionClass($this))->getShortName() . '_url';
    }

    protected function setLastUrl(string $url = null)
    {
        session([$this->lastUrlKey() => $url?:url()->full()]);
    }

    protected function getLastUrl(string $default = null)
    {
        return session($this->lastUrlKey(), $default?:RouteServiceProvider::HOME);
    }
}
