<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Exception;

class Post extends Model
{
    protected $fillable = ['title'];

    public function content() {
        switch ($this->type) {
            case 'content':
                return $this->hasOne(PostContent::class);
            default:
                throwException(new Exception('Unknown post type'));
        }
    }
}
