<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;
use Symfony\Component\HttpFoundation\Response;
use Cog\Contracts\Love\Reactable\Models\Reactable as ReactableContract;
use Cog\Laravel\Love\Reactable\Models\Traits\Reactable;

class Post extends Model implements ReactableContract
{
    use Reactable;
    use SoftDeletes;

    public static $validTypes = ['content', 'video'];

    protected $fillable = ['title', 'description', 'type', 'tags', 'user_ip', 'hash'];

    protected static function boot()
    {
        parent::boot();

        self::saving(function($model) {
            $model->user_id = auth()->id();
            $model->slug = Str::limit(Str::slug($model->title), 255, '');
        });
    }

    public function content()
    {
        switch ($this->type) {
            case 'content':
                return $this->hasOne(PostContent::class);
            default:
                abort(Response::HTTP_BAD_REQUEST, 'Unknown post type');
        }
    }

    public function author()
    {
        return $this->belongsTo('App\User', 'user_id')
            ->withDefault([
                'id' => 0,
                'name' => 'Guest'
            ]);
    }

    public function getUrlAttribute(): string
    {
        return action('PostController@detail', [$this->id, $this->slug]);
    }
}
