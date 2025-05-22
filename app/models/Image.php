<?php

namespace Core;
use GdImage;

defined('ROOT') or die("Direct script access denied");

class Image {
    private ?GdImage $image = null;
    private int $imageType;
    private string $filename;

    public function load(string $filename): void {
        $imageDetails = getimagesize($filename);
        $this->filename = $filename;

        if (!$imageDetails) {
            throw new \Exception("Failed to load image: $filename");
        }

        $this->imageType = $imageDetails[2];

        switch ($this->imageType) {
            case IMAGETYPE_JPEG:
                $this->image = imagecreatefromjpeg($filename);
                break;
            case IMAGETYPE_PNG:
                $this->image = imagecreatefrompng($filename);
                break;
            case IMAGETYPE_GIF:
                $this->image = imagecreatefromgif($filename);
                break;
            case IMAGETYPE_WEBP:
                if (!function_exists('imagecreatefromwebp')) {
                    throw new \Exception("WebP not supported on this server.");
                }
                $this->image = imagecreatefromwebp($filename);
                break;
            case IMAGETYPE_TIFF_II:
            case IMAGETYPE_TIFF_MM:
                throw new \Exception("TIFF format is not supported by GD. Use Imagick instead.");
            default:
                throw new \Exception("Unsupported image format.");
        }
    }

    public function resize(int $width, int $height): void {
        if ($this->image === null) {
            throw new \Exception("Image not loaded.");
        }

        $newImage = imagecreatetruecolor($width, $height);

        if (in_array($this->imageType, [IMAGETYPE_PNG, IMAGETYPE_GIF, IMAGETYPE_WEBP])) {
            $transparentColor = imagecolorallocatealpha($newImage, 0, 0, 0, 127);
            imagefill($newImage, 0, 0, $transparentColor);
            imagealphablending($newImage, false);
            imagesavealpha($newImage, true);
        }

        imagecopyresampled($newImage, $this->image, 0, 0, 0, 0, $width, $height, imagesx($this->image), imagesy($this->image));
        $this->image = $newImage;
    }

    public function crop(int $x, int $y, int $width, int $height): void {
        if ($this->image === null) {
            throw new \Exception("Image not loaded.");
        }

        $this->image = imagecrop($this->image, ['x' => $x, 'y' => $y, 'width' => $width, 'height' => $height]);
        if ($this->image === false) {
            throw new \Exception("Failed to crop image.");
        }
    }

    public function save(string $filename = "", int $quality = 90, int $pngCompressionLevel = 6): void {
        if ($this->image === null) {
            throw new \Exception("Image not loaded.");
        }

        if (empty($filename)) {
            $filename = $this->getDirectory() . '/' . basename($this->filename);
        }

        switch ($this->imageType) {
            case IMAGETYPE_JPEG:
                imagejpeg($this->image, $filename, $quality);
                break;
            case IMAGETYPE_PNG:
                imagepng($this->image, $filename, $pngCompressionLevel);
                break;
            case IMAGETYPE_GIF:
                imagegif($this->image, $filename);
                break;
            case IMAGETYPE_WEBP:
                if (!function_exists('imagewebp')) {
                    throw new \Exception("WebP not supported on this server.");
                }
                imagewebp($this->image, $filename, $quality);
                break;
            default:
                throw new \Exception("Unsupported image format.");
        }
    }

    public function convertFormat(string $filename = "", string $newFormat): void {
        if ($this->image === null) {
            throw new \Exception("Image not loaded.");
        }

        if (empty($filename)) {
            $pathInfo = pathinfo($this->filename);
            $filename = $pathInfo['dirname'] . '/' . $pathInfo['filename'] . '.' . $newFormat;
        }

        switch (strtolower($newFormat)) {
            case 'jpeg':
            case 'jpg':
                imagejpeg($this->image, $filename);
                break;
            case 'png':
                imagepng($this->image, $filename);
                break;
            case 'gif':
                imagegif($this->image, $filename);
                break;
            case 'webp':
                if (!function_exists('imagewebp')) {
                    throw new \Exception("WebP not supported on this server.");
                }
                imagewebp($this->image, $filename);
                break;
            default:
                throw new \Exception("Unsupported format.");
        }
    }

    public function copy(string $destination): bool {
        if ($this->image === null) {
            throw new \Exception("Image not loaded.");
        }

        if (empty($destination)) {
            throw new \Exception("Invalid destination path.");
        }

        $result = copy($this->getFilename(), $destination);

        if (!$result) {
            throw new \Exception("Failed to copy image to $destination.");
        }

        return true;
    }

