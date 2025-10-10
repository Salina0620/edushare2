<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Faculty extends Model {
    use HasFactory;
    protected $fillable = ['name'];
    public function notes(){ return $this->hasMany(Note::class); }
}
