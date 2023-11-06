<?php

require_once('vendor/autoload.php');

use iutnc\touiteur\dispatch as dispatch;
use iutnc\touiteur\db as db;

session_start();
db\ConnectionFactory::setConfig('src/classes/db/db.config.ini');

$dispatcher = new dispatch\Dispatcher();
$dispatcher->run();