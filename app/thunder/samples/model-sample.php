<?php

namespace App\Models;

class SampleModel extends Model
{
    protected $table = 'samples';
    public $primary_key = 'id';

    // Allowed columns for insertion
    protected $allowedColumns = [
        'name',
        'email',
        'date_created',
    ];

    // Allowed columns for updates
    protected $allowedUpdateColumns = [
        'name',
        'email',
        'date_updated',
    ];

    // Validate insert data
    public function validate_insert(array $data): bool
    {
        if (empty($data['name'])) {
            $this->errors['name'] = "Name is required.";
        } elseif (strlen($data['name']) < 3) {
            $this->errors['name'] = "Name must be at least 3 characters.";
        }

        if (empty($data['email'])) {
            $this->errors['email'] = "Email is required.";
        } elseif (!filter_var($data['email'], FILTER_VALIDATE_EMAIL)) {
            $this->errors['email'] = "Invalid email format.";
        }

        return empty($this->errors);
    }

    // Validate update data
    public function validate_update(array $data): bool
    {
        if (isset($data['name']) && strlen($data['name']) < 3) {
            $this->errors['name'] = "Name must be at least 3 characters.";
        }

        if (isset($data['email']) && !filter_var($data['email'], FILTER_VALIDATE_EMAIL)) {
            $this->errors['email'] = "Invalid email format.";
        }

        return empty($this->errors);
    }

    // Batch insert records
    public function batchInsert(array $records): bool
    {
        foreach ($records as $record) {
            if (!$this->validate_insert($record)) {
                return false;
            }
        }

        return $this->createMultiple($records);
    }

    // Batch update records
    public function batchUpdate(array $records): bool
    {
        foreach ($records as $record) {
            if (!$this->validate_update($record)) {
                return false;
            }
        }

        foreach ($records as $record) {
            $this->update($record['id'], $record);
        }

        return true;
    }
}