    public function output(int $quality = 90, int $pngCompressionLevel = 6): void {
        if ($this->image === null) {
            throw new \Exception("Image not loaded.");
        }

        switch ($this->imageType) {
            case IMAGETYPE_JPEG:
                header('Content-Type: image/jpeg');
                imagejpeg($this->image, null, $quality);
                break;
            case IMAGETYPE_PNG:
                header('Content-Type: image/png');
                imagepng($this->image, null, $pngCompressionLevel);
                break;
            case IMAGETYPE_GIF:
                header('Content-Type: image/gif');
                imagegif($this->image);
                break;
            case IMAGETYPE_WEBP:
                if (!function_exists('imagewebp')) {
                    throw new \Exception("WebP not supported on this server.");
                }
                header('Content-Type: image/webp');
                imagewebp($this->image, null, $quality);
                break;
            default:
                throw new \Exception("Unsupported image format.");
        }

        exit();
    }

    public function __destruct() {
        if ($this->image !== null) {
            imagedestroy($this->image);
        }
    }

    public function createThumbnail(int $thumbWidth, int $thumbHeight = 0, string $filename = ""): void {
        if ($this->image === null) {
            throw new \Exception("Image not loaded.");
        }

        if (empty($filename)) {
            $filename = $this->generateThumbnailFilename();
        }

        if (file_exists($filename)) {
            $this->image = $this->load($filename);
            return;
        }

        $originalWidth = imagesx($this->image);
        $originalHeight = imagesy($this->image);
        $aspectRatio = $originalWidth / $originalHeight;

        if ($thumbHeight == 0) {
            $thumbHeight = (int)($thumbWidth / $aspectRatio);
        } else {
            $thumbWidth = (int)($thumbHeight * $aspectRatio);
        }

        $thumbnail = imagecreatetruecolor($thumbWidth, $thumbHeight);

        if (in_array($this->imageType, [IMAGETYPE_PNG, IMAGETYPE_GIF, IMAGETYPE_WEBP])) {
            $transparentColor = imagecolorallocatealpha($thumbnail, 0, 0, 0, 127);
            imagefill($thumbnail, 0, 0, $transparentColor);
            imagealphablending($thumbnail, false);
            imagesavealpha($thumbnail, true);
        }

        imagecopyresampled($thumbnail, $this->image, 0, 0, 0, 0, $thumbWidth, $thumbHeight, $originalWidth, $originalHeight);
        $this->image = $thumbnail;
        $this->save($filename);
    }

    private function generateThumbnailFilename(): string {
        $pathInfo = pathinfo($this->getFilename());
        return $pathInfo['dirname'] . '/' . $pathInfo['filename'] . '_thumbnail.' . $pathInfo['extension'];
    }

    public function getFilename(): string {
        return $this->filename;
    }

    public function getDirectory(): string {
        return dirname($this->filename);
    }

    public static function extractImagesFromContent(string $content, string $folder): array {
        if (!is_dir($folder)) {
            mkdir($folder, 0755, true);
        }

        preg_match_all('/<img[^>]+src="data:image\/[^;]+;base64,([^"]+)"[^>]+data-filename="([^"]+)"/', $content, $matches, PREG_SET_ORDER);
        $savedPaths = [];

        foreach ($matches as $match) {
            $base64 = $match[1];
            $originalFilename = $match[2];
            $timestamp = time();
            $newFilename = $timestamp . rand(0, 1000) . '_' . $originalFilename;
            $savePath = rtrim($folder, '/') . '/' . $newFilename;

            $imageData = base64_decode($base64);
            $tmpFile = tempnam(sys_get_temp_dir(), 'img_');
            file_put_contents($tmpFile, $imageData);

            try {
                $img = new self();
                $img->load($tmpFile);
                $originalWidth = imagesx($img->image);
                $originalHeight = imagesy($img->image);
                $newWidth = 1000;
                $newHeight = (int)($originalHeight * ($newWidth / $originalWidth));

                $img->resize($newWidth, $newHeight);
                $img->save($savePath);

                $savedPaths[] = [
                    'originalFilename' => $originalFilename,
                    'savedPath' => $savePath,
                    'originalTag' => $match[0],
                ];
            } catch (\Exception $e) {
                // Log or handle
            } finally {
                unlink($tmpFile);
            }
        }

        return $savedPaths;
    }
}
