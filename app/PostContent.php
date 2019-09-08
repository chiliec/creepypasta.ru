<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class PostContent extends Model
{
    protected $fillable = ['text', 'source'];

    protected $table = 'posts_content';

    public function post() {
        return $this->belongsTo(Post::class);
    }
}
