<?php
namespace App\Traits;

use Illuminate\Support\Facades\Log;

trait LoggerTrait
{
    public function logInfo($msg){
      Log::info($msg);
    }

     public function logError($msg)
    {
        Log::error($msg);
    }
}