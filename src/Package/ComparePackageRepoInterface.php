<?php

namespace SiteStudio\Package;


interface ComparePackageRepoInterface {

  /**
   * Yaml Difference Report.
   */
  function yamlDiffReport();

  /**
   * Pull Projects based on User choice of Repo and Group.
   */
  function getProjectsList();

  /**
   * Gitlab Repo File Download.
   */
  function downloadRepoFiles(array $project_details);

  /**
   * Invoke Site studio Compare tool.
   */
  function invokeCompareRepo(array $package_names);

}
