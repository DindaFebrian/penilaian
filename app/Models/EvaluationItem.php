<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class EvaluationItem extends Model
{
    protected $fillable = ['evaluation_id','aspect','indicator','score','evidence_path','notes'];

    public function evaluation() { return $this->belongsTo(Evaluation::class); }
}
