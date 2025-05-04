<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Teacher extends Model
{
    use HasFactory;
    protected $fillable = ['floorplan_unit_id','name','email','floor','file_path'];

    public function unit() {
        return $this->belongsTo(FloorplanUnit::class);
    }
}
