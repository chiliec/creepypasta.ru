<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Request;

class LinkController extends Controller
{
    public function fetch(Request $request)
    {
        $url = trim($request->input('url'));
        $remoteContent = $this->getRemoteContent($url, $request->userAgent());
        if (!is_array($remoteContent)) {
            return ['success' => 0];
        }
        $url = $remoteContent['url'];
        $content = $remoteContent['content'];
        $meta = $this->parseMetadata($content);
        if (!is_array($meta)) {
            return ['success' => 0];
        }
        $results = [
            'success' => 1,
            'link' => $url,
            'meta' => [
                'title' => $meta['title'],
                'description' => $meta['description']
            ]
        ];
        $images = $meta['images'];
        if (!empty($images)) {
            $results['meta']['image']['url'] = $images[0];
        } else if ($favicon = $this->getFavicon($url)) {
            $results['meta']['image']['url'] = $favicon;
        }
        return $results;
    }

    /**
     * Get array metadata from HTML content
     * with 'title', 'description' and 'images' array
     *
     * @param string $html
     * @return array
     */
    private function parseMetadata(string $html)
    {
        $encoding = mb_detect_encoding($html);
        $doc = new \DOMDocument('', $encoding);
        @$doc->loadHTML($html);
        $result = [
            'title' => '',
            'description' => '',
            'images' => []
        ];
        $titleNodes = $doc->getElementsByTagName('title');
        if ($titleNodes->length > 0) {
            $result['title'] = $titleNodes->item(0)->nodeValue;
        }
        foreach ($doc->getElementsByTagName('meta') as $tag) {
            if ($tag->hasAttribute('property')) {
                $property = strtolower(trim($tag->getAttribute('property')));
                $content = trim($tag->getAttribute('content'));
                if (empty($content)) {
                    continue;
                }
                switch ($property) {
                    case 'og:title':
                    case 'twitter:title':
                        $result['title'] = $content;
                        break;
                    case 'og:description':
                        $result['description'] = $content;
                        break;
                    case 'og:image:secure_url':
                    case 'og:image:url':
                    case 'og:image':
                    case 'twitter:image':
                        $result['images'][] = $content;
                        break;
                    default:
                        break;
                }
            }
            if ($tag->hasAttribute('name')) {
                $name = strtolower(trim($tag->getAttribute('name')));
                $content = trim($tag->getAttribute('content'));
                if (empty($content)) {
                    continue;
                }
                switch ($name) {
                    case 'twitter:title':
                    case 'hdl':
                        $result['title'] = $content;
                        break;
                    case 'description':
                        $result['description'] = $content;
                        break;
                    case 'twitter:image':
                    case 'thumbnail':
                        $result['images'][] = $content;
                        break;
                    default:
                        break;
                }
            }
        }
        return $result;
    }

    /**
     * Return content and final path after redirects from remote url
     * ['content' => '', 'url' => ''] or false
     *
     * @param $url
     * @param string $userAgent
     * @return array|bool
     */
    private function getRemoteContent($url, $userAgent)
    {
        if ($this->invalidUrl($url)) {
            return false;
        }
        $options = [
            CURLOPT_URL => $url,
            CURLOPT_FAILONERROR => true,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_HEADER => true,
            CURLOPT_FOLLOWLOCATION => false,
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_ENCODING => "",
            CURLOPT_AUTOREFERER => true,
            CURLOPT_CONNECTTIMEOUT => 15,
            CURLOPT_TIMEOUT => 15,
            CURLOPT_SSL_VERIFYPEER => false,
            CURLOPT_SSL_VERIFYHOST => false,
            CURLOPT_USERAGENT => $userAgent,
        ];
        $ch = curl_init();
        curl_setopt_array($ch, $options);
        $content = curl_exec($ch);
        $status = curl_getinfo($ch, CURLINFO_HTTP_CODE);

        // CURLOPT_FOLLOWLOCATION by hands because when php safe_mode = ON it's not working
        $maxRedirects = 5;
        for ($i = 0; $i < $maxRedirects; $i++) {
            $redirectStatuses = [301, 302];
            if (in_array($status, $redirectStatuses)) {
                list($header) = explode("\r\n\r\n", $content, 10);
                $matches = [];
                preg_match("/(Location:|URI:)[^(\n)]*/i", $header, $matches);
                if (count($matches) >= 1) {
                    if (count($matches) > 1) {
                        $url = trim(str_replace($matches[1], "", $matches[0]));
                    } else {
                        $url = $matches[0];
                    }
                    if ($this->invalidUrl($url)) {
                        return false;
                    }
                    curl_setopt($ch, CURLOPT_URL, $url);
                    $content = curl_exec($ch);
                    $status = curl_getinfo($ch, CURLINFO_HTTP_CODE);
                }
            } else {
                break;
            }
        }

        curl_close($ch);
        return compact('content', 'url');
    }

    /**
     * Get favicon url from Google service
     *
     * @param $url
     * @param int $size
     * @return string
     */
    private function getFavicon($url, $size = 128)
    {
        $parsed = parse_url($url);
        if ($parsed && $parsed['host']) {
            return "https://www.google.com/s2/favicons?sz={$size}&domain_url=" . $parsed['host'];
        }
    }

    /**
     * Indicates that the URL is not valid
     *
     * @param $url
     * @return bool
     */
    private function invalidUrl($url): bool
    {
        $rules = ['url' => 'required|active_url'];
        $validator = Validator::make(compact('url'), $rules);
        return $validator->fails();
    }
}
