<?php

namespace App;

use Conner\Tagging\Taggable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;
use Cog\Contracts\Love\Reactable\Models\Reactable as ReactableContract;
use Cog\Laravel\Love\Reactable\Models\Traits\Reactable;

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

    private static function convertToHTML($content): string
    {
        if (empty($content)) {
            return '';
        }

        $blocks = Arr::get($content, 'blocks', []);
        $output = '';

        foreach ($blocks as $block) {
            switch ($block['type']) {
                case 'paragraph':
                    $text = Arr::get($block, 'data.text');
                    $output .= "<p>{$text}</p>";
                    break;
                case 'header':
                    $level = Arr::get($block, 'data.level');
                    $text = Arr::get($block, 'data.text');
                    $output .= "<h{$level}>{$text}</h{$level}>";
                    break;
                case 'delimiter':
                    $output .= '<div class="ce-delimiter"></div>';
                    break;
                case 'code':
                    $code = Arr::get($block, 'data.code');
                    $output .= "<pre><code>{$code}</code></pre>";
                    break;
            }
        }

        return html_entity_decode($output);
    }
}
