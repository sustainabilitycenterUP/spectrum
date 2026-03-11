<?php
use Spectrum\Evidence\Core\Url;

if (!defined('ABSPATH')) exit;

include __DIR__ . '/layout-open.php';

$year = (int)($year ?? 0);
$status = $status ?? array();
$weekly = $weekly ?? array('total' => 0, 'submitted' => 0, 'approved' => 0);
?>

<div class="sp-page-header">
  <div class="sp-page-title-block">
    <h1>Dashboard SPECTRUM</h1>
    <p>Ringkasan progres pengumpulan evidence (semua status).</p>
  </div>
</div>

<section class="sp-card">

  <form method="get" style="display:flex;gap:12px;flex-wrap:wrap;align-items:end;margin-bottom:14px;">
    <div>
      <label class="sp-label">Tahun</label>
      <select name="year" class="sp-select" style="min-width:160px;">
        <option value="">Semua Tahun</option>
        <?php foreach ((array)$years as $y): ?>
          <option value="<?php echo (int)$y; ?>" <?php selected($year, (int)$y); ?>>
            <?php echo esc_html($y); ?>
          </option>
        <?php endforeach; ?>
      </select>
    </div>
    <div>
      <button class="sp-btn-primary" type="submit">Terapkan</button>
      <a class="sp-btn-secondary" href="<?php echo esc_url(Url::page('dashboard')); ?>">Reset</a>
    </div>
  </form>

  <!-- Cards status -->
  <div style="display:grid;grid-template-columns:repeat(3,minmax(0,1fr));gap:14px;">
    <?php
      $totalEvidence =
        (int)($status['DRAFT'] ?? 0) +
        (int)($status['SUBMITTED'] ?? 0) +
        (int)($status['APPROVED'] ?? 0) +
        (int)($status['REJECTED'] ?? 0);

      $cards = array(
        'Total Evidence' => $totalEvidence,
        'Submitted'      => (int)($status['SUBMITTED'] ?? 0),
        'Approved'       => (int)($status['APPROVED'] ?? 0),
      );
    ?>
    <?php foreach ($cards as $k => $v): ?>
      <div class="sp-box">
        <div style="color:#6b7280;font-size:12px;"><?php echo esc_html($k); ?></div>
        <div style="font-size:26px;font-weight:650;margin-top:6px;"><?php echo esc_html($v); ?></div>
        <div style="margin-top:6px;">
          <span style="background:#dcfce7;color:#166534;padding:3px 8px;border-radius:999px;font-size:11px;">
            +<?php
              echo ($k === 'Total Evidence')
                ? (int)$weekly['total']
                : (($k === 'Submitted') ? (int)$weekly['submitted'] : (int)$weekly['approved']);
            ?> dalam 7 hari
          </span>
        </div>
      </div>
    <?php endforeach; ?>
  </div>

  <div style="display:grid;grid-template-columns: 1fr 1fr; gap:14px; margin-top:16px;">
    <!-- SDG -->
    <div class="sp-box">
      <h3 style="margin-top:0;">Progress per SDG</h3>
      <?php if (empty($sdg)): ?>
        <div style="color:#6b7280;">Belum ada data SDG.</div>
      <?php else: ?>
        <?php foreach ($sdg as $row): ?>
          <div style="margin-bottom:10px;">
            <div style="display:flex;justify-content:space-between;font-size:12px;">
              <div>SDG <?php echo esc_html($row->sdg_number); ?></div>
              <div><?php echo (int)$row->total; ?></div>
            </div>
            <div style="background:#eee;height:8px;border-radius:4px;">
              <div style="width:<?php echo min(((int)$row->total)*5,100); ?>%;background:#31572C;height:8px;border-radius:4px;"></div>
            </div>
          </div>
        <?php endforeach; ?>
      <?php endif; ?>
    </div>

    <!-- UNIT -->
    <div class="sp-box">
      <h3 style="margin-top:0;">
        Progress per Unit
        <span title="Dihitung berdasarkan jumlah evidence yang sudah SUBMITTED" style="cursor:help;color:#6b7280;">ⓘ</span>
        </h3>

        <?php foreach ($unit as $u): ?>
          <div style="margin-bottom:10px;">
            <div style="display:flex;justify-content:space-between;font-size:12px;">
              <div><?php echo esc_html($u->unit_code ?: '—'); ?></div>
              <div><?php echo (int)$u->total; ?></div>
            </div>

            <div style="background:#eee;height:8px;border-radius:4px;">
              <div style="width:<?php echo min(((int)$u->total)*5,100); ?>%;background:#1d4ed8;height:8px;border-radius:4px;"></div>
            </div>
          </div>
        <?php endforeach; ?>
      <?php if (empty($unit)): ?>
        <div style="color:#6b7280;">Belum ada data unit.</div>
      <?php else: ?>
        <div style="max-height:320px;overflow:auto;">
          <?php foreach ($unit as $u): ?>
            <div style="display:flex;justify-content:space-between;border-bottom:1px solid #eee;padding:8px 0;font-size:12px;">
              <div><?php echo esc_html($u->unit_code ?: '—'); ?></div>
              <div><strong><?php echo (int)$u->total; ?></strong></div>
            </div>
          <?php endforeach; ?>
        </div>
      <?php endif; ?>
    </div>
  </div>

  <!-- Top Fungsi -->
  <div class="sp-box" style="margin-top:14px;">
    <h3>
    Top Fungsi (Approved Evidence)
    <span title="Unit dengan jumlah evidence APPROVED terbanyak" style="cursor:help;color:#6b7280;">ⓘ</span>
    </h3>

    <?php if (empty($top_units)): ?>
      <div style="color:#6b7280;">Belum ada data.</div>
    <?php else: ?>

      <?php foreach ($top_units as $u): ?>

      <div style="margin-bottom:10px;">
        <div style="display:flex;justify-content:space-between;font-size:13px;">
          <strong><?php echo esc_html($u->unit_code); ?></strong>
          <span><?php echo (int)$u->total; ?> approved</span>
        </div>

        <div style="background:#eee;height:10px;border-radius:5px;">
          <div style="width:<?php echo min(((int)$u->total)*10,100); ?>%;background:#16a34a;height:10px;border-radius:5px;"></div>
        </div>

      </div>

      <?php endforeach; ?>

    <?php endif; ?>
    </div>

    <!-- evidence per sdg -->
    <div class="sp-box" style="margin-top:14px;">

    <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:10px;">
      <div>
        <h3 style="margin:0;">Evidence per Metrik</h3>
        <div style="font-size:12px;color:#6b7280;">
          <!-- Klik salah satu SDG untuk drill-down ke metric. -->
        </div>
      </div>
    </div>

    <?php if (empty($sdg_summary)): ?>

      <div style="color:#6b7280;">Belum ada data SDG.</div>

    <?php else: ?>

    <table class="sp-table">

    <thead>
    <tr>
    <th style="width:60px;">SDG</th>
    <th>Nama</th>
    <th>Total</th>
    <th>Submitted</th>
    <th>Approved</th>
    </tr>
    </thead>

    <tbody>

    <?php foreach ($sdg_summary as $row): ?>

    <tr>

    <td>
    <strong><?php echo (int)$row->sdg_number; ?></strong>
    </td>

    <td>
    <?php echo esc_html($row->metric_title); ?>
    </td>

    <td>
    <?php echo (int)$row->total; ?>
    </td>

    <td>
    <?php echo (int)$row->submitted; ?>
    </td>

    <td>
    <?php echo (int)$row->approved; ?>
    </td>

    </tr>

    <?php endforeach; ?>

    </tbody>
    </table>

    <?php endif; ?>

    </div>
</section>

<?php include __DIR__ . '/layout-close.php'; ?>
