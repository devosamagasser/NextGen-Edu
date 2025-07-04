<?php

namespace App\Facades;

use App\Facades\FacadesLogic\FileHandlerLogic;
use Illuminate\Support\Facades\Facade;

/**
 * @method static FileHandlerLogic deleteFile(string $name)
 * @method static FileHandlerLogic storeFile($file, $path, $extension, $name = null)
 * @method static FileHandlerLogic updateFile($file, string $oldName, $path, $extension, $name = null)
 */
class FileHandler extends Facade
{
    protected static function getFacadeAccessor()
    {
        return FileHandler::class;
    }
}
