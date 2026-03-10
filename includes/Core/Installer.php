<?php
namespace Spectrum\Evidence\Core;

if (!defined('ABSPATH')) exit;
require_once __DIR__ . '/Db.php';
final class Installer {

  public static function install() {
    global $wpdb;
    require_once ABSPATH . 'wp-admin/includes/upgrade.php';

    $charset = $wpdb->get_charset_collate();

    // Minimal: pastikan attachment_id ada (karena requirement kamu)
    $tEvidence = Db::table('spectrum_evidence');

    // Kalau tabel evidence sudah ada, ALTER add column jika belum ada
    $has = $wpdb->get_results("SHOW COLUMNS FROM {$tEvidence} LIKE 'attachment_id'");
    if (empty($has)) {
      $wpdb->query("ALTER TABLE {$tEvidence} ADD COLUMN attachment_id BIGINT UNSIGNED NULL AFTER link_url");
      $wpdb->query("ALTER TABLE {$tEvidence} ADD KEY idx_evidence_attachment_id (attachment_id)");
    }

    // Optional: kalau kamu mau installer juga create table log (kalau belum)
    $tLog = Db::table('spectrum_evidence_log');
    $sqlLog = "CREATE TABLE {$tLog} (
      id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
      evidence_id BIGINT UNSIGNED NOT NULL,
      actor_id BIGINT UNSIGNED NULL,
      from_status VARCHAR(32) NULL,
      to_status VARCHAR(32) NOT NULL,
      notes TEXT NULL,
      created_at DATETIME NOT NULL,
      PRIMARY KEY (id),
      KEY evidence_id (evidence_id),
      KEY actor_id (actor_id)
    ) {$charset};";
    dbDelta($sqlLog);
  }
}