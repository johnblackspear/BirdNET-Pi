<?php

namespace Birds;

use PHPUnit\Framework\TestCase;
use SQLite3;

function date(string $format) {
    return '2023-04-15';
}

final class OverviewTest extends TestCase
{
    private static $SCRIPT = 'scripts/overview.php';
    private SQLite3 $db;

    public function setUp(): void
    {
        // reset all the dodgy superglobal stuff we are doing
        // to make the scripts run
        $_GET = [];
        $this->db = new SQLite3('/Users/vehikl/Code/growth-sessions/BirdNET-Pi/scripts/birds.db', SQLITE3_OPEN_CREATE | SQLITE3_OPEN_READWRITE);
    }

    public function loadFixture($file)
    {

        $result = file_get_contents($file);
        if ($result === false) {
            throw 'Could not load file';
        }

        return $result;
    }

    public function rewriteSpectogramNoCacheValue($html)
    {
        return preg_replace(
            '/<img id="spectrogramimage" src="\/spectrogram.png\?nocache=\d+">/',
            '<img id="spectrogramimage" src="/spectrogram.png\?nocache=fixed_value">',
            $html
        );
    }

    public function runScript($fileName)
    {
        ob_start();
        require(dirname(__FILE__) . "/../{$fileName}");
        $scriptOutput = $this->rewriteSpectogramNoCacheValue(ob_get_contents());
        ob_end_clean();
        return $scriptOutput;
    }

    /** @test */
    public function it_returns_the_overview_page()
    {
        $expected = $this->rewriteSpectogramNoCacheValue(
            $this->loadFixture(dirname(__FILE__) . '/fixtures/overview.html')
        );
        $this->assertSame($expected, $this->runScript(self::$SCRIPT));
    }

    /** @test */
    public function it_prints_a_chart_url_when_fetch_chart_string_is_true()
    {
        $_GET['fetch_chart_string'] = 'true';
        $output = $this->runScript(self::$SCRIPT);
        $this->assertSame("Combo-2023-04-15.png", $output);
    }

    /** @test */
    public function it_prints_a_table_of_detections_when_ajax_left_chart_is_true()
    {
        // TODO
        $_GET['ajax_left_chart'] = 'true';
        $expected = $this->loadFixture(dirname(__FILE__) . '/fixtures/detections.html');
        $output = $this->runScript(self::$SCRIPT);
        $this->assertSame($expected, $output);
    }

    /** @test */
    public function it_prints_detections_if_ajax_detections_is_set()
    {
        $_GET['ajax_detections'] = 'true';
        $_GET['previous_detection_identifier'] = 'true';
        $expected = '<h3>No Detections For Today.</h3>';
        $output = $this->runScript(self::$SCRIPT);
        $this->assertSame($expected, $output);
    }

    /** @test  */
    public function it_prints_detections_if_there_are_any()
    {
        self::markTestSkipped();
        $_GET['ajax_detections'] = 'true';
        $_GET['previous_detection_identifier'] = 'true';
        // add some detections for today to the database


        // interact with our db instance
//        $statement4 = $this->db->prepare("INSERT INTO detections (Com_Name, Sci_Name, `Date`, `Time`, Confidence, File_Name) values ('tacos', 'pizza', NOW(), NOW(), 0.75, 'turdus.mp3')");
//        $statement4 = $this->db->prepare("INSERT INTO detections(Com_Name, Sci_Name, `Date`, `Time`, Confidence, File_Name) VALUES ('tacos', 'pizza', '2023-03-23', '16:55', 0.75, 'turdus.mp3')");
//        $statement4->execute();
        $expected = '<h3>Detections For Today.</h3>';
        $output = $this->runScript(self::$SCRIPT);
        $this->assertSame($expected, $output);
    }
}
