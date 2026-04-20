<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Orchid\Filters\Filterable;
use Orchid\Screen\AsSource;

class Icon extends Model
{
    use AsSource, Filterable, HasFactory;
    protected $fillable = ['name', 'file'];

    public function getSvgAttribute(): string
    {
        $path = storage_path('app/public/icons/' . $this->file);

        return file_exists($path)
            ? file_get_contents($path)
            : '';
    }
}
