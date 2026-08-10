<?php
namespace Images;

use \Model\Model;

defined('ROOT') or die("Direct script access denied");

class Images extends Model {
    protected $table = 'images';
    public $primary_key = 'id';
    public $order_column = 'id';
    public $order = 'desc';

    protected $allowedColumns = [
        'filename',
        'path',
        'original_name',
        'size',
        'user_id',
        'date_created',
    ];

    protected $allowedUpdateColumns = [];

    public function insert(array $data): bool {
        return $this->create($data);
    }
}
