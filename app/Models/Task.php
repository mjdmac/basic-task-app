<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;


class Task extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $fillable = [
        'parent',
        'name',
        'description',
        'status',
        'order',
        'custom_status'
    ];
    protected $dates = ['deleted_at'];
    protected $appends = ['deleted_at_date'];

    public function getDeletedAtDateAttribute()
    {
        return date('d F Y', strtotime($this->deleted_at));
    }


    public function parent() {
        return $this->hasOne(Task::class, 'id', 'parent');
    }

    public function subtasks() {
        return $this->hasMany(Task::class, 'parent', 'id')->orderBy('order');
    }

    // public static function tree() {
    //     return static::with(implode('.', array_fill(0, 4, 'children')))->where('parent', null);
    // }

    public function owner()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public static function boot()
    {
        parent::boot();
        static::creating(function($model)
        {
            $user = auth()->user();
            $model->created_by = $user ? $user->id : 1;
            $model->updated_by = $user ? $user->id : 1;
        });
        static::updating(function($model)
        {
            $user = auth()->user();
            $model->updated_by = $user ? $user->id : 1;
        });
    }
}
