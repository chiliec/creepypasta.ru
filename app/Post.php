<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Symfony\Component\HttpFoundation\Response;

class Post extends Model
{
    use SoftDeletes;

    public static $validTypes = ['content', 'video'];

    protected $fillable = ['title', 'description', 'type', 'tags', 'user_ip', 'hash'];

    protected static function boot()
    {
        parent::boot();

        self::creating(function($model) {
            $model->user_id = auth()->id();
            $model->slug = md5($model->title);
        });

        self::updating(function($model) {
            $model->slug = md5($model->title);
        });
    }

    public function content() {
        switch ($this->type) {
            case 'content':
                return $this->hasOne(PostContent::class);
            default:
                abort(Response::HTTP_BAD_REQUEST, 'Unknown post type');
        }
    }
}
