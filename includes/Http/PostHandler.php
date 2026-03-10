<?php
namespace Spectrum\Evidence\Http;

use Spectrum\Evidence\Core\Auth;
use Spectrum\Evidence\Core\Notices;
use Spectrum\Evidence\Core\Url;

use Spectrum\Evidence\Services\EvidenceService;
use Spectrum\Evidence\Services\ReviewService;
use Spectrum\Evidence\Services\DeleteService;

if (!defined('ABSPATH')) exit;

final class PostHandler {

  public static function handle() {
    if (!Auth::isLoggedIn()) return;
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') return;

    // 1) evidence save (draft/submit/update_*)
    if (!empty($_POST['spectrum_action']) && !empty($_POST['spectrum_nonce'])) {
      self::handleEvidenceSave();
      return;
    }

    // 2) review action
    if (isset($_POST['review_action'], $_POST['evidence_id'])) {
      self::handleReview();
      return;
    }

    // 3) delete draft
    if (isset($_POST['action'], $_POST['evidence_id']) && $_POST['action'] === 'delete_evidence') {
      self::handleDelete();
      return;
    }
  }

  private static function redirectBack() {
    $redirect = '';
    if (!empty($_POST['redirect_to'])) $redirect = esc_url_raw($_POST['redirect_to']);
    if (!$redirect) $redirect = wp_get_referer();
    if (!$redirect) $redirect = home_url('/');
    wp_safe_redirect($redirect);
    exit;
  }

  private static function handleEvidenceSave() {
    $user_id = Auth::userId();

    if (!wp_verify_nonce($_POST['spectrum_nonce'], 'spectrum_save_evidence')) {
      Notices::set($user_id, 'error', 'Sesi sudah kedaluwarsa. Silakan muat ulang halaman dan coba lagi.');
      self::redirectBack();
    }

    $action = sanitize_text_field($_POST['spectrum_action']); // draft|submit|update_submit|update_draft etc

    $result = EvidenceService::createOrUpdateFromPost($action);

    if (is_wp_error($result)) {
      Notices::set($user_id, 'error', $result->get_error_message());
      self::redirectBack();
    }

    $target_status = (strpos($action, 'submit') !== false) ? 'SUBMITTED' : 'DRAFT';
    $msg = ($target_status === 'SUBMITTED')
      ? 'Evidence berhasil disubmit dan akan direview.'
      : 'Draft evidence berhasil disimpan.';

    Notices::set($user_id, 'success', $msg);
    wp_safe_redirect( Url::page('my') );
    exit;
  }

  private static function handleReview() {
    $evidence_id = (int)$_POST['evidence_id'];

    if (!wp_verify_nonce($_POST['_wpnonce'], 'review_action_' . $evidence_id)) {
      wp_die('Nonce tidak valid');
    }

    $decision = sanitize_text_field($_POST['review_action']); // approve|reject
    $notes = sanitize_textarea_field($_POST['review_notes'] ?? '');

    $mapped = ($decision === 'approve') ? 'APPROVED' : (($decision === 'reject') ? 'REJECTED' : '');

    $res = ReviewService::applyDecision($evidence_id, $mapped, $notes);

    if (is_wp_error($res)) {
      Notices::set(Auth::userId(), 'error', $res->get_error_message());
    } else {
      Notices::set(Auth::userId(), 'success', 'Review berhasil disimpan.');
    }

    wp_safe_redirect(Url::page('review'));
    exit;
  }

  private static function handleDelete() {
    $evidence_id = (int)$_POST['evidence_id'];

    if (!wp_verify_nonce($_POST['_wpnonce'], 'delete_evidence_' . $evidence_id)) {
      wp_die('Nonce tidak valid');
    }

    $res = DeleteService::deleteDraft($evidence_id);

    if (is_wp_error($res)) {
      Notices::set(Auth::userId(), 'error', $res->get_error_message());
    } else {
      Notices::set(Auth::userId(), 'success', 'Evidence draft berhasil dihapus.');
    }

    self::redirectBack();
  }
}