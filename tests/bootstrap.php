<?php
namespace Tests;
$rootDir = rtrim(dirname(__DIR__ . '../'), '/') . '/';
require "$rootDir/vendor/autoload.php";

$dbFilename = $rootDir . $_ENV['DB_FILE'];
echo "Loading DB: $dbFilename\n";
$db = new Utils\Database(new \SQLite3(
    $dbFilename,
    SQLITE3_OPEN_CREATE | SQLITE3_OPEN_READWRITE
));

// Setup clean DB for testing
$db->dropTable('detections');
$db->createTable();
$db->seedTestData();
