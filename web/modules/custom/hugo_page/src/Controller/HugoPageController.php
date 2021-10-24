<?php

namespace Drupal\hugo_page\Controller;

use Drupal\Core\Controller\ControllerBase;
use Symfony\Component\Finder\Finder;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

/**
 * Returns responses for Markdown page routes.
 */
class HugoPageController extends ControllerBase {

  /**
   * Active state from file system.
   *
   * @var array
   */
  public array $active = [];
  /**
   * Staged state from database.
   *
   * @var array
   */
  public array $staged = [];

  /**
   * Contstructor.
   */
  public function __construct() {
    $this->active = self::getActiveState();
    $this->staged = \Drupal::state()->get('hugo_page.routes', []);
  }

  /**
   * Get all file pathnames.
   */
  public static function getActiveState() :array {
    $paths = [];
    if (!defined('PROJECT_ROOT')) {
      define('PROJECT_ROOT', DRUPAL_ROOT . '/../');
    }
    $content_dir = PROJECT_ROOT . '/hugo/content';
    if (!is_readable($content_dir)) {
      return $paths;
    }
    $finder = new Finder();
    $finder->files()->in($content_dir);
    if ($finder->hasResults()) {
      $files = iterator_to_array($finder, FALSE);
      $paths = array_map(function ($file) {
        return str_replace('/', '_', str_replace('.md', '', $file->getRelativePathname()));
      }, $files);
    }
    return $paths;
  }

  /**
   * Check if markdown page route in $paths.
   */
  public function checkRouteId(String $routeId) {
    $matched = FALSE;
    if (!empty($this->staged)) {
      $matched = in_array($routeId, $this->staged);
    }
    return $matched;
  }

  /**
   * Builds the response.
   */
  public function content(String $routeId) {
    $path = str_replace('_', '/', $routeId);
    if (!empty($path) && !$this->checkRouteId($routeId)) {
      throw new NotFoundHttpException();
    }
    // Read files from built directory.
    $public_dir = PROJECT_ROOT . '/hugo/public';
    $body = file_get_contents($public_dir . '/' . $path . '/index.html');
    // Parse frontmatter.
    $params = json_decode(file_get_contents($public_dir . '/' . $path . '/index.json'));
    $params->data->routeId = $routeId;
    $params->data->path = '/' . $path;
    $build['content'] = [
      '#theme' => 'hugo_page',
      '#body' => $body,
      '#params' => $params->data,
    ];
    return $build;
  }

}
