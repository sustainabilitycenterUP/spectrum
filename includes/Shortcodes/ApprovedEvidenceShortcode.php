<?php
namespace Spectrum\Evidence\Shortcodes;

use Spectrum\Evidence\Core\Assets;
use Spectrum\Evidence\Core\Auth;
use Spectrum\Evidence\Core\View;

use Spectrum\Evidence\Repositories\ApprovedEvidenceRepository;

if (!defined('ABSPATH')) exit;

final class ApprovedEvidenceShortcode {

  public static function render() {
    if (!Auth::isLoggedIn()) return '<p>Silakan login.</p>';
    if (!Auth::isReviewer()) return '<p>Halaman ini hanya untuk reviewer.</p>';

    Assets::enqueueOnce();

    $unit = isset($_GET['unit_code']) ? sanitize_text_field($_GET['unit_code']) : '';
    $sdg  = isset($_GET['sdg_number']) ? (int)$_GET['sdg_number'] : 0;

    $filters = array(
      'unit_code' => $unit,
      'sdg_number' => $sdg,
    );

    return View::render('approved-evidence', array(
      'active' => 'approved',
      'filters' => $filters,
      'units' => ApprovedEvidenceRepository::distinctUnits(),
      'rows' => ApprovedEvidenceRepository::list($filters),
    ));
  }
}