# site-studio-compare

This project provides tools to:
* Identify a high level diff - deletions and overrides between a source and target package.
* Output a high level diff to PHP array, JSON or CSV.
* Compare package JSON fields stored as strings, by wrapping JSONdiff https://github.com/swaggest/json-diff#library-usage

## Installation

Add the following to the repositories section of your project's composer.json:

```
"site-studio-tools": {
    "type": "vcs",
    "url": "https://github.com/garrowsmith/site-studio-compare.git"
}
```

Require the dependency via Composer:

```
composer require garrowsmith/site-studio-compare
```

## Package Setup

This project relies on real packages and are excluded:

```
    tests/packages/complex/mds.2.6.0.package.yml
    tests/packages/complex/brand-1.0.4.yml
    tests/packages/complex/brand-styles-1.0.7.yml
```

## Example Usage

### Example usage: JSON diff

See `./examples/diff.php` folder for a usage example where field value of `json_values` is compared.

### Example usage: multiple packages

```
<?php

use SiteStudio\Package\ComparePackage;

$source = 'mds.2.6.0.package.yml';
$targets = array(
    'brand.1.0.14.package.yml',
    'brand-styles.1.0.1.package.yml',
);

$compare = new ComparePackage($source, $targets);
$compare->diffToCSV('../path/to/report.csv');

$diff = $compare->diffToArray();
$json = $compare->diffToJSON();

```

### Example usage: single package

```
<?php

use SiteStudio\Package\ComparePackage;

$source = 'mds.2.6.0.package.yml';
$target = 'brand.1.0.14.package.yml';

$compare = new ComparePackage($source, $target);
$compare->diffToCSV('../path/to/report.csv');

```

## Structure of the diff Array 

```
$diff["/path/to/target.package.yml"] => {
    ["insertions"]=> {...}
    ["deletions"]=> {...}
    ["overrides"] => {
        ["21548607-8efc-490c-aa9b-2d3694399aad] => {
            ["idx"]=> "overrides"
            ["type"]=> "cohesion_menu_templates"
            ["uuid"]=> "21548607-8efc-490c-aa9b-2d3694399aad"
            ["id"]=> "menu_tpl_main_navigation"
            ["label"]=> "Burger menu template"
            ["package"]=> "single-target-styles.yml"
            ["path"]=> "/some/path/packages/single-target-styles.yml"
    }
}

```

### Reference YAML Structure

Reference Site Studio Package structure (6.3.x, 6.4.x):

```
-
    type: cohesion_component
    export:
        langcode: en
        status: true
        dependencies: {  }
        id: cpt_machine_name
        label: 'Title of component'
        json_values: '{}'
        json_mapper: '{}'
        last_entity_update: entityupdate_0099
        locked: false
        modified: true
        selectable: true
    ...    
*/
```

## PHPUnit Tests

```
./vendor/bin/phpunit tests
```
