<?php

namespace App\Traits;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

trait HasFile
{
    public function upload_file($file, string $folder): ?string
    {
        // Cek apakah ada file yang dikirim, jika ada simpan ke disk public
        return $file ? $file->store($folder, 'public') : null;
    }

    public function update_file(Request $request, Model $model, string $column, string $folder): ?string
    {
        if($request->hasFile($column)){
            $this->delete_file($model, $column);
            return $request->file($column)->store($folder, 'public');
        }

        return $model->$column;
    }

    public function delete_file(Model $model, string $column): void
    {
        if($model->$column) {
            // Beritahu Storage untuk menghapus dari disk 'public'
            Storage::disk('public')->delete($model->$column);
        }
    }
}