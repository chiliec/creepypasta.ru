<?php

namespace App;

use Conner\Tagging\Taggable;
use EditorJS\EditorJSException;
use Illuminate\Contracts\View\Factory as ViewFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;
use Cog\Contracts\Love\Reactable\Models\Reactable as ReactableContract;
use Cog\Laravel\Love\Reactable\Models\Traits\Reactable;
use EditorJS\EditorJS;

class Post extends Model implements ReactableContract
{
    use Reactable;
    use SoftDeletes;
    use Taggable;

    protected $fillable = ['title', 'content', 'tags', 'user_ip', 'hash'];

    protected $casts = ['content' => 'array'];

    protected static function boot()
    {
        parent::boot();

        self::saving(function($model) {
            $model->user_id = auth()->id();
            $model->slug = Str::limit(Str::slug($model->title), 255, '');
            $html = self::convertToHTML($model->content);
            $model->description = Str::limit($html, 300, '...');
            $model->text = $html;
            $model->hash = md5($html);
            if ($model->tags) {
                $model->tag($model->tags);
            }
        });
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

    public function getTagsStringAttribute(): string
    {
        $tags = $this->tags->pluck('name')->toArray();
        return implode(', ', $tags);
    }

    private static function convertToHTML($json): string
    {
        if (empty($json)) {
            return '';
        }
        if (!is_string($json)) {
            $newData = json_encode($json);
            if (json_last_error() === \JSON_ERROR_NONE) {
                $json = $newData;
            }
        }
        $config = config('editor-js');
        try {
            $editor = new EditorJS($json, json_encode($config));
        } catch (EditorJSException $e) {
            return $e->getMessage();
        }
        $blocks = $editor->getBlocks();
        $output = '';
        for ($i = 0; $i < count($blocks); $i++) {
            $keys = [];
            foreach($blocks[$i]['data'] as $key => $value) {
                $keys[] = $key;
                $$key = $value;
            }
            $factory = app(ViewFactory::class);
            $view = 'blocks.' . $blocks[$i]['type'];
            if ($factory->exists($view)) {
                $output .= $factory->make($view, compact($keys))->render();
            } else {
                $output .= "<p>View {$view} not found.</p>";
            }
        }
        return html_entity_decode($output);
    }
}
