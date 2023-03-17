<?php

namespace Test;

use Carbon\Carbon;
use Exception;
use PHPUnit\Framework\TestCase;

final class OverviewTest extends TestCase
{
    private static $SCRIPT = 'scripts/overview.php';

    public function setUp(): void
    {
        // reset all the dodgy superglobal stuff we are doing
        // to make the scripts run
        $_GET = [];
    }

    public function loadFixture($fullyQualifiedFilename)
    {

        $result = file_get_contents($fullyQualifiedFilename);
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
        require(dirname(__FILE__) . "/../${fileName}");
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
        $date = date('Y-m-d');
        $this->assertSame("Combo-$date.png", $output);
    }

    /** @test */
    public function it_prints_a_table_of_detections_when_ajax_left_chart_is_true()
    {
        $_GET['ajax_left_chart'] = 'true';
        $output = $this->runScript(self::$SCRIPT);
        $this->assertSame("going to fail", $output);
    }
}
