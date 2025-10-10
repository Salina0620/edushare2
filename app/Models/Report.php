<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Report extends Model {
    use HasFactory;
    protected $fillable = ['user_id','note_id','reason','status'];
    public function note(){ return $this->belongsTo(Note::class); }
    public function user(){ return $this->belongsTo(User::class); }
}
