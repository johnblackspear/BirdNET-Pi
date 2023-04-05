<?php

namespace Tests\Utils;

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


    public function seedTestData()
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

        $stmt->bindValue(':detectionDate', date('Y-m-d'));
        $stmt->bindValue(':detectionTime', date('H:i:s'));
        $stmt->bindValue(':scientificName', 'Otus senegalensis');
        $stmt->bindValue(':commonName', 'African Scops-Owl');
        $stmt->bindValue(':confidence', 0.87094456);
        $stmt->bindValue(':latitude', -24.996);
        $stmt->bindValue(':longitude', 31.5919);
        $stmt->bindValue(':cutoff', 0.7);
        $stmt->bindValue(':week', intval(date('W')));
        $stmt->bindValue(':sensitivity', 1.25);
        $stmt->bindValue(':overlap', 0);
        $stmt->bindValue(':recordingFilename', 'African_Scops-Owl-87-2023-03-10-birdnet-18:14:17.mp3');

        $stmt->execute();
    }
}
