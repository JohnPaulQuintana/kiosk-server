<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class FloorplanUnit extends Model
{
    use HasFactory;
    protected $fillable = ['floorplan_id','unit','door','availability','old_unit','image'];

    public function floorplan() :BelongsTo{
        return $this->belongsTo(Floorplan::class);
    }

    public function teachers() :HasMany{
        return $this->hasMany(Teacher::class);
    }
}
