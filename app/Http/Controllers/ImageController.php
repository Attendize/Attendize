<?php

namespace App\Http\Controllers;

class ImageController extends Controller
{
    /**
     * Generate a thumbnail for a given image
     *
     * @param $image_src
     * @param bool $width
     * @param bool $height
     * @param int $quality
     */
    public function generateThumbnail($image_src, $width = false, $height = false, $quality = 90)
    {
        $img = Image::make('public/foo.jpg');

        $img->resize(320, 240);

        $img->insert('public/watermark.png');

        $img->save('public/bar.jpg');
    }
}
