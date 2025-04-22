<?php

namespace Siteusers;

use \Model\Model;

defined('ROOT') or die("Direct script access denied");

/**
 * Siteusers model
 */
class Siteusers extends Model {
    protected $table = 'siteusers';
    public $primary_key = 'id';

    // Allowed columns for insertion
    protected $allowedColumns = [
        'first_name',
        'last_name',
        'image',
        'email',
        'password',
        'date_created',
    ];

    protected $allowedUpdateColumns = [
        'first_name',
        'last_name',
        'image',
        'email',
        'password',
        'deleted',
        'date_updated',
        'date_deleted',
    ];

    public function validate_insert(array $data): bool {
        if (empty($data['first_name'])) {
            $this->errors['first_name'] = "First Name is required.";
        } elseif (!preg_match("/^[a-zA-Z]+$/", trim($data['first_name']))) {
            $this->errors['first_name'] = "First Name can only have letters with no spaces allowed.";
        }

        if (empty($data['last_name'])) {
            $this->errors['last_name'] = "Last Name is required.";
        } elseif (!preg_match("/^[a-zA-Z]+$/", trim($data['last_name']))) {
            $this->errors['last_name'] = "Last Name can only have letters with no spaces allowed.";
        }

        if (empty($data['email'])) {
            $this->errors['email'] = "Email is required.";
        } elseif (!filter_var($data['email'], FILTER_VALIDATE_EMAIL)) {
            $this->errors['email'] = "Invalid email format.";
        }

        if (!empty($data['image'])) {
            $allowedFormats = ['jpg', 'jpeg', 'png', 'gif'];
            $fileExtension = strtolower(pathinfo($data['image'], PATHINFO_EXTENSION));

            if (!in_array($fileExtension, $allowedFormats)) {
                $this->errors['image'] = "Invalid image format. Only JPG, JPEG, PNG, and GIF are allowed.";
            }
        }

        if (empty($data['password'])) {
            $this->errors['password'] = "Password is required.";
        } elseif (strlen($data['password']) < 8) {
            $this->errors['password'] = "Password must be at least 8 characters long.";
        } elseif (!preg_match('/[\W]/', $data['password'])) {
            $this->errors['password'] = "Password must contain at least one special character.";
        }

        return empty($this->errors);
    }

    public function validate_update(array $data): bool {
        if (isset($data['first_name'])) {
            if (!preg_match("/^[a-zA-Z]+$/", trim($data['first_name']))) {
                $this->errors['first_name'] = "First Name can only have letters with no spaces allowed";
            }
        }

        if (isset($data['last_name'])) {
            if (!preg_match("/^[a-zA-Z]+$/", trim($data['last_name']))) {
                $this->errors['last_name'] = "Last Name can only have letters with no spaces allowed";
            }
        }

        if (isset($data['email']) && !filter_var($data['email'], FILTER_VALIDATE_EMAIL)) {
            $this->errors['email'] = "Invalid email format.";
        }

        if (!empty($data['image'])) {
            $allowedFormats = ['jpg', 'jpeg', 'png', 'gif'];
            $fileExtension = strtolower(pathinfo($data['image'], PATHINFO_EXTENSION));

            if (!in_array($fileExtension, $allowedFormats)) {
                $this->errors['image'] = "Invalid image format. Only JPG, JPEG, PNG, and GIF are allowed.";
            }
        }

        if (!empty($data['password'])) {
            if (strlen($data['password']) < 1) {
                $this->errors['password'] = "Password must be at least 8 characters long.";
            } elseif (!preg_match('/[\W]/', $data['password'])) {
                $this->errors['password'] = "Password must contain at least one special character.";
            }
        }

        return empty($this->errors);
    }

    public function insert(array $data): bool {
        $data['date_created'] = date('Y-m-d H:i:s'); // Auto add creation date
        return $this->create($data);
    }

    public function update_user(int $id, array $data): bool {
        if (!$this->validate_update($data)) {
            return false;
        }

        $data['date_updated'] = date('Y-m-d H:i:s'); // Auto update modified date
        return $this->update($id, $data);
    }
}
