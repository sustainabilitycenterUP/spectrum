<?php
use Spectrum\Evidence\Core\Url;

if (!defined('ABSPATH')) exit;

$active = 'new';
include __DIR__ . '/layout-open.php';
?>

<div class="sp-page-header">
  <div>
    <h1>Buat Evidence Baru</h1>
    <p>Isi form berikut untuk menyimpan draft atau submit evidence.</p>
  </div>
  <!-- <a class="sp-btn-secondary" href="<?#php echo esc_url(Url::page('my')); ?>">← Kembali</a> -->
</div>

<section class="sp-card">
  <?php if (!empty($notice) && !empty($notice['messages'])): ?>
    <div class="sp-alert <?php echo ($notice['type']==='success')?'sp-alert-success':'sp-alert-error'; ?>">
      <ul>
        <?php foreach ((array)$notice['messages'] as $m): ?><li><?php echo esc_html($m); ?></li><?php endforeach; ?>
      </ul>
    </div>
  <?php endif; ?>

  <form method="post" enctype="multipart/form-data" class="sp-form-wrapper">
    <?php wp_nonce_field('spectrum_save_evidence', 'spectrum_nonce'); ?>

    <div class="sp-form-row">
      <label class="sp-label">Metrik THE *</label>
      <select name="metric_id" id="metric_select" class="sp-select" required>
        <option value="">-- Pilih Metrik --</option>
        <?php foreach ((array)$metric_options as $group_label => $items): ?>
          <optgroup label="<?php echo esc_attr($group_label); ?>">
            <?php foreach ((array)$items as $m): ?>
              <option value="<?php echo esc_attr($m->id); ?>"
                data-question="<?php echo esc_attr($m->metric_question); ?>"
                data-note="<?php echo esc_attr($m->metric_note); ?>">
                <?php echo esc_html('SDG '.$m->sdg_number.' – '.$m->metric_code.' – '.$m->metric_title); ?>
              </option>
            <?php endforeach; ?>
          </optgroup>
        <?php endforeach; ?>
      </select>
    </div>

    <div id="metric_info" class="sp-metric-box" style="display:none;">
      <div class="sp-metric-title">Metric Question</div>
      <div id="metric_question"></div>
      <div class="sp-metric-title" style="margin-top:8px;">Metric Note</div>
      <div id="metric_note"></div>
    </div>

    <div class="sp-form-row">
      <label class="sp-label">Judul Evidence *</label>
      <input type="text" name="title" class="sp-input" required>
    </div>

    <!-- BENTUK EVIDENCE -->
    <div class="sp-form-row">
      <label class="sp-label">Bentuk Evidence *</label>
      <div style="display:flex;gap:16px;align-items:center;">
        <label style="display:flex;gap:6px;align-items:center;">
          <input type="radio" name="source_type" value="link" required> Link
        </label>
        <label style="display:flex;gap:6px;align-items:center;">
          <input type="radio" name="source_type" value="file" required> File
        </label>
      </div>
    </div>

    <!-- LINK -->
    <div class="sp-form-row sp-source sp-source-link" style="display:none;">
      <label class="sp-label">Link URL *</label>
      <input type="url" name="link_url" class="sp-input" placeholder="https://..." >
      <div class="sp-help">
        Catatan: pastikan link accessible dan bisa diakses sampai tahun 2025.
      </div>
    </div>

    <!-- FILE -->
    <div class="sp-form-row sp-source sp-source-file" style="display:none;">
      <label class="sp-label">Upload File *</label>
      <input type="file" name="evidence_file" class="sp-input">
    </div>

    <div class="sp-form-row">
      <label class="sp-label">Ringkasan Evidence *</label>
      <textarea name="summary" class="sp-textarea" required></textarea>
    </div>

    <div class="sp-form-row">
      <label class="sp-label">Tahun Pelaporan *</label>
      <select name="year" class="sp-select" required>
        <?php foreach ((array)$years as $y): ?>
          <option value="<?php echo esc_attr($y); ?>"><?php echo esc_html($y); ?></option>
        <?php endforeach; ?>
      </select>
      <div class="sp-help">Periode data yang diminta: Oktober 2024 – Agustus 2025.</div>
    </div>

    <div class="sp-form-actions">
      <button type="submit" name="spectrum_action" value="draft" class="sp-btn-secondary">Simpan Draft</button>
      <button type="submit" name="spectrum_action" value="submit" class="sp-btn-primary">Submit</button>
    </div>
  </form>
</section>

<script>
document.getElementById('metric_select').addEventListener('change', function () {
  const opt = this.options[this.selectedIndex];
  const box = document.getElementById('metric_info');
  if (!this.value) { box.style.display = 'none'; return; }
  document.getElementById('metric_question').innerHTML = opt.dataset.question || '-';
  document.getElementById('metric_note').innerHTML = opt.dataset.note || '-';
  box.style.display = 'block';
});
</script>

<!-- JS untuk pilih jenis evidence (file/url) -->
<script> 
(function(){
  const form = document.querySelector('form[enctype="multipart/form-data"]');
  if (!form) return;

  const radios = form.querySelectorAll('input[name="source_type"]');
  const linkWrap = form.querySelector('.sp-source-link');
  const fileWrap = form.querySelector('.sp-source-file');
  const linkInput = form.querySelector('input[name="link_url"]');
  const fileInput = form.querySelector('input[name="evidence_file"]');

  function setMode(mode){
    if (mode === 'link') {
      linkWrap.style.display = '';
      fileWrap.style.display = 'none';
      linkInput.required = true;
      fileInput.required = false;
      // optional: clear file selection
      if (fileInput) fileInput.value = '';
    } else if (mode === 'file') {
      linkWrap.style.display = 'none';
      fileWrap.style.display = '';
      linkInput.required = false;
      fileInput.required = true;
      // optional: clear link
      if (linkInput) linkInput.value = '';
    }
  }

  radios.forEach(r => r.addEventListener('change', () => setMode(r.value)));

  // default: belum pilih, semua hidden
})();
</script>

<?php include __DIR__ . '/layout-close.php'; ?>