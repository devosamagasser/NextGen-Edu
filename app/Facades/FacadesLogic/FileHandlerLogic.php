<?php

namespace App\Facades\FacadesLogic;

use Illuminate\Support\Facades\Storage;

class FileHandlerLogic
{
    /**
     * @param $file
     * @return string
     */
    public function storeFile($file, $path, $extension, $name = null)
    {
        try{
            $newName = ($name ?? time()).".$extension";
            $path = Storage::disk('public')->putFileAs($path, $file, $newName);
            return $path;
        } catch (\Exception $e) {
            return throw new \Exception($e->getMessage());
        }
    }

    /**
     * @param $file
     * @param string $oldname
     * @return string
     */
    public function updateFile($file,string $oldname,$path, $extension, $name = null)
    {
        try{
            $this->deleteFile($oldname);
            return $this->storeFile($file,$path,$extension, $name);
        } catch (\Exception $e) {
            return throw new \Exception($e->getMessage());
        }
    }

    /**
     * @param string $name
     * @return bool
     */
    public function deleteFile(string $name)
    {
        try {
            return Storage::disk('public')->delete($name);
        } catch (\Exception $e) {
            return throw new \Exception($e->getMessage());
        }
    }


}
