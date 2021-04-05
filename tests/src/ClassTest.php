<?php declare(strict_types=1);

namespace ClassTest;
use SiteStudio\Package\ComparePackage;
use PHPUnit\Framework\TestCase;

/*
*  ./vendor/bin/phpunit tests/src/ClassTest.php --filter testCompareClass
*/
final class ClassTest extends TestCase {

    private $path;
    private $basic_source;
    private $basic_target;
    private $basic_report;

    private $complex_source;
    private $complex_single_target;
    private $complex_single_report;
    private $complex_multi_target;
    private $complex_multi_report;

    protected function setUp(): void {
        parent::setUp();
        $this->path = dirname(__FILE__);
        $this->basic_source = $this->path . '/../packages/basic/source.yml';
        $this->basic_target = $this->path . '/../packages/basic/target.yml';
        $this->basic_report = $this->path . '/../results/class/basic.generated.csv';

        $this->complex_source = $this->path . '/../packages/complex/mds.2.6.0.package.yml';
        $this->complex_single_target = $this->path . '/../packages/complex/brand-1.0.4.yml';
        $this->complex_multi_target = array(
            $this->complex_single_target,
            $this->path . '/../packages/complex/brand-styles-1.0.7.yml',
        );
        $this->complex_single_report = $this->path . '/../results/class/complex.single.generated.csv';
        $this->complex_multi_report = $this->path . '/../results/class/complex.multiple.generated.csv';
    }
  
    public function testCompareBasicPackageToArray(): void {
        $compare = new ComparePackage($this->basic_source, $this->basic_target);
        $diff_arr = $compare->diffToArray();
        $this->assertNotEmpty(
            $diff_arr[$this->basic_target]['overrides'],
            "diff array is not empty."
        );
        $this->assertNotEmpty(
            $diff_arr[$this->basic_target]['insertions'],
            "diff array is not empty."
        ); 
    }

    public function testCompareBasicPackageToJSON(): void {
        $compare = new ComparePackage($this->basic_source, $this->basic_target);
        $json = $compare->diffToJSON();
        $this->assertNotNull(
            $json,
            "JSON is not null."
        );
    }

    public function testCompareBasicPackageToCSV(): void {
        if (file_exists($this->basic_report)) unlink($this->basic_report);

        $compare = new ComparePackage($this->basic_source, $this->basic_target);
        $compare->diffToCSV($this->basic_report);

        $report_exists = file_exists($this->basic_report);
        $this->assertNotNull(
            $report_exists,
            "Basic CSV Report exists."
        );
    }

    public function testCompareSingleComplexPackageToCSV(): void {
        if (file_exists($this->complex_single_report)) unlink($this->complex_single_report);

        $compare = new ComparePackage($this->complex_source, $this->complex_single_target);
        $compare->diffToCSV($this->complex_single_report);
        
        $report_exists = file_exists($this->complex_single_report);
        $this->assertNotNull(
            $report_exists,
            "Complex CSV Report for single package exists."
        );
    }

    public function testCompareMultipleComplexPackageToCSV(): void {
        if (file_exists($this->complex_multi_report)) unlink($this->complex_multi_report);

        $compare = new ComparePackage($this->complex_source, $this->complex_multi_target);
        $compare->diffToCSV($this->complex_multi_report);
        
        $report_exists = file_exists($this->complex_multi_report);
        $this->assertNotNull(
            $report_exists,
            "Complex CSV Report for multiple packages exists."
        );
    }    

    protected function tearDown(): void {
        $this->path = null;
        $this->source = null;
        $this->target = null;
        $this->report = null;
        
        $this->basic_source = null;
        $this->basic_target = null;
        $this->basic_report = null;
    
        $this->complex_source = null;
        $this->complex_single_target = null;
        $this->complex_single_report = null;
        $this->complex_multi_target = null;
        $this->complex_multi_report = null;

        parent::tearDown();
    }

}
