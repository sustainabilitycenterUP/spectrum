-- Master SDG (singkat)
CREATE TABLE wp_spectrum_sdg (
  id TINYINT UNSIGNED PRIMARY KEY,
  sdg_number TINYINT UNSIGNED NOT NULL UNIQUE,
  sdg_title VARCHAR(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Master Metric THE
CREATE TABLE wp_spectrum_metric (
  id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  sdg_number TINYINT UNSIGNED NOT NULL,        -- 1–17
  metric_code VARCHAR(10) NOT NULL,            -- mis. '1.2.1'
  metric_type ENUM('numeric','initiatives','policy') NOT NULL,
  metric_title VARCHAR(255) NOT NULL,
  metric_question TEXT NULL,
  metric_note LONGTEXT NULL,
  is_active_default TINYINT(1) NOT NULL DEFAULT 1,
  created_at DATETIME NULL,
  updated_at DATETIME NULL,
  INDEX idx_metric_sdg (sdg_number),
  INDEX idx_metric_code (metric_code),
  INDEX idx_metric_type (metric_type)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Konfigurasi per tahun pelaporan
CREATE TABLE wp_spectrum_year_metric (
  id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  year INT NOT NULL,
  metric_id BIGINT UNSIGNED NOT NULL,
  is_active TINYINT(1) NOT NULL DEFAULT 1,
  weight DECIMAL(5,2) NULL,
  created_at DATETIME NULL,
  updated_at DATETIME NULL,
  CONSTRAINT fk_year_metric_metric
    FOREIGN KEY (metric_id) REFERENCES wp_spectrum_metric(id)
    ON DELETE CASCADE,
  INDEX idx_year_metric (year, metric_id),
  INDEX idx_year_active (year, is_active)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;


CREATE TABLE IF NOT EXISTS wp_spectrum_evidence (
  id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  submitter_id BIGINT UNSIGNED NOT NULL,    -- wp_users.ID
  year INT NOT NULL,                        -- tahun pelaporan (mis. 2024, 2025, 2026)
  unit_code VARCHAR(100) NOT NULL,          -- fungsi / direktorat / fakultas (diambil dari user_meta)
  title VARCHAR(255) NOT NULL,
  summary TEXT NULL,                        -- ringkasan kegiatan
  justification TEXT NULL,                  -- justifikasi ke metrik
  status ENUM(
    'DRAFT',
    'SUBMITTED',
    'UNDER_REVIEW',
    'APPROVED',
    'NEED_REVISION',
    'REJECTED'
  ) NOT NULL DEFAULT 'DRAFT',
  submitted_at DATETIME NULL,
  last_reviewed_at DATETIME NULL,
  created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  updated_at DATETIME NULL ON UPDATE CURRENT_TIMESTAMP,
  INDEX idx_evidence_submitter (submitter_id),
  INDEX idx_evidence_year_status (year, status),
  INDEX idx_evidence_unit (unit_code)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;


CREATE TABLE IF NOT EXISTS wp_spectrum_evidence_metric (
  id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  evidence_id BIGINT UNSIGNED NOT NULL,
  metric_id BIGINT UNSIGNED NOT NULL,
  created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  CONSTRAINT fk_evmetric_evidence
    FOREIGN KEY (evidence_id) REFERENCES wp_spectrum_evidence(id)
    ON DELETE CASCADE,
  CONSTRAINT fk_evmetric_metric
    FOREIGN KEY (metric_id) REFERENCES wp_spectrum_metric(id)
    ON DELETE CASCADE,
  INDEX idx_evmetric_evidence (evidence_id),
  INDEX idx_evmetric_metric (metric_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;


CREATE TABLE IF NOT EXISTS wp_spectrum_attachment (
  id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  evidence_id BIGINT UNSIGNED NOT NULL,
  file_name VARCHAR(255) NOT NULL,
  file_path VARCHAR(500) NOT NULL,      -- path relatif di server
  file_type VARCHAR(100) NULL,          -- mime type, contoh: application/pdf
  file_size BIGINT UNSIGNED NULL,       -- dalam bytes
  created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  CONSTRAINT fk_att_evidence
    FOREIGN KEY (evidence_id) REFERENCES wp_spectrum_evidence(id)
    ON DELETE CASCADE,
  INDEX idx_att_evidence (evidence_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;


CREATE TABLE IF NOT EXISTS wp_spectrum_review (
  id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  evidence_id BIGINT UNSIGNED NOT NULL,
  reviewer_id BIGINT UNSIGNED NOT NULL,      -- wp_users.ID
  decision ENUM('APPROVED','NEED_REVISION','REJECTED') NOT NULL,
  notes TEXT NULL,
  created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  CONSTRAINT fk_review_evidence
    FOREIGN KEY (evidence_id) REFERENCES wp_spectrum_evidence(id)
    ON DELETE CASCADE,
  INDEX idx_review_evidence (evidence_id),
  INDEX idx_review_reviewer (reviewer_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;


CREATE TABLE IF NOT EXISTS wp_spectrum_status_log (
  id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  evidence_id BIGINT UNSIGNED NOT NULL,
  changed_by BIGINT UNSIGNED NOT NULL,       -- wp_users.ID
  old_status VARCHAR(50) NULL,
  new_status VARCHAR(50) NOT NULL,
  notes TEXT NULL,
  changed_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  CONSTRAINT fk_statuslog_evidence
    FOREIGN KEY (evidence_id) REFERENCES wp_spectrum_evidence(id)
    ON DELETE CASCADE,
  INDEX idx_statuslog_evidence (evidence_id),
  INDEX idx_statuslog_changed_by (changed_by)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;


CREATE TABLE IF NOT EXISTS wp_spectrum_reviewer_scope (
  id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  reviewer_id BIGINT UNSIGNED NOT NULL,      -- wp_users.ID
  sdg_number TINYINT UNSIGNED NULL,          -- boleh NULL kalau scope-nya per metric saja
  metric_id BIGINT UNSIGNED NULL,            -- boleh NULL kalau scope-nya per SDG
  unit_code VARCHAR(100) NULL,               -- NULL = semua unit
  created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  INDEX idx_scope_reviewer (reviewer_id),
  INDEX idx_scope_sdg (sdg_number),
  INDEX idx_scope_metric (metric_id),
  INDEX idx_scope_unit (unit_code)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;


