<?php
use Spectrum\Evidence\Core\Url;

if (!defined('ABSPATH')) exit;

include __DIR__ . '/layout-open.php';
?>

<div class="sp-page-header">
  <div class="sp-page-title-block">
    <h1>Approved Evidence</h1>
    <p>Daftar evidence yang sudah <strong>APPROVED</strong>. Gunakan filter Unit atau SDG.</p>
  </div>
</div>

<section class="sp-card">
  <form method="get" style="display:flex;gap:12px;flex-wrap:wrap;align-items:end;margin-bottom:14px;">
    <div>
      <label class="sp-label">Filter Unit</label>
      <select name="unit_code" class="sp-select" style="min-width:220px;">
        <option value="">Semua Unit</option>
        <?php foreach ((array)$units as $u): ?>
          <option value="<?php echo esc_attr($u); ?>" <?php selected($filters['unit_code'], $u); ?>>
            <?php echo esc_html($u); ?>
          </option>
        <?php endforeach; ?>
      </select>
    </div>

    <div>
      <label class="sp-label">Filter SDG</label>
      <select name="sdg_number" class="sp-select" style="min-width:160px;">
        <option value="">Semua SDG</option>
        <?php for ($i=1;$i<=17;$i++): ?>
          <option value="<?php echo $i; ?>" <?php selected((int)$filters['sdg_number'], $i); ?>>
            SDG <?php echo $i; ?>
          </option>
        <?php endfor; ?>
      </select>
    </div>

    <div>
      <button class="sp-btn-primary" type="submit">Terapkan Filter</button>
      <a class="sp-btn-secondary" href="<?php echo esc_url(Url::page('approved')); ?>">Reset</a>
    </div>
  </form>

  <?php if (empty($rows)): ?>
    <div style="padding:14px;color:#6b7280;">Belum ada evidence approved (atau hasil filter kosong).</div>
  <?php else: ?>
    <div style="width:100%;overflow-x:auto;">
      <table class="sp-table" style="min-width:980px;">
        <thead>
          <tr>
            <th>Judul</th>
            <th>Tahun</th>
            <th>Unit</th>
            <th>SDG</th>
            <th>Metric</th>
            <th>Update</th>
            <th>Aksi</th>
          </tr>
        </thead>
        <tbody>
          <?php foreach ($rows as $r): ?>
            <tr>
              <td><?php echo esc_html($r->title); ?></td>
              <td><?php echo esc_html($r->year); ?></td>
              <td><?php echo esc_html($r->unit_code); ?></td>
              <td><?php echo $r->sdg_number ? esc_html('SDG '.$r->sdg_number) : '—'; ?></td>
              <td>
                <?php echo $r->metric_code ? esc_html($r->metric_code.' – '.$r->metric_title) : '—'; ?>
              </td>
              <td><?php echo esc_html($r->updated_at); ?></td>
              <td>
                <a class="sp-btn-secondary" href="<?php echo esc_url(Url::page('detail', array('evidence_id'=>$r->id))); ?>">
                  Detail
                </a>
              </td>
            </tr>
          <?php endforeach; ?>
        </tbody>
      </table>
    </div>
  <?php endif; ?>
</section>

<?php include __DIR__ . '/layout-close.php'; ?>