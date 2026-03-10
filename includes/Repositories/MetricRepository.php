<?php
namespace Spectrum\Evidence\Repositories;

use Spectrum\Evidence\Core\Db;

if (!defined('ABSPATH')) exit;

final class MetricRepository {
  public static function tMetric() { return Db::table('spectrum_metric'); }
  public static function tYearMetric() { return Db::table('spectrum_year_metric'); }

  public static function activeMetricsWithYear() {
    global $wpdb;
    $m = self::tMetric();
    $y = self::tYearMetric();

    return $wpdb->get_results("
      SELECT 
        m.id, m.metric_code, m.metric_title, m.metric_question, m.metric_note, m.sdg_number, y.year
      FROM {$m} m
      JOIN {$y} y ON y.metric_id = m.id
      WHERE y.is_active=1
      ORDER BY m.sdg_number, m.metric_code
    ");
  }
}