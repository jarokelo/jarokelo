<?php

namespace app\components\jqueryupload;

use yii\helpers\ArrayHelper;
use yii\web\UploadedFile;

class ImageManipulate
{
    protected static function imageCreateFromAny($filepath)
    {
        $type = @exif_imagetype($filepath);
        $allowedTypes = [
            1,  // [] gif
            2,  // [] jpg
            3,  // [] png
            //6   // [] bmp
        ];
        if ($type === false || !in_array($type, $allowedTypes)) {
            return false;
        }
        $savetype = '';
        switch ($type) {
            case 1:
                $savetype = 'gif';
                $im = @imageCreateFromGif($filepath);
                break;
            case 2:
                $savetype = 'jpeg';
                $im = @imageCreateFromJpeg($filepath);

                if ($im !== false) {
                    $exif = @exif_read_data($filepath);
                    if ($exif !== false && isset($exif['Orientation'])) {
                        switch ($exif['Orientation']) {
                            case 8:
                                $im = imagerotate($im, 90, 0);
                                break;
                            case 3:
                                $im = imagerotate($im, 180, 0);
                                break;
                            case 6:
                                $im = imagerotate($im, -90, 0);
                                break;
                        }
                    }
                }
                break;
            case 3:
                $savetype = 'png';
                $im = @imageCreateFromPng($filepath);
                break;
            /*case 6 :
                $savetype = 'png';
                $im = imageCreateFromWBmp($filepath);
            break;*/
        }
        if ($im === false) {
            return false;
        }

        return [
            'savetype' => $savetype,
            'image' => $im,
        ];
    }

    protected static function raiseMemoryLimit()
    {
        $orig_limit = ini_get('memory_limit');

        $mem_limit = trim($orig_limit);
        $last = strtolower($mem_limit[strlen($mem_limit) - 1]);
        $mem_limit = (int)$mem_limit;

        switch ($last) {
            case 'g':
            case 'gb':
                $mem_limit *= 1024;
                break;
            case 'm':
            case 'mb':
                $mem_limit *= 1024;
                break;
            case 'k':
            case 'kb':
                $mem_limit *= 1024;
        }

        if ($mem_limit < 512 * 1024 * 1024) {
            ini_set('memory_limit', '512M');
        }

        return $orig_limit;
    }

    protected static function loadImage($filename)
    {
        $orig_limit = static::raiseMemoryLimit();
        $image_arr = static::imageCreateFromAny($filename);
        ini_set('memory_limit', $orig_limit);
        if ($image_arr === false) {
            return false;
        }
        $image = $image_arr['image'];
        $savetype = $image_arr['savetype'];

        $width = imagesx($image);
        $height = imagesy($image);

        return [
            $image,
            $savetype,
            $width,
            $height,
        ];
    }

    protected static function saveImage($thumb, $target_fn, $savetype)
    {
        switch ($savetype) {
            case 'gif':
                return imagegif($thumb, $target_fn);
                break;
            case 'jpeg':
                return imagejpeg($thumb, $target_fn);
                break;
            case 'png':
                return imagepng($thumb, $target_fn, 9);
                break;
        }

        return false;
    }

    /**
     * Resize an image so that it fits inside given dimensions
     * @param string $filename input file name
     * @param string $target_fn output file name
     * @param int $max_width width
     * @param int $max_height height
     */
    public static function resize($filename, $target_fn, $max_width, $max_height)
    {
        $load = static::loadImage($filename);
        if ($load === false) {
            return false;
        }
        list($image, $savetype, $width, $height) = $load;

        $new_width = $width;
        $new_height = $height;

        $width_diff = $max_width / $width;
        $height_diff = $max_height / $height;

        if ($max_width < $width && $max_height < $height) {
            if ($height_diff > $width_diff) {
                $new_width = $max_width;
                $new_height = $height * $width_diff;
            } else {
                $new_height = $max_height;
                $new_width = $width * $height_diff;
            }
        } elseif ($max_width < $width) {
            $new_width = $max_width;
            $new_height = $height * $width_diff;
        } elseif ($max_height < $height) {
            $new_height = $max_height;
            $new_width = $width * $height_diff;
        }

        $new_image = imagecreatetruecolor($new_width, $new_height);
        imagealphablending($new_image, false);
        imagesavealpha($new_image, true);

        $ret = imagecopyresampled(
            $new_image,
            $image,
            0,
            0,
            0,
            0,
            $new_width,
            $new_height,
            $width,
            $height
        );

        if (!$ret) {
            return false;
        }

        return static::saveImage($new_image, $target_fn, $savetype);
    }

    /**
     * Resize and crop an image to exact dimensions
     * @param string $filename input file name
     * @param string $target_fn output file name
     * @param int $thumb_width width
     * @param int $thumb_height height
     */
    public static function crop($filename, $target_fn, $thumb_width, $thumb_height)
    {
        $load = static::loadImage($filename);
        if ($load === false) {
            return false;
        }
        list($image, $savetype, $width, $height) = $load;

        $original_aspect = $width / $height;
        $thumb_aspect = $thumb_width / $thumb_height;

        if ($original_aspect >= $thumb_aspect) {
            // If image is wider than thumbnail (in aspect ratio sense)
            $new_height = $thumb_height;
            $new_width = $width / ($height / $thumb_height);
        } else {
            // If the thumbnail is wider than the image
            $new_width = $thumb_width;
            $new_height = $height / ($width / $thumb_width);
        }

        $thumb = imagecreatetruecolor($thumb_width, $thumb_height);
        imagealphablending($thumb, false);
        imagesavealpha($thumb, true);

        // Resize and crop
        $ret = imagecopyresampled(
            $thumb,
            $image,
            0 - ($new_width - $thumb_width) / 2, // Center the image horizontally
            0 - ($new_height - $thumb_height) / 2, // Center the image vertically
            0,
            0,
            $new_width,
            $new_height,
            $width,
            $height
        );
        if (!$ret) {
            return false;
        }

        return static::saveImage($thumb, $target_fn, $savetype);
    }

    public static function validateMinResolution(UploadedFile $file, $minWidth, $minHeight)
    {
        if ($minWidth || $minHeight) {
            $image = static::loadImage($file->tempName);

            $width = ArrayHelper::getValue($image, 2, 0);
            $height = ArrayHelper::getValue($image, 3, 0);

            if ($minWidth && $width < $minWidth) {
                return false;
            }

            if ($minHeight && $height < $minHeight) {
                return false;
            }
        }

        return true;
    }
}
