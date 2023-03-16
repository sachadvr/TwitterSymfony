<?php

namespace App\Custom;
use Symfony\Component\HttpFoundation\Request;

class LastRoute
{
    
    public static function getLastRoute($request, $default = '/')
    {
        try{
            if($request->headers->get('referer') == null) throw new \Exception();
            return $request->headers->get('referer');
        }catch(\Exception $e){
            return $default;
        }
    }
    
}