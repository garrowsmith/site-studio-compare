# site-studio-compare

## Example Usage

```
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

## Example Usage