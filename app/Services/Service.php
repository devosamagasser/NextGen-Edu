<?php

namespace App\Services;

Abstract class Service
{
    public function updatedDataFormated($request,$data = null)
    {
        $data = $data ?? $request->all();
        $updatedData = [] ;
        foreach ($data as $key => $datum) {
            if($request->filled($key))
                $updatedData[$key] = $datum;
        }
        return $updatedData;
    }

}
