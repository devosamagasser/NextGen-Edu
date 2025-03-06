<?php

namespace App\Services;

use App\Facades\FileHandler;

Trait DynamicUpdateService
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

    public function updateWith($fileAttrName, $oldFile, $pathStoring, $request, $fileExt = 'jpg')
    {
        try{
            $data = $this->updatedDataFormated($request, $request->except($fileAttrName));
            $data[$fileAttrName] = FileHandler::updateFile($request->$fileAttrName, $oldFile, $pathStoring, $fileExt);
            return $data;
        } catch (\Exception $e) {
            throw new \Exception($e->getMessage());
        }
    }

    public function updateWithFile($fileAttrName, $request, $model, $pathStoring, $fileExt = 'jpg')
    {
        if ($request->has($fileAttrName)) {
            return $this->updateWith($fileAttrName, $model->$fileAttrName, $pathStoring, $request, $fileExt);
        }
        return $this->updatedDataFormated($request);
    }
}
