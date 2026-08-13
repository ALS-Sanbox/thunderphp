<?php
namespace ActivityLog;

use \Model\Model;

defined('ROOT') or die("Direct script access denied");

class ActivityLog extends Model {
    protected $table = 'activity_log';
    public $primary_key = 'id';
    public $order_column = 'id';

    protected $allowedColumns = [
        'user_id',
        'username',
        'action',
        'entity_type',
        'ip_address',
        'date_created',
    ];

    protected $allowedUpdateColumns = [];
}
