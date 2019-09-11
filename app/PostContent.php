<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

interface Content {
    // link to Post related model
    function post();
    // default view
    function show();
}

class PostContent extends Model implements Content
{
    protected $fillable = ['post_id', 'text', 'source'];

    protected $table = 'posts_content';

    public $timestamps = false;

    public function post() {
        return $this->belongsTo(Post::class);
    }

    public function show() {
        return $this->text;
    }
}
