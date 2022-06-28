<?php

namespace subconverter;

use PDO;

$dbConfig = require 'database.php';
extract($dbConfig);
$tpl = '%s:host=%s;dbname=%s;port=%s;charset=%s';
$dsn = sprintf($tpl, $type, $host, $dbname, $port, $charset);
$db = new PDO($dsn, $username, $password);
$db->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);