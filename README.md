# site-studio-compare

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

## Example usage: multiple packages

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

## Example usage: single package

```
<?php

use SiteStudio\Package\ComparePackage;

$source = 'mds.2.6.0.package.yml';
$target = 'brand.1.0.14.package.yml';

$compare = new ComparePackage($source, $target);
$compare->diffToCSV('../path/to/report.csv');
```

## PHPUnit Tests

```
./vendor/bin/phpunit tests
```

### YAML Structure
Reference Site Studio Package structure.

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
