<?php

require '../vendor/autoload.php';

use SiteStudio\Package\ComparePackage;
use Swaggest\JsonDiff\JsonDiff;

$path = __DIR__;
$source = $path . '/packages/single-source.yml';
$targets = array(
    $path . '/packages/single-target.yml',
    $path . '/packages/single-target-styles.yml',
);

$compare = new ComparePackage($source, $targets);
$report_path = './report/compare-single-packages.csv';

// remove existing comparison report
if (file_exists($report_path)) unlink($report_path);

echo "Generating CSV comparison report $report_path";
$compare->diffToCSV($report_path);

echo "Dumping the diff array";
var_dump($compare->diffToArray());