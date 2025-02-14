<?php

namespace App\Traits;

use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

trait Upload
{


    public function upload($file, $folderName)
    {
        if (!$file) return null;
        // Generar un nombre único para el archivo
        $fileName = Str::random(25) . uniqid(true) . '.'  . $file->getClientOriginalExtension();

        // Guardar el archivo en la ruta especificada con el nuevo nombre
        Storage::disk('public')->putFileAs($folderName, $file, $fileName);

        return $folderName . '/' . $fileName;
    }

    public function removeFile($path)
    {
        Storage::delete('public/' . $path);
    }

    public function updateFile($file, $folderName, $oldFile)
    {
        if (!$file) return $oldFile;
        $this->removeFile($oldFile);
        return $this->upload($file, $folderName);
    }
}
