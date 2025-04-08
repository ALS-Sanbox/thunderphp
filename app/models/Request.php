<?php

namespace Core;
defined('ROOT') or die("Direct script access denied");

class Request {
    public $upload_max_size     = 20;
    public $upload_errors       = [];
    public $upload_folder       = 'uploads';
    public $upload_file_types   = [
        'image/jpeg',
        'image/jpg',
        'image/webp',
        'image/gif',
        'image/png',
    ];

    private function getSuperGlobalValue(string $key = '', string|array $superGlobal = ''):mixed {
        if (empty($key)) {
            return $superGlobal;
        }

        if (!empty($superGlobal[$key])) {
            return $superGlobal[$key];
        }

        return '';
    }

    public function method(): string {
        return $_SERVER['REQUEST_METHOD'];
    }

    public function posted(): bool {
        return $this->method() === 'POST';
    }

    public function post(string $key = ''): string|array {
        return $this->getSuperGlobalValue($key, $_POST);
    }

    public function input(string $key, string $default = ''): string {
        return !empty($_POST[$key]) ? $_POST[$key] : $default;
    }

    public function get(string $key = ''): string|array {
        return $this->getSuperGlobalValue($key, $_GET);
    }

    public function files(string $key = ''): string|array {
        return $this->getSuperGlobalValue($key, $_FILES);
    }

    public function all(string $key = ''): string|array {
        return $this->getSuperGlobalValue($key, $_REQUEST);
    }

    public function upload_files(string $key = '', string $directory = ''): string|array {
        $files = $this->files($key);
        if (empty($files) || !isset($files['name']) || empty($files['tmp_name'])) {
            $this->upload_errors[] = "No file uploaded or invalid file.";
            return $this->upload_errors;
        }
    
        // Ensure the directory is set and exists
        $upload_dir = !empty($directory) ? rtrim($directory, '/') : $this->upload_folder;
        if (!is_dir($upload_dir)) {
            $this->upload_errors[] = "Upload directory does not exist.";
            return $this->upload_errors;
        }
    
        $file_name = pathinfo($files['name'], PATHINFO_FILENAME);
        $file_extension = pathinfo($files['name'], PATHINFO_EXTENSION);
        $file_tmp  = $files['tmp_name'];
    
        // Ensure tmp_name is not empty before calling mime_content_type
        if (!file_exists($file_tmp) || empty($file_tmp)) {
            $this->upload_errors[] = "Temporary file is missing.";
            return $this->upload_errors;
        }
    
        $file_size = $files['size'] / (1024 * 1024); // Convert size to MB
        $file_type = mime_content_type($file_tmp);
        $upload_path = $upload_dir . '/' . $file_name . '.' . $file_extension;
    
        $counter = 1;
        while (file_exists($upload_path)) {
            $upload_path = $upload_dir . '/' . $file_name . "_" . $counter . '.' . $file_extension;
            $counter++;
        }
    
        if (!in_array($file_type, $this->upload_file_types)) {
            $this->upload_errors[] = "Invalid file type: " . $file_type;
            return $this->upload_errors;
        }
    
        if ($file_size > $this->upload_max_size) {
            $this->upload_errors[] = "File size exceeds limit of " . $this->upload_max_size . "MB.";
            return $this->upload_errors;
        }
    
        if (move_uploaded_file($file_tmp, $upload_path)) {
            return $upload_path;
        } else {
            $this->upload_errors[] = "Failed to move uploaded file.";
            return $this->upload_errors;
        }
    }
    
    
    public function upload_multiple_files(string $key = '', string $directory = ''): array {
        $files = $this->files($key);
        $uploaded_files = [];
    
        if (empty($files) || !isset($files['name']) || !is_array($files['name'])) {
            $this->upload_errors[] = "No files uploaded.";
            return $this->upload_errors;
        }
    
        foreach ($files['name'] as $index => $file_name) {
            $file_data = [
                'name' => $file_name,
                'tmp_name' => $files['tmp_name'][$index],
                'size' => $files['size'][$index],
                'type' => $files['type'][$index]
            ];
    
            $_FILES[$key] = $file_data;
            $result = $this->upload_files($key, $directory);
            
            if (is_array($result)) {
                $uploaded_files = array_merge($uploaded_files, $result);
            } else {
                $uploaded_files[] = $result;
            }
        }
    
        return $uploaded_files;
    }
    
    public function delete_file(string $file_path): bool {
        if (file_exists($file_path)) {
            return unlink($file_path);
        }
        
        return false;
    }  
}
