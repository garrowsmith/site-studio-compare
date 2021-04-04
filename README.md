# site-studio-compare

## Example Usage

```
<?php

use SiteStudio\Package\ComparePackage;

$source = 'mds.2.6.0.package.yml';
$targets = array(
    'brand.1.0.14.package.yml',
    'brand-styles.1.0.1.package.yml',
);
$compare = new ComparePackage($source, $targets);

// Usage
$diff = $compare->diffToArray();
$json = $compare->diffToArray();
$compare->diffToCSV('../path/to/report.csv');
```

## PHPUnit Tests

```
./vendor/bin/phpunit tests
```