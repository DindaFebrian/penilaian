<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Evaluation extends Model
{
    protected $fillable = ['school_id','pengawas_id','tanggal','status','overall_notes'];

    public function school() { return $this->belongsTo(School::class); }
    public function pengawas() { return $this->belongsTo(User::class, 'pengawas_id'); }
    public function items() { return $this->hasMany(EvaluationItem::class); }

    // helper untuk baca nilai item
    public function getScore(string $aspect, string $indicator): ?string {
        return optional($this->items->firstWhere(fn($i)=>$i->aspect===$aspect && $i->indicator===$indicator))->score;
    }
}
