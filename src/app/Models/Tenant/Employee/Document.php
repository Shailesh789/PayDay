<?php

namespace App\Models\Tenant\Employee;

use App\Models\Core\Auth\User;
use App\Models\Core\BaseModel;
use App\Models\Core\Traits\CreatedByRelationship;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Models\Core\File\Traits\FileAttribute;

class Document extends BaseModel
{
    use HasFactory, CreatedByRelationship, FileAttribute;

    protected $fillable = ['created_by', 'name', 'path', 'user_id'];

    protected $appends = ['full_url'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
