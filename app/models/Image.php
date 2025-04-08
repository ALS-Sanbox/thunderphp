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
            default:
                throw new \Exception("Unsupported image format.");
        }
    }

    public function resize(int $width, int $height): void {
        if ($this->image === null) {
            throw new \Exception("Image not loaded.");
        }

        $newImage = imagecreatetruecolor($width, $height);

        // Preserve transparency for PNG and GIF images
        if ($this->imageType === IMAGETYPE_PNG || $this->imageType === IMAGETYPE_GIF) {
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
    
        switch ($newFormat) {
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

        // Set the appropriate header based on image type
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
    
        if ($this->imageType === IMAGETYPE_PNG || $this->imageType === IMAGETYPE_GIF) {
            $transparentColor = imagecolorallocatealpha($thumbnail, 0, 0, 0, 127);
            imagefill($thumbnail, 0, 0, $transparentColor);
            imagealphablending($thumbnail, false);
            imagesavealpha($thumbnail, true);
        }
    
        imagecopyresampled($thumbnail, $this->image, 0, 0, 0, 0, $thumbWidth, $thumbHeight, $originalWidth, $originalHeight);
    
        $this->image = $thumbnail;
    
        // Save the thumbnail image
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
        return dirname($this->filename);  // Get the directory part of the original filename
    }
    
}

/* Example usage
try {
    $image = new Image();
    $image->load("example.jpg");  // Load an image

    // Resize the image to 800x600
    $image->resize(800, 600);

    // Crop the image (x, y, width, height)
    $image->crop(0, 0, 20, 50);

    // Save as a new file
    $image->save("resized_and_cropped.jpg");

    // Convert image format to PNG
    $image->convertFormat("converted_image.png", "png");

    // Create a thumbnail with a width of 150px (height will be scaled accordingly)
    $image->createThumbnail(150);

    // Resize to 150x100 (will stretch or crop if needed)
    $image->createThumbnail(150, 100);  

    // Output the image to browser
    // $image->output();

    // Copy the loaded image to a new location
    $image->copy("copied_example.jpg");

} catch (\Exception $e) {
    echo "Error: " . $e->getMessage();
}*/
