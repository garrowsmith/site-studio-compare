# site-studio-compare

A PHP implementation for comparing two or more Site Studio Packages:

* high level diff - deletions and overrides between a source and target package.
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

## Example Usage
In order to generate the report - one has to provide access token to access the Repo API (Gitlab is only supported for now.)

* The tool accepts arguments naming:
* Source Repo
* Source Tag or branch
* Target Repo
* Target Repo or branch

Upon Running the script - The user will be asked to:
* Enter the Access Token

### Basic usage: CSV report

See [site-studio-compare/examples/compareRepo.php] for a working example using mock packages.

```
...
$compare = new ComparePackageRepo();
```


