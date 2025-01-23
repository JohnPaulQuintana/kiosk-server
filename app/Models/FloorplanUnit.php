<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class FloorplanUnit extends Model
{
    use HasFactory;
    protected $fillable = ['floorplan_id','unit','door','availability','old_unit','image'];

    public function floorplan() :BelongsTo{
        return $this->belongsTo(Floorplan::class);
    }
}
