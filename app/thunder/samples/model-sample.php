<?php
namespace {NAMESPACE};

defined('ROOT') or die("Direct script access denied");

/**
 * {CLASS_NAME} model
 */
class {CLASS_NAME} extends Model {
    protected $table = '{TABLE_NAME}s';
    public $primary_key = 'id';

    // Allowed columns for insertion
    protected $allowedColumns = [
        'email',
        'date_created',
    ];

    // Allowed columns for updates
    protected $allowedUpdateColumns = [
        'email',
        'date_updated',
        'date_deleted',
        'deleted',
    ];

    /**
     * Validate insert data
     */
    public function validate_insert(array $data): bool {
        if (empty($data['email'])) {
            $this->errors['email'] = "Email is required.";
        } elseif (!filter_var($data['email'], FILTER_VALIDATE_EMAIL)) {
            $this->errors['email'] = "Invalid email format.";
        }

        return empty($this->errors);
    }

    /**
     * Validate update data
     */
    public function validate_update(array $data): bool {
        if (isset($data['email']) && !filter_var($data['email'], FILTER_VALIDATE_EMAIL)) {
            $this->errors['email'] = "Invalid email format.";
        }

        return empty($this->errors);
    }

    /**
     * Insert user data
     */
    public function insert(array $data): bool {
        if (!$this->validate_insert($data)) {
            return false;
        }

        $data['date_created'] = date('Y-m-d H:i:s'); // Auto add creation date
        return $this->create($data);
    }

    /**
     * Update user data
     */
    public function update_user(int $id, array $data): bool {
        if (!$this->validate_update($data)) {
            return false;
        }

        $data['date_updated'] = date('Y-m-d H:i:s'); // Auto update modified date
        return $this->update($id, $data);
    }
}
