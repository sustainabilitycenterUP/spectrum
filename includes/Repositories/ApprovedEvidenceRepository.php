<?php
namespace Spectrum\Evidence\Repositories;

use Spectrum\Evidence\Core\Db;

if (!defined('ABSPATH')) exit;

final class ApprovedEvidenceRepository {

  public static function list($filters = array()) {
    global $wpdb;

    $e  = Db::table('spectrum_evidence');
    $em = Db::table('spectrum_evidence_metric');
    $m  = Db::table('spectrum_metric');

    $where = "WHERE e.status = 'APPROVED'";
    $params = array();

    if (!empty($filters['unit_code'])) {
      $where .= " AND e.unit_code = %s";
      $params[] = $filters['unit_code'];
    }

    if (!empty($filters['sdg_number'])) {
      $where .= " AND m.sdg_number = %d";
      $params[] = (int)$filters['sdg_number'];
    }

    $sql = "
      SELECT e.id, e.year, e.title, e.unit_code, e.status, e.updated_at,
             m.sdg_number, m.metric_code, m.metric_title
      FROM {$e} e
      LEFT JOIN {$em} em ON em.evidence_id = e.id
      LEFT JOIN {$m}  m  ON m.id = em.metric_id
      {$where}
      ORDER BY e.updated_at DESC, e.created_at DESC
    ";

    if (!empty($params)) {
      $sql = $wpdb->prepare($sql, $params);
    }

    return $wpdb->get_results($sql);
  }

  public static function distinctUnits() {
    global $wpdb;
    $e = Db::table('spectrum_evidence');
    return $wpdb->get_col("SELECT DISTINCT unit_code FROM {$e} WHERE unit_code <> '' ORDER BY unit_code ASC");
  }
}