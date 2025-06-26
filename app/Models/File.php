<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class File extends Model
{
    use HasFactory;
    
    protected $fillable = ['name', 'url', 'description', 'document_id', 'user_id', 'is_approved', 'original_name'];
    
    public function document()
    {
        return $this->belongsTo(Document::class);
    }
    
    public function user()
    {
        return $this->belongsTo(User::class);
    }
    
    public function isApproved()
    {
        return $this->is_approved;
    }
    
    public function isPendingApproval()
    {
        return !$this->is_approved;
    }
}