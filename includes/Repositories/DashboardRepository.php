<?php
namespace Spectrum\Evidence\Repositories;

use Spectrum\Evidence\Core\Db;

if (!defined('ABSPATH')) exit;

final class DashboardRepository {

  public static function distinctYears() {
    global $wpdb;
    $e = Db::table('spectrum_evidence');
    return $wpdb->get_col("SELECT DISTINCT year FROM {$e} ORDER BY year DESC");
  }

  public static function statusCounts($year = 0) {
    global $wpdb;
    $e = Db::table('spectrum_evidence');

    $where = "1=1";
    $params = array();

    if ($year) {
      $where .= " AND year = %d";
      $params[] = $year;
    }

    $sql = "SELECT status, COUNT(*) AS total FROM {$e} WHERE {$where} GROUP BY status";
    if ($params) $sql = $wpdb->prepare($sql, $params);

    $rows = $wpdb->get_results($sql);
    $out = array('DRAFT'=>0,'SUBMITTED'=>0,'APPROVED'=>0,'REJECTED'=>0);
    foreach ((array)$rows as $r) $out[$r->status] = (int)$r->total;

    return $out;
  }

  public static function sdgCounts($year = 0) {
    global $wpdb;
    $e  = Db::table('spectrum_evidence');
    $em = Db::table('spectrum_evidence_metric');
    $m  = Db::table('spectrum_metric');

    $where = "e.status IN ('DRAFT','SUBMITTED','APPROVED','REJECTED')";
    $params = array();

    if ($year) {
      $where .= " AND e.year = %d";
      $params[] = $year;
    }

    $sql = "
      SELECT m.sdg_number, COUNT(DISTINCT e.id) AS total
      FROM {$e} e
      LEFT JOIN {$em} em ON em.evidence_id = e.id
      LEFT JOIN {$m}  m  ON m.id = em.metric_id
      WHERE {$where} AND m.sdg_number IS NOT NULL
      GROUP BY m.sdg_number
      ORDER BY m.sdg_number
    ";
    if ($params) $sql = $wpdb->prepare($sql, $params);

    return $wpdb->get_results($sql);
  }

  public static function unitCounts($year = 0) {
    global $wpdb;
    $e = Db::table('spectrum_evidence');

    $where = "status = 'SUBMITTED'";
    $params = array();

    if ($year) {
      $where .= " AND year = %d";
      $params[] = $year;
    }

    $sql = "
      SELECT unit_code, COUNT(*) AS total
      FROM {$e}
      WHERE {$where}
      GROUP BY unit_code
      ORDER BY total DESC, unit_code ASC
    ";
    if ($params) $sql = $wpdb->prepare($sql, $params);

    return $wpdb->get_results($sql);
  }

  public static function latestEvidence($limit = 8, $year = 0) {
    global $wpdb;
    $e  = Db::table('spectrum_evidence');
    $em = Db::table('spectrum_evidence_metric');
    $m  = Db::table('spectrum_metric');

    $where = "1=1";
    $params = array();

    if ($year) {
      $where .= " AND e.year = %d";
      $params[] = $year;
    }

    $sql = "
      SELECT e.id, e.title, e.unit_code, e.status, e.updated_at, e.year,
             m.sdg_number, m.metric_code
      FROM {$e} e
      LEFT JOIN {$em} em ON em.evidence_id = e.id
      LEFT JOIN {$m}  m  ON m.id = em.metric_id
      WHERE {$where}
      ORDER BY e.updated_at DESC, e.created_at DESC
      LIMIT %d
    ";
    $params[] = (int)$limit;

    $sql = $wpdb->prepare($sql, $params);
    return $wpdb->get_results($sql);
  }

  public static function topApprovedUnits($year = 0) {
    global $wpdb;
    $e = Db::table('spectrum_evidence');

    $where = "status = 'APPROVED'";
    $params = array();

    if ($year) {
      $where .= " AND year = %d";
      $params[] = $year;
    }

    $sql = "
      SELECT unit_code, COUNT(*) AS total
      FROM {$e}
      WHERE {$where}
      GROUP BY unit_code
      ORDER BY total DESC
      LIMIT 3
    ";

    if ($params) $sql = $wpdb->prepare($sql, $params);

    return $wpdb->get_results($sql);
  }

  public static function sdgSummary($year = 0) {
    global $wpdb;

    $e  = Db::table('spectrum_evidence');
    $em = Db::table('spectrum_evidence_metric');
    $m  = Db::table('spectrum_metric');

    $where = "1=1";
    $params = array();

    if ($year) {
      $where .= " AND e.year = %d";
      $params[] = $year;
    }

    $sql = "
      SELECT
        m.sdg_number,
        m.metric_title ,

        COUNT(e.id) AS total,

        SUM(CASE WHEN e.status = 'SUBMITTED' THEN 1 ELSE 0 END) AS submitted,

        SUM(CASE WHEN e.status = 'APPROVED' THEN 1 ELSE 0 END) AS approved

      FROM {$e} e
      LEFT JOIN {$em} em ON em.evidence_id = e.id
      LEFT JOIN {$m} m ON m.id = em.metric_id

      WHERE {$where} AND m.sdg_number IS NOT NULL

      GROUP BY m.sdg_number, m.metric_title

      ORDER BY m.sdg_number
    ";

    if ($params) {
      $sql = $wpdb->prepare($sql, $params);
    }

    return $wpdb->get_results($sql);
  }

  public static function weeklyCounts($year = 0) {
    global $wpdb;
    $e = Db::table('spectrum_evidence');

    $where = "created_at >= DATE_SUB(NOW(), INTERVAL 7 DAY)";
    $params = array();

    if ($year) {
      $where .= " AND year = %d";
      $params[] = $year;
    }

    $sql = "
      SELECT
        COUNT(*) AS total,
        SUM(CASE WHEN status='SUBMITTED' THEN 1 ELSE 0 END) AS submitted,
        SUM(CASE WHEN status='APPROVED' THEN 1 ELSE 0 END) AS approved
      FROM {$e}
      WHERE {$where}
    ";

    if (!empty($params)) {
      $sql = $wpdb->prepare($sql, $params);
    }

    $row = $wpdb->get_row($sql);

    return array(
      'total' => (int)($row->total ?? 0),
      'submitted' => (int)($row->submitted ?? 0),
      'approved' => (int)($row->approved ?? 0),
    );
  }
}
