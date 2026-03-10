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

    return View::render('my-evidence', array(
      'notice' => Notices::get($user_id),
      'email'  => $user->user_email,
      'rows'   => EvidenceRepository::findBySubmitter($user_id),
    ));
  }
}