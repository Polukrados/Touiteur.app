<?php
require_once('vendor/autoload.php');

use iutnc\admin\dispatch as dispatch;
use iutnc\admin\db as db;

session_start();
db\ConnectionFactoryAdmin::setConfig('src/admin/db/db.config.ini');

$dispatcher = new dispatch\DispatcherAdmin();
$dispatcher->run();
