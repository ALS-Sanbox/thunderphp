<?php
namespace BasicPosts;

use \Model\Model;
use \Core\Image;

defined('ROOT') or die("Direct script access denied");

class Content extends Model {
    public function extract_images(string $content): string {
        $folder = plugin_path('uploads/');
        $savedImages = Image::extractImagesFromContent($content, $folder);
        foreach ($savedImages as $imageInfo) {
            $originalTag = $imageInfo['originalTag'];
            $originalFilename = $imageInfo['originalFilename'];
            $savedPath = plugin_http_path('uploads/' . basename($imageInfo['savedPath']));
            $attributes = '';
            if (preg_match_all('/(\w+)=["\']([^"\']*)["\']/', $originalTag, $attrMatches, PREG_SET_ORDER)) {
                foreach ($attrMatches as $attr) {
                    $attrName = strtolower($attr[1]);
                    if ($attrName !== 'src' && $attrName !== 'data-filename') {
                        $attributes .= ' ' . $attrName . '="' . htmlspecialchars($attr[2], ENT_QUOTES) . '"';
                    }
                }
            }

            $replacement = '<img src="' . $savedPath . '"' . $attributes;

            $content = str_replace($originalTag, $replacement, $content);
        }

        return $content;
    }

    public function delete_unsued_images(string $old_content, string $new_content): void {
        $uploadDir = plugin_path('uploads/'); // Physical path
        $uploadHttpPath = plugin_http_path('uploads/'); // URL path

        // Extract image sources from both contents
        preg_match_all('/<img[^>]+src=["\']([^"\']+)["\']/', $old_content, $oldMatches);
        preg_match_all('/<img[^>]+src=["\']([^"\']+)["\']/', $new_content, $newMatches);

        $oldImages = $oldMatches[1] ?? [];
        $newImages = $newMatches[1] ?? [];

        // Build set of images in new content
        $newFilenames = array_map(function ($src) {
            return basename(parse_url($src, PHP_URL_PATH));
        }, $newImages);

        // Find unused images
        foreach ($oldImages as $src) {
            $filename = basename(parse_url($src, PHP_URL_PATH));
            if (!in_array($filename, $newFilenames)) {
                $filePath = $uploadDir . $filename;
                if (file_exists($filePath)) {
                    @unlink($filePath);
                }
            }
        }
    }


    public function delete_images(string $content): void {
        $uploadDir = plugin_path('uploads/'); // Server path to uploads folder

        // Match <img src="..."> where src points to plugin uploads path
        preg_match_all('/<img[^>]+src=["\']([^"\']+)["\']/', $content, $matches);

        if (!empty($matches[1])) {
            foreach ($matches[1] as $imgSrc) {
                // Convert URL to local file path
                $filename = basename(parse_url($imgSrc, PHP_URL_PATH)); // Get file name from URL
                $filePath = $uploadDir . $filename;

                if (file_exists($filePath)) {
                    @unlink($filePath); // Delete image file
                }
            }
        }
    }
}