<?php

namespace App\Http\Controllers\Api\V1\shared;

use App\Http\Controllers\Controller;
use App\Models\Image;
use App\Models\NewsImage;
use App\Models\NewsVideo;
use App\Models\Video;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage as store;

class FilesController extends Controller
{

    public static function saveImages($images = [], $news_id)
    {
        foreach ($images as $key => $imageBase64) {

            $imageName = self::saveFile($imageBase64, "images");

            $image = new NewsImage();
            $image->name = $imageName;
            $image->news_id = $news_id;
            $image->save();
        }
    }

    public static function saveVideos($videos = [], $news_id)
    {
        foreach ($videos as $key => $video) {
            $videoName = self::saveFile($video, "videos");

            $video = new NewsVideo();
            $video->name = $videoName;
            $video->news_id = $news_id;
            $video->save();
        }
    }

    public static function saveFile($data64, $urlPath)
    {
        $fileName = Str::random(40) . '.';
        if ($urlPath == "videos") {
            $fileName = $fileName . 'mp4';
        } else {
            $fileName = $fileName . 'jpg';
        }
        store::disk("do_spaces")->putFileAs(env('DO_SPACES_PATH') . $urlPath, $data64, $fileName);
        return $fileName;
    }

    public static function deleteImage($images = [])
    {
        foreach ($images as $key => $url) {
            $image = NewsImage::where('url', $url)->first();
            if ($image) {
                $image->delete();
            }
        }
    }

    public static function deleteVideo($videos = [])
    {
        foreach ($videos as $key => $url) {
            $video = NewsVideo::where('url', $url)->first();
            if ($video) {
                $video->delete();
            }
        }
    }
}
