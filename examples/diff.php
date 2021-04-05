<?php

require '../vendor/autoload.php';

use SiteStudio\Package\ComparePackage;
use Swaggest\JsonDiff\JsonDiff;

$path = __DIR__;
$source = $path . '/packages/single-source.yml';
$target = $path . '/packages/single-target.yml';

$compare = new ComparePackage($source, $target);
$compare->setSource($source);

foreach ($compare->diffToArray() as $package_path => $diff) {
    // store the target package in memory
    $compare->setTarget($package_path);

    // loop through all cases where source is overriden
    foreach ($diff['overrides'] as $uuid => $item) {
        // get source package JSON field
        $source_entity = $compare->inspect($uuid, $compare->sourceArray);
        $source_json = json_decode($source_entity['export']['json_values']);

        // get target package JSON field, custom components or styles
        $target_entity = $compare->inspect($uuid, $compare->targetArray);
        $target_json = json_decode($target_entity['export']['json_values']);
        
        // See https://github.com/swaggest/json-diff#library-usage
        $diff = new JsonDiff(
            $source_json, 
            $target_json,
            JsonDiff::TOLERATE_ASSOCIATIVE_ARRAYS
        );

        if ($diff->getDiffCnt() > 0 ){
            echo $uuid . "  '" . $target_entity['export']['label'] . "' - Total: " . $diff->getDiffCnt() . "\n";
            if ($diff->getDiffCnt() < 10 ){
                // getPatch() returns a JSON Patch, see http://jsonpatch.com/
                echo json_encode($diff->getPatch(), JSON_PRETTY_PRINT);
            }
        }
    }
}
