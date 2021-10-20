<?php

/**
 * @file
 * Contains \DrupalProject\composer\ScriptHandler.
 * From https://github.com/drupal-composer/drupal-project/blob/9.x/scripts/composer/ScriptHandler.php
 */

namespace DrupalProject\composer;

use Composer\Script\Event;
use Composer\Semver\Comparator;
use Dotenv\Dotenv;
use Drupal\Core\Site\Settings;
use DrupalFinder\DrupalFinder;
use Symfony\Component\Filesystem\Filesystem;
use Webmozart\PathUtil\Path;

class ScriptHandler {

  /**
   * Checks if the installed version of Composer is compatible.
   *
   * Composer 1.0.0 and higher consider a `composer install` without having a
   * lock file present as equal to `composer update`. We do not ship with a lock
   * file to avoid merge conflicts downstream, meaning that if a project is
   * installed with an older version of Composer the scaffolding of Drupal will
   * not be triggered. We check this here instead of in drupal-scaffold to be
   * able to give immediate feedback to the end user, rather than failing the
   * installation after going through the lengthy process of compiling and
   * downloading the Composer dependencies.
   *
   * @see https://github.com/composer/composer/pull/5035
   */
  public static function checkComposerVersion(Event $event) {
    $composer = $event->getComposer();
    $io = $event->getIO();

    $version = $composer::VERSION;

    // The dev-channel of composer uses the git revision as version number,
    // try to the branch alias instead.
    if (preg_match('/^[0-9a-f]{40}$/i', $version)) {
      $version = $composer::BRANCH_ALIAS_VERSION;
    }

    // If Composer is installed through git we have no easy way to determine if
    // it is new enough, just display a warning.
    if ($version === '@package_version@' || $version === '@package_branch_alias_version@') {
      $io->writeError('<warning>You are running a development version of Composer. If you experience problems, please update Composer to the latest stable version.</warning>');
    }
    elseif (Comparator::lessThan($version, '1.0.0')) {
      $io->writeError('<error>Drupal-project requires Composer version 1.0.0 or higher. Please update your Composer before continuing</error>.');
      exit(1);
    }
  }

  /**
   * Add vscode workspace file.
   */
  public static function addWorkspace($event) {
    $fs = new Filesystem();
    $drupalFinder = new DrupalFinder();
    $drupalFinder->locateRoot(getcwd());
    $io = $event->getIO();

    $composerRoot = $drupalFinder->getComposerRoot();
    $projectCode = basename($composerRoot);

    $source = $composerRoot . '/config/site-dev/code-workspace';
    $destination = $composerRoot . '/' . $projectCode . '.code-workspace';
    if (!$fs->exists($destination)) {
      $fs->copy($source, $destination);
      $io->write("code $projectCode.code-workspace");   
    }
    else {
      $io->write("Worksapce file exists.");      
    }
  }

  /**
   * Add settings.local.php.
   */
  public static function addSettingsLocal($event) {
    $fs = new Filesystem();
    $drupalFinder = new DrupalFinder();
    $drupalFinder->locateRoot(getcwd());
    $drupalRoot = $drupalFinder->getDrupalRoot();
    $composerRoot = $drupalFinder->getComposerRoot();

    $settingsFile = $drupalRoot . '/sites/default/settings.php';
    $settingsLocalFile = $composerRoot . '/config/site-dev/settings.local.php';
    $settingsLocalFileTarget = $drupalRoot . '/sites/default/settings.local.php';
    $includeSettingsLocal = PHP_EOL . 'include "settings.local.php";' . PHP_EOL;

    $fs->chmod($drupalRoot . '/sites/default', 0777);
    $fs->chmod($settingsFile, 0666);
    $fs->chmod($settingsLocalFile, 0666);
    file_put_contents($settingsFile, $includeSettingsLocal, FILE_APPEND | LOCK_EX);
    symlink($settingsLocalFile, $settingsLocalFileTarget);
    $fs->chmod($drupalRoot . '/sites/default', 0755);
    $fs->chmod($settingsFile, 0444);
  }

}
