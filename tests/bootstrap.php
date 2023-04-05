<?php

namespace Tests;

use Carbon\Carbon;
use function date;

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
$db->addDetection(Carbon::now()->subSeconds(90), 'Otus senegalensis', 'African Scops-Owl', 0.85);
$db->addDetection(Carbon::now()->subSeconds(60), 'Cuculus solitarius', 'Red-chested Cuckoo', 0.92);
$db->addDetection(Carbon::now()->subSeconds(30), 'Myrmecocichla monticola', 'Mountain Wheatear', 0.80);
$db->addDetection(Carbon::now()->subSeconds(15), 'Strix woodfordii', 'African Wood-Owl', 0.88);
