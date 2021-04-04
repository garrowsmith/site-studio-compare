<?php

namespace SiteStudio\Package;
use Symfony\Component\Yaml\Yaml;
use SiteStudio\Package\ArrayDiff;

class CompareYaml {

    private $source;
    private $target;
    private $report;

    private $diff = array();
    private $diffCount = 0;

    private $options = 0;
    private $ignoredEntityTypes;
    private $compareKeys = array();

    public function __construct($source, $target, $options = 0) {
        $this->source = $source;
        $this->target = $target;
        $this->options = $options;
        $this->ignoredEntityTypes = array(
            'cohesion_sync_package',
            'file',
            'cohesion_style_guide_manager',
            'cohesion_style_guide',
            'image_style',
            'cohesion_component_category',
        );
        $this->compareKeys = array(
            'type',
            'uuid',
            'id', 
            'label',
        );
        $this->report_filename = 'changeme';
        $this->process($source, $target);
    }

    /**
     * @param string $source path to source packages
     * @param mixed $targets single or array of paths to package(s)
     */
    private function process($source, $targets) {
        if (!is_array($targets)) {
            $filename = basename($targets);
            $this->diff[$filename] = CompareYaml::comparePackages($source, $targets);    
        } 
        if (is_array($targets)) {
            foreach($targets as $target) {
                $filename = basename($target);
                $this->diff[$filename] = CompareYaml::comparePackages($source, $target);
            }
        }
    }

    /**
     * Prefix array with an index column and write array to csv file (append)
     *
     * @param string $source source yaml file to compare
     * @param string $target target yaml file to compare
     * @param string $keys override the keys used for diff comparison
     * @param string $keys an array of keys to be compared
     * 
     * @return array[] array with keys 'overrides', 'insertions' and 'deletions'
     */
    private function comparePackages($source, $target, $keys = null) {
        $keys = $keys ? $keys : $this->compareKeys;
        
        $sourceYAML = CompareYaml::collateSSYaml($source);
        $comparisonYAML = CompareYaml::collateSSYaml($target);
        $diff = ArrayDiff::arrayDifference($sourceYAML, $comparisonYAML, $keys);
        // an override is everything that hasn't been 'insterted'
        $diff['overrides'] = array_diff_key($comparisonYAML, $diff['insertions']);
        CompareYaml::classifyDiff('overrides', 'overrides', $diff, $target);
        // "customisations" are "insertions"
        CompareYaml::classifyDiff('custom', 'insertions', $diff, $target);

        return $diff;
    }

    /**
     * Classifies aeach row and attributes source package
     *
     * @param string $tag classify the type of diff
     * @param string $key keyed on the package path
     * @param array $data the diff array
     * @param string $target attribute the change to a package 
     * 
     * @return array[] array with keys 'overrides', 'insertions' and 'deletions'
     */    
    private function classifyDiff($tag, $key, &$data, $target) {
        $target_filename = basename($target);
        foreach ($data[$key] as &$row) {
            $row = array('idx' => $tag) + $row;
            $row = $row + array('source' => $target_filename);
            $this->diffCount++;
        }
    }

    /**
     * Compare two arrays and return a list of items only in array1 (deletions) and only in array2 (insertions)
     * 
     * @param string $path The file path to a Site Studio yaml package
     * @param string $ignoredEntityTypes Site Studio entities to be ignored 
     */
    private function collateSSYaml($path, $ignoredEntityTypes = null) {
        $ignoredEntityTypes = $ignoredEntityTypes ? $ignoredEntityTypes : $this->ignoredEntityTypes;
        $package = Yaml::parse(file_get_contents($path));    
        $config_collection = array();

        foreach ($package as $source => $config_item) {
            if (in_array($config_item['type'], $ignoredEntityTypes)) continue;
            $item['type'] = $config_item['type'];
            $item['uuid'] = $config_item['export']['uuid'];
            $item['id'] = $config_item['export']['id'];
            $item['label'] = $config_item['export']['label'];
            $config_collection[$item['uuid']] = $item;
        }

        unset($package);

        return $config_collection;
    }

    /**
     * Prefix array with an index column and write array to csv file (append)
     *
     * @param string $destination csv
    */
    public function diffToCSV($destination = null) {
        $destination = $destination ? $destination : $this->report;
        foreach ($this->diff as &$target) {
            CompareYaml::fputDiffCSV($destination, $target['overrides']);
            CompareYaml::fputDiffCSV($destination, $target['insertions']);
        }
    }

    /**
     * Returns diff as PHP Array
     * 
     * @return array[] array with keys for each source, 'overrides', 'insertions' and 'deletions'
    */
    public function diffToArray() {
        return $this->diff;
    }

    /**
     * Returns diff as JSON
     *
     * @return JSON  
    */
    public function diffToJSON() {
        return json_encode($this->diff);
    }

   /**
     * Prefix array with an index column and write array to csv file (append)
     *
     * @param string $index type of diff/change
     * @param string $source_path the source package
     * @param string $file_destination destination file file
     * @param array $data 
    */
    private function fputDiffCSV($file_destination, $data, $fparams = "a") {
        $handle = fopen($file_destination, $fparams);
        foreach ($data as $row) {
            fputcsv($handle, $row);
        }
        fclose($handle);
    }    

}