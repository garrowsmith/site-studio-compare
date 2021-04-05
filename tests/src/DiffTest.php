<?php declare(strict_types=1);

namespace DiffTest;
use SiteStudio\Package\ComparePackage;
use Swaggest\JsonDiff\JsonDiff;
use PHPUnit\Framework\TestCase;

/*
*  ./vendor/bin/phpunit tests/src/DiffTest.php
*/
final class DiffTest extends TestCase {

    private $path;
    private $source;
    private $target;
    private $report;
    private $diff_report;

    protected function setUp(): void {
        parent::setUp();
        $this->path = dirname(__FILE__);
        $this->report = $this->path . '/../results/diff/generated.report.csv';
        $this->diff_report = $this->path . '/../results/diff/generated.diff.csv';
    }
  
    public function testPackageDiff(): void {
        if (file_exists($this->report)) unlink($this->report);
        if (file_exists($this->diff_report)) unlink($this->diff_report);

        $source = $this->path . '/../packages/complex/mds.2.6.0.package.yml';
        $target = $this->path . '/../packages/complex/brand-styles-1.0.7.yml';
        
        $compare = new ComparePackage($source, $target);
        $compare->diffToCSV($this->report);

        $compare->setSource($source);
        $result = array();

        foreach ($compare->diffToArray() as $package_path => $diff) {

            $compare->setTarget($package_path);
            foreach ($diff['overrides'] as $uuid => $item) {
                $target_entity = $compare->inspect($uuid, $compare->targetArray);
                $target_json = json_decode($target_entity['export']['json_values']);
                $source_entity = $compare->inspect($uuid, $compare->sourceArray);
                $source_json = json_decode($source_entity['export']['json_values']);

                $diff = new JsonDiff(
                    $source_json, 
                    $target_json,
                    JsonDiff::TOLERATE_ASSOCIATIVE_ARRAYS
                );
                if ($diff->getDiffCnt() > 0 ){
                    $row = array($uuid, $target_entity['export']['label'], $diff->getDiffCnt());
                    $result[] = $row;
                }
            }
        }

        $compare->fputDiffCSV($this->diff_report, $result, $fparams = "a");

        $expectedDiffs = 23;
        $this->assertCount(
            $expectedDiffs,
            $result, "Total diffs are not exactly 23 items"
        );

        $report_exists = file_exists($this->report);
        $this->assertNotNull(
            $report_exists,
            "Diff Item Report exists."
        );
        
        $diff_report_exists = file_exists($this->diff_report);
        $this->assertNotNull(
            $diff_report_exists,
            "Diff Report exists."
        );        

    }

    protected function tearDown(): void {
        $this->path = null;
        $this->source = null; 
        $this->target = null;
        $this->report = null;        
        $this->diff_report = null; 
        parent::tearDown();
    }

}
