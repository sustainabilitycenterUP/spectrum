<?php
namespace Spectrum\Evidence\Shortcodes;

use Spectrum\Evidence\Core\Assets;
use Spectrum\Evidence\Core\Auth;
use Spectrum\Evidence\Core\View;
use Spectrum\Evidence\Repositories\DashboardRepository;

if (!defined('ABSPATH')) exit;

final class DashboardShortcode {
  public static function render() {
    if (!Auth::isLoggedIn()) return '<p>Silakan login.</p>';

    Assets::enqueueOnce();

    $year = isset($_GET['year']) ? (int)$_GET['year'] : 0;

    $data = array(
      'active' => 'dashboard',
      'year'   => $year,
      'years'  => DashboardRepository::distinctYears(),
      'status' => DashboardRepository::statusCounts($year),
      'sdg'    => DashboardRepository::sdgCounts($year),
      'sdg_summary' => DashboardRepository::sdgSummary($year),
      'unit'   => DashboardRepository::unitCounts($year),
      'top_units'   => DashboardRepository::topApprovedUnits($year),
      // 'latest' => DashboardRepository::latestEvidence(8, $year),
    );

    return View::render('dashboard', $data);
  }
}