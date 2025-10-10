<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Download extends Model {
    use HasFactory;
    protected $fillable = ['user_id','note_id','ip'];
    public function note(){ return $this->belongsTo(Note::class); }
}
