<?php declare(strict_types=1);

namespace BasicTest;
use SiteStudio\Package\ComparePackage;
use PHPUnit\Framework\TestCase;

final class BasicTest extends TestCase {

    private $path;
    private $source;
    private $target;
    private $generated_report;
    private $target_report;
    
    protected function setUp(): void {
        parent::setUp();
        $this->path = dirname(__FILE__);
        $this->source = $this->path . '/../packages/basic/source.yml';
        $this->target = $this->path . '/../packages/basic/target.yml';
        $this->generated_report = $this->path . '/../results/basic/generated.csv';
        $this->target_report = $this->path . '/../results/basic/target.csv';
    }

    public function testTargetExists(): void {
        $this->assertFileExists( 
            $this->target_report, 
            "Basic report $this->target_report target exists"
        );
    }

    public function testCompareArray(): void {
        $compare = new ComparePackage($this->source, $this->target);
        $diff = $compare->diffToArray();        
        $package = basename($this->target);
        
        $this->assertNotEmpty(
            $diff[$package]['overrides'],
            "diff array is not empty."
        );
        $this->assertNotEmpty(
            $diff[$package]['insertions'],
            "diff array is not empty."
        );
    }

    public function testComparePackagesReportExists(): void { 
        if (file_exists($this->generated_report)) unlink($this->generated_report);

        $compare = new ComparePackage($this->source, $this->target);
        $compare->diffToCSV($this->generated_report);

        $this->assertFileExists( 
            $this->generated_report, 
            "Basic report $this->generated_report exists."
        );
    } 

    public function testComparePackagesReportIdentical(): void { 
        $identical = false;
        if (filesize($this->generated_report) == filesize($this->target_report)
            && md5_file($this->generated_report) == md5_file($this->target_report)) {
            $identical = true;
        }
        $this->assertTrue(
            $identical,
            "Basic target and generated reports are identical."
        );
    }

    protected function tearDown(): void {
        $this->path = null;
        $this->generated_report = null;
        $this->target_report = null;
        $this->source = null;
        $this->target = null;
        parent::tearDown();
    }

}
