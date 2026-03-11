<?php
namespace Spectrum\Evidence\Shortcodes;

use Spectrum\Evidence\Core\Assets;
use Spectrum\Evidence\Core\Auth;
use Spectrum\Evidence\Core\Notices;
use Spectrum\Evidence\Core\View;

use Spectrum\Evidence\Repositories\EvidenceRepository;

if (!defined('ABSPATH')) exit;

final class MyEvidenceShortcode {
  public static function render() {
    if (!Auth::isLoggedIn()) return '<p>Silakan login untuk melihat evidence Anda.</p>';
    Assets::enqueueOnce();

    $user_id = Auth::userId();
    $user = wp_get_current_user();

    $filters = array(
      'year' => isset($_GET['year']) ? (int)$_GET['year'] : 0,
      'status' => isset($_GET['status']) ? sanitize_text_field($_GET['status']) : '',
      'sdg_number' => isset($_GET['sdg_number']) ? (int)$_GET['sdg_number'] : 0,
      'keyword' => isset($_GET['keyword']) ? sanitize_text_field($_GET['keyword']) : '',
    );

    $allowed_status = array('DRAFT', 'SUBMITTED', 'APPROVED', 'REJECTED');
    if (!in_array($filters['status'], $allowed_status, true)) {
      $filters['status'] = '';
    }

    return View::render('my-evidence', array(
      'notice' => Notices::get($user_id),
      'email'  => $user->user_email,
      'filters' => $filters,
      'years' => EvidenceRepository::distinctYearsBySubmitter($user_id),
      'rows'   => EvidenceRepository::findBySubmitterFiltered($user_id, $filters),
    ));
  }
}
