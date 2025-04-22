<?php
namespace Models;

use Core\Database;

class DragDropModel {
    protected $db;

    public function __construct() {
        $this->db = new Database();
    }

    public function updateItemPosition($itemId, $newPosition) {
        $sql = "UPDATE items SET position = ? WHERE id = ?";
        return $this->db->query($sql, [$newPosition, $itemId]);
    }
}