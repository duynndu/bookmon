<?php

namespace App\Traits;

use Illuminate\Support\Facades\Storage;

trait RemoveImageTrait
{
    public function removeImage($request, $table, $colum, $folder)
    {
        $result = $table->findOrFail($request->id);

        if ($result) {
            if ($result->$colum) {
                $path = 'public/' . $folder . '/' . basename($result->$colum);
                if (Storage::exists($path)) {
                    Storage::delete($path);
                }
            }

            $result->update([
                $colum => null
            ]);
        }

        return true;
    }
}
