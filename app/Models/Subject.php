<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Subject extends Model {
    use HasFactory;
    protected $fillable = ['name','faculty_id','semester_id'];
    public function notes(){ return $this->hasMany(Note::class); }
}
