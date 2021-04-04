<?php declare(strict_types=1);

namespace ClassTest;
use SiteStudio\Package\ComparePackage;
use PHPUnit\Framework\TestCase;

/*
*  ./vendor/bin/phpunit tests/src/ClassTest.php --filter testCompareClass
*/
final class ClassTest extends TestCase {

    private $path;
    private $source;
    private $target;
    private $report;

    protected function setUp(): void {
        parent::setUp();
        $this->path = dirname(__FILE__);
        $this->source = $this->path . '/../packages/basic/source.yml';
        $this->target = $this->path . '/../packages/basic/target.yml';
        $this->report = $this->path . '/../results/class/basic.generated.csv';
    }
  
    public function testCompareBasicClass(): void {
        $compare = new ComparePackage($this->source, $this->target);
        $diff_arr = $compare->diffToArray();
        $key = basename($this->target);

        $this->assertNotEmpty(
            $diff_arr[$key]['overrides'],
            "diff array is not empty."
        );
        $this->assertNotEmpty(
            $diff_arr[$key]['insertions'],
            "diff array is not empty."
        ); 
    }

    public function testCompareBasicToJSON(): void {
        $compare = new ComparePackage($this->source, $this->target);
        $json = $compare->diffToJSON();
        $this->assertNotNull(
            $json,
            "JSON is not null."
        );
    }

    public function testCompareBasicToCSV(): void {
        // remove previous test runs
        if (file_exists($this->report)) unlink($this->report);

        $compare = new ComparePackage($this->source, $this->target);
        $compare->diffToCSV($this->report);

        $report_exists = file_exists($this->report);
        $this->assertNotNull(
            $report_exists,
            "Basic Report exists."
        );
    }

    public function testCompareSingleComplexToCSV(): void {
        $path = $this->path . '/../packages/class/';
        $source = $path . '2.6.0.package.yml';
        $target = $path . 'brand-1.0.4.yml';
        $this->report = $this->path . '/../results/class/complex.single.generated.csv';
        // remove previous test runs
        if (file_exists($this->report)) unlink($this->report);

        $compare = new ComparePackage($source, $target);
        $compare->diffToCSV($this->report);

        $report_exists = file_exists($this->report);
        $this->assertNotNull(
            $report_exists,
            "Complex Report exists."
        );
    }

    public function testCompareMultipleComplexToCSV(): void {
        $path = $this->path . '/../packages/class/';
        $source = $path . '2.6.0.package.yml';
        $target = array(
            $path . 'brand-1.0.4.yml',
            $path . 'brand-styles-1.0.7.yml'
        );
        $this->report = $this->path . '/../results/class/complex.multiple.generated.csv';

        if (file_exists($this->report)) unlink($this->report);

        $compare = new ComparePackage($source, $target);
        $compare->diffToCSV($this->report);

        $report_exists = file_exists($this->report);
        $this->assertNotNull(
            $report_exists,
            "Complex Report exists."
        );
    }    

    protected function tearDown(): void {
        $this->path = null;
        $this->target = null;
        $this->source = null;        
        $this->report = null;        
        parent::tearDown();
    }

}
