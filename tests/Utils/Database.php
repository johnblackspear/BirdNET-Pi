<?php

namespace Tests\Utils;

use Carbon\Carbon;
use SQLite3;

class Database
{
    private SQLite3 $db;

    public function __construct($db)
    {
        $this->db = $db;
    }

    public function dropTable(string $tableName = 'detections'): bool
    {
        $drop = "DROP TABLE IF EXISTS $tableName";
        return $this->db->exec($drop);
    }

    public function createTable()
    {
        return $this->db->exec('CREATE TABLE IF NOT EXISTS detections ( Date DATE, Time TIME, Sci_Name VARCHAR(100) NOT NULL, Com_Name VARCHAR(100) NOT NULL, Confidence FLOAT, Lat FLOAT, Lon FLOAT, Cutoff FLOAT, Week INT, Sens FLOAT, Overlap FLOAT, File_Name VARCHAR(100) NOT NULL)');
    }

    public function addDetection(
        Carbon $datetime = null,
        string $scientificName = 'Otus senegalensis',
        string $commonName = 'African Scops-Owl',
        float $confidence = 0.87
    )
    {
        if ($datetime == null) {
            $datetime = Carbon::now();
        }

        $this->insertDetectionRow($datetime, $scientificName, $commonName, $confidence);
    }

    private function insertDetectionRow(
        Carbon $dateTime,
        string $scientificName,
        string $commonName,
        float  $confidence
    )
    {
        // `Date`, `Time`, Sci_Name, Com_Name, Confidence, Lat, Lon, Cutoff, Week, Sens, Overlap, File_Name
        $stmt = $this->db->prepare(
            'INSERT INTO detections  ' .
            'VALUES (' .
            ':detectionDate, ' .
            ':detectionTime, ' .
            ':scientificName, ' .
            ':commonName, ' .
            ':confidence, ' .
            ':latitude, ' .
            ':longitude, ' .
            ':cutoff, ' .
            ':week, ' .
            ':sensitivity, ' .
            ':overlap, ' .
            ':recordingFilename ' .
            ')'
        );

        $stmt->bindValue(':detectionDate', $dateTime->toDateString());
        $stmt->bindValue(':detectionTime', $dateTime->toTimeString());
        $stmt->bindValue(':scientificName', $scientificName);
        $stmt->bindValue(':commonName', $commonName);
        $stmt->bindValue(':confidence', $confidence);
        $stmt->bindValue(':latitude', -24.996);
        $stmt->bindValue(':longitude', 31.5919);
        $stmt->bindValue(':cutoff', 0.7);
        $stmt->bindValue(':week', $dateTime->isoWeeksInYear());
        $stmt->bindValue(':sensitivity', 1.25);
        $stmt->bindValue(':overlap', 0);
        $stmt->bindValue(':recordingFilename', $this->recordingFileName($commonName, $confidence, $dateTime));

        $stmt->execute();
    }

    private function recordingFileName(string $commonName, $confidence, Carbon $dateTime): string
    {
        $date = $dateTime->toDateString();
        $time = $dateTime->toTimeString();
        $confidencePercent = round($confidence * 100,0);
        return preg_replace('/\s+/', '_', $commonName) .
            "-$confidencePercent-$date-birdnet-$time.mp3";
    }
}
