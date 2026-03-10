<?php
namespace Spectrum\Evidence\Shortcodes;

use Spectrum\Evidence\Core\Assets;
use Spectrum\Evidence\Core\Auth;
use Spectrum\Evidence\Core\Notices;
use Spectrum\Evidence\Core\View;

use Spectrum\Evidence\Repositories\MetricRepository;
use Spectrum\Evidence\Repositories\YearMetricRepository;

if (!defined('ABSPATH')) exit;

final class EvidenceFormShortcode {
  public static function render() {
    if (!Auth::isLoggedIn()) return '<p>Silakan login untuk mengisi evidence.</p>';
    Assets::enqueueOnce();

    $user_id = Auth::userId();

    $years = YearMetricRepository::yearsActiveDistinct();
    $metrics = MetricRepository::activeMetricsWithYear();

    // group option
    $metric_options = array();
    foreach ((array)$metrics as $m) {
      $key = 'SDG ' . $m->sdg_number . ' – Tahun ' . $m->year;
      if (!isset($metric_options[$key])) $metric_options[$key] = array();
      $metric_options[$key][] = $m;
    }

    return View::render('evidence-form', array(
      'notice' => Notices::get($user_id),
      'years'  => $years,
      'metric_options' => $metric_options,
    ));
  }
}