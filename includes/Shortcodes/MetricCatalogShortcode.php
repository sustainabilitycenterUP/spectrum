<?php
namespace Spectrum\Evidence\Shortcodes;

use Spectrum\Evidence\Core\Assets;
use Spectrum\Evidence\Core\Auth;
use Spectrum\Evidence\Core\View;
use Spectrum\Evidence\Repositories\MetricRepository;

if (!defined('ABSPATH')) exit;

final class MetricCatalogShortcode {
  public static function render() {
    if (!Auth::isLoggedIn()) return '<p>Silakan login.</p>';

    Assets::enqueueOnce();

    $filters = array(
      'year' => isset($_GET['year']) ? (int)$_GET['year'] : 0,
      'sdg_number' => isset($_GET['sdg_number']) ? (int)$_GET['sdg_number'] : 0,
      'keyword' => isset($_GET['keyword']) ? sanitize_text_field($_GET['keyword']) : '',
    );

    return View::render('metric-catalog', array(
      'active' => 'metrics',
      'filters' => $filters,
      'years' => MetricRepository::activeYears(),
      'rows' => MetricRepository::catalog($filters),
    ));
  }
}
