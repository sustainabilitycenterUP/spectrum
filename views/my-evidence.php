<?php
use Spectrum\Evidence\Core\Url;

if (!defined('ABSPATH')) exit;

$active = 'my';
include __DIR__ . '/layout-open.php';
?>

<div class="sp-page-header">
  <div class="sp-page-title-block">
    <h1>Evidence Saya</h1>
    <p>Lihat dan kelola seluruh evidence yang pernah Anda ajukan. Akun: <strong><?php echo esc_html($email); ?></strong></p>
  </div>
  <a href="<?php echo esc_url(Url::page('new')); ?>" class="sp-btn-primary">+ Buat Evidence Baru</a>
</div>

<section class="sp-card">
  <?php if (!empty($notice) && !empty($notice['messages'])): ?>
    <div class="sp-alert <?php echo ($notice['type']==='success')?'sp-alert-success':'sp-alert-error'; ?>">
      <ul>
        <?php foreach ((array)$notice['messages'] as $m): ?><li><?php echo esc_html($m); ?></li><?php endforeach; ?>
      </ul>
    </div>
  <?php endif; ?>

  <?php if (empty($rows)): ?>
    <div class="sp-empty">Belum ada evidence. Klik <strong>"Buat Evidence Baru"</strong> untuk mulai.</div>
  <?php else: ?>
    <div style="width:100%;overflow-x:auto;">
      <table class="sp-table">
        <thead>
          <tr>
            <th>Judul</th><th>Tahun</th><th>Unit</th><th>Status</th><th>Update</th><th>Aksi</th>
          </tr>
        </thead>
        <tbody>
          <?php foreach ($rows as $r): ?>
            <tr>
              <td>
                <a href="<?php echo esc_url(Url::page('detail', array('evidence_id'=>$r->id))); ?>">
                  <?php echo esc_html($r->title); ?>
                </a>
              </td>
              <td><?php echo esc_html($r->year); ?></td>
              <td><?php echo esc_html($r->unit_code); ?></td>
              <td><span class="sp-status-badge sp-status-<?php echo esc_attr($r->status); ?>"><?php echo esc_html($r->status); ?></span></td>
              <td><?php echo esc_html($r->updated_at); ?></td>
              <td>
                <?php if (in_array($r->status, array('DRAFT','REJECTED'), true)): ?>
                  <div class="sp-action-group">
                    <a href="<?php echo esc_url(Url::page('detail', array('evidence_id'=>$r->id,'mode'=>'edit'))); ?>" 
                       class="sp-action-btn" title="Edit">
                      <span class="dashicons dashicons-edit"></span>
                    </a>

                    <form method="post" class="sp-action-form">
                      <?php wp_nonce_field('delete_evidence_' . $r->id); ?>
                      <input type="hidden" name="action" value="delete_evidence">
                      <input type="hidden" name="evidence_id" value="<?php echo (int)$r->id; ?>">
                      <button type="submit" class="sp-action-btn"
                        onclick="return confirm('Yakin hapus evidence ini?');">
                        <span class="dashicons dashicons-trash"></span>
                      </button>
                    </form>

                  </div>
                <?php else: ?>
                  —
                <?php endif; ?>
              </td>
            </tr>
          <?php endforeach; ?>
        </tbody>
      </table>
    </div>
  <?php endif; ?>
</section>

<?php include __DIR__ . '/layout-close.php'; ?>