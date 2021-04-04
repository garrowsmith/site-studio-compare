<?php declare(strict_types=1);

namespace BasicTest;
use SiteStudio\Package\ComparePackage;
use PHPUnit\Framework\TestCase;

final class BasicTest extends TestCase {

    private $path;
    private $report;
    private $target;
    
    protected function setUp(): void {
        parent::setUp();
        $this->path = dirname(__FILE__);
        $this->report = $this->path . '/../results/basic/generated.csv';
        $this->target = $this->path . '/../results/basic/target.csv';
    }

    public function testTargetExists(): void {
        $this->assertFileExists( 
            $this->target, 
            "Basic report $this->report target exists"
        ); 
    }    

    public function testComparePackages(): void {
        $source = $this->path . '/../packages/basic/source.yml';
        $target = $this->path . '/../packages/basic/target.yml';
        $keys = array(
            'cohesion_sync_package',
            'file',
            'cohesion_style_guide_manager',
            'cohesion_style_guide',
            'image_style',
            'cohesion_component_category',
        );
        $diff = ComparePackage::compare($source, $target, $keys);

        if (file_exists($this->report)) unlink($this->report);
        ComparePackage::fputDiff('overrides', $target, $this->report, $diff[$target]['overrides']);
        ComparePackage::fputDiff('custom', $target, $this->report, $diff[$target]['insertions']);

        $this->assertNotEmpty(
            $diff[$target]['overrides'],
            "diff array is not empty."
        );
        $this->assertNotEmpty(
            $diff[$target]['insertions'],
            "diff array is not empty."
        );        
    }

    public function testComparePackagesReportExists(): void { 
        $this->assertFileExists( 
            $this->report, 
            "Basic report $this->report exists."
        );
    } 

    public function testComparePackagesReportIdentical(): void { 
        $identical = false;
        if (filesize($this->report) == filesize($this->target)
            && md5_file($this->report) == md5_file($this->target)) {
            $identical = true;
        }
        $this->assertTrue(
            $identical,
            "Reports are identical."
        );
    }

    protected function tearDown(): void {
        $this->path = null;
        $this->report = null;
        $this->target = null;
        parent::tearDown();
    }

}
