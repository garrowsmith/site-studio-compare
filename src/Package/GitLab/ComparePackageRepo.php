<?php

namespace SiteStudio\Package\GitLab;

use Symfony\Component\Yaml\Yaml;
use SiteStudio\Package\ArrayDiff;
use SiteStudio\Package\GitLab\ConfigInitializer;
use SiteStudio\Package\ComparePackage;
use SiteStudio\Package\ComparePackageRepoInterface;

class ComparePackageRepo extends ConfigInitializer implements ComparePackageRepoInterface {

  /**
   * ConfigInitializer constructor.
   */
  public function __construct() {
    parent::__construct();
    $this->yamlDiffReport();
  }

  /**
   * Yaml Difference Report.
   */
  public function yamlDiffReport() {
    // Get Projects List based on Repo and access token.
    $project_details = $this->getProjectsList();

    // Now that we have received user choice to compare packages.
    // Download repo with respective tag.
    $package_names = $this->downloadRepoFiles($project_details);

    // Trigger Compare function.
    if (!empty($package_names) && count($package_names) == 2) {
        $this->invokeCompareRepo($package_names);
    }
  }

  /**
   * Pull Projects based on User choice of Repo and Group.
   */
  public function getProjectsList() {
    $project_details = [];

    // Remove old Entries.
    if (is_dir($this->comparisonFolder)) {
      $remove_directory = "rm -r $this->comparisonFolder";
      shell_exec($remove_directory);
    }

    // Create comparison folder.
    $create_directory = "mkdir -p $this->comparisonFolder";
    shell_exec($create_directory);

    $this->accessToken = trim(readline('Please Enter Private Access token for Package Management Repo'));
    $list_of_projects_cmd = "curl --header 'PRIVATE-TOKEN: $this->accessToken' '$this->gitlabApiPath/groups/$this->packageRepoGroup/projects?per_page=500' >> ./$this->comparisonFolder/$this->projectsPath";
    shell_exec($list_of_projects_cmd);

    $projects_data = file_get_contents("$this->comparisonFolder/$this->projectsPath");
    $projects_info = json_decode($projects_data, TRUE);

    if (!empty($projects_info)) {
      foreach ($projects_info as $project_info) {
        $project_names[] = $project_info['name'];
        $project_details[$project_info['name']] = $project_info;
      }
    }

    return $project_details;
  }

  /**
   * Gitlab Repo File Download.
   */
  public function downloadRepoFiles($project_details) {
    $compare_packages = [
      'source' => [
        $this->srcPackageRepo => $this->srcBranchTag,
      ],
      'target' => [
        $this->tarPackageRepo => $this->tarBranchTag,
      ],
    ];

    $package_names = [];
    foreach ($compare_packages as $package_type => $package_details) {
      foreach ($package_details as $package_repo => $branch_or_tag) {
        $package_name = "$this->comparisonFolder/$package_repo-$branch_or_tag.yml";
        $project_id = $project_details[$package_repo]['id'];
        $download_command = "curl --header 'PRIVATE-TOKEN: $this->accessToken' '$this->gitlabApiPath/projects/$project_id/repository/files/package.yml/raw?ref=$branch_or_tag' >> ./$package_name";
        shell_exec($download_command);
        $package_names[$package_type] = $package_name;
      }
    }

    return $package_names;
  }

  /**
   * Invoke Site studio Compare tool.
   */
  public function invokeCompareRepo($package_names) {
    $compare = new ComparePackage($package_names['source'], $package_names['target']);
    $report_path = "$this->comparisonFolder/compare-packages.csv";

    // Remove existing comparison report.
    if (file_exists($report_path)) {
      unlink($report_path);
    }

    echo "Generating CSV comparison report $report_path";
    $compare->diffToCSV($report_path);
  }

}
