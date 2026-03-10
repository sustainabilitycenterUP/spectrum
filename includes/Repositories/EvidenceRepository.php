<?php
namespace Spectrum\Evidence\Repositories;

use Spectrum\Evidence\Core\Db;

if (!defined('ABSPATH')) exit;

final class EvidenceRepository {

  public static function table() {
    return Db::table('spectrum_evidence');
  }

  public static function find($id) {
    global $wpdb;
    $t = self::table();
    return $wpdb->get_row($wpdb->prepare("SELECT * FROM {$t} WHERE id=%d", (int)$id));
  }

  public static function findBySubmitter($submitter_id) {
    global $wpdb;
    $t = self::table();
    return $wpdb->get_results($wpdb->prepare(
      "SELECT id, year, title, status, unit_code, updated_at, created_at
       FROM {$t}
       WHERE submitter_id=%d
       ORDER BY updated_at DESC, created_at DESC",
      (int)$submitter_id
    ));
  }

  public static function insert($data) {
    global $wpdb;
    $t = self::table();
    $ok = $wpdb->insert($t, $data);
    if (!$ok) return false;
    return (int)$wpdb->insert_id;
  }

  public static function update($id, $data) {
    global $wpdb;
    $t = self::table();
    return $wpdb->update($t, $data, array('id' => (int)$id));
  }

  public static function delete($id) {
    global $wpdb;
    $t = self::table();
    return $wpdb->delete($t, array('id' => (int)$id));
  }

  public static function listForReview($status = '') {
    global $wpdb;
    $t = self::table();

    if ($status) {
      return $wpdb->get_results($wpdb->prepare(
        "SELECT id, year, title, unit_code, status, updated_at, created_at
         FROM {$t}
         WHERE status=%s
         ORDER BY updated_at DESC, created_at DESC",
        $status
      ));
    }

    // default queue: hanya SUBMITTED
    return $wpdb->get_results(
      "SELECT id, year, title, unit_code, status, updated_at, created_at
       FROM {$t}
       WHERE status='SUBMITTED'
       ORDER BY updated_at DESC, created_at DESC"
    );
  }
}