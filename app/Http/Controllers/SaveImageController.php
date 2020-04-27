<?php
namespace App\Http\Controllers;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Storage;

class SaveImageController extends Controller {

    protected $disk = 'public';
    protected $imageFolder = 'images';
    protected $maxSize = 10 * 1024;

    public function file(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'image' => 'required|image' . "|max:{$this->maxSize}",
        ]);
        if ($validator->fails()) {
            return ['success' => 0];
        }
        $path = $request->file('image')->store($this->imageFolder, $this->disk);
        return [
            'success' => 1,
            'file' => [
                'url' => Storage::disk($this->disk)->url($path),
            ]
        ];
    }

    public function url(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'url' => [
                'required',
                'active_url',
                function ($attribute, $value, $fail) {
                    $imageDetails = getimagesize($value);
                    if (!in_array($imageDetails['mime'] ?? '', [
                        'image/jpeg',
                        'image/webp',
                        'image/gif',
                        'image/png',
                        'image/svg+xml',
                    ])) {
                        $fail($attribute . ' is invalid.');
                    }
                },
            ],
        ]);
        if ($validator->fails()) {
            return ['success' => 0];
        }
        $url = $request->input('url');
        $cacheKey = 'remote_image_url_' . $url;
        $urlFromCache = Cache::get($cacheKey);
        if ($urlFromCache) {
            return [
                'success' => 1,
                'file' => [
                    'url' => $urlFromCache
                ]
            ];
        }
        $imageContents = file_get_contents($url);
        if (!$imageContents) {
            return ['success' => 0];
        }
        $extension = substr($url, strrpos($url, '.') + 1);
        $nameWithPath = $this->imageFolder . '/' . uniqid('', true) . '.' . $extension;
        if (Storage::disk($this->disk)->put($nameWithPath, $imageContents)) {
            $resultUrl = Storage::disk($this->disk)->url($nameWithPath);
            Cache::put($cacheKey, $resultUrl, now()->addDay());
            return [
                'success' => 1,
                'file' => [
                    'url' => $resultUrl
                ]
            ];
        }
        return ['success' => 0];
    }
}
