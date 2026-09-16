<?php $files = $files ?? []; $tab = $tab ?? 'backup'; ?>
<?php if ($success = flash('success')): ?><div class="alert alert-success">✅ <?php echo e($success); ?></div><?php endif; ?>
<?php if ($error = flash('error')): ?><div class="alert alert-error">⚠️ <?php echo e($error); ?></div><?php endif; ?>

<!-- ============ Tabs ============ -->
<div class="tab-bar">
  <a href="<?php echo url('/admin/backup'); ?>?tab=backup" class="tab <?php echo $tab === 'backup' ? 'is-active' : ''; ?>">
    💾 Backup
  </a>
  <a href="<?php echo url('/admin/backup'); ?>?tab=restore" class="tab <?php echo $tab === 'restore' ? 'is-active' : ''; ?>">
    ♻️ Restore
  </a>
</div>

<?php if ($tab === 'backup'): ?>

  <!-- ================= BACKUP TAB ================= -->
  <div class="admin-grid-2">
    <div class="admin-panel">
      <div class="panel-top"><h2>📦 Create Backup</h2></div>
      <p class="muted">Download a complete <code>.sql</code> snapshot of the entire website database — settings, sliders, notices, events, teachers, gallery records and users.</p>

      <ul class="info-list">
        <li>🗄️ Database: <strong><?php echo e(config('db.name')); ?></strong></li>
        <li>📊 Current size: <strong><?php echo e($dbSize); ?></strong></li>
        <li>🗂️ Stored backups: <strong><?php echo count($files); ?></strong></li>
      </ul>

      <form method="post" action="<?php echo url('/admin/backup/create'); ?>">
        <?php echo csrf_field(); ?>
        <button type="submit" class="btn btn-gold btn-lg">💾 Backup Now</button>
      </form>
    </div>

    <div class="admin-panel">
      <div class="panel-top"><h2>🛡️ Good Practice</h2></div>
      <ul class="tick-list">
        <li>Take a backup <strong>before</strong> restoring any file.</li>
        <li>Download backups to your computer weekly.</li>
        <li>Keep at least one backup from before every major update.</li>
        <li>Backup files are stored in <code>storage/backups</code> — web access is blocked.</li>
      </ul>
    </div>
  </div>

  <div class="admin-panel">
    <div class="panel-top"><h2>🗂️ Saved Backups (<?php echo count($files); ?>)</h2></div>

    <?php if (!$files): ?>
      <p class="muted">No backups yet. Click “Backup Now” to create the first one.</p>
    <?php else: ?>
      <table class="table">
        <thead><tr><th>File</th><th>Size</th><th>Created</th><th class="th-actions">Actions</th></tr></thead>
        <tbody>
          <?php foreach ($files as $f): ?>
            <tr>
              <td><strong>📄 <?php echo e($f['name']); ?></strong></td>
              <td class="muted"><?php echo e(number_format((float) $f['size'] / 1024, 1)); ?> KB</td>
              <td class="muted"><?php echo e($f['date']); ?></td>
              <td class="row-actions">
                <a class="btn btn-sm btn-primary" href="<?php echo url('/admin/backup/download/' . $f['name']); ?>">⬇ Download</a>
                <form method="post" action="<?php echo url('/admin/backup/' . $f['name'] . '/delete'); ?>" class="inline-form" data-confirm="Delete backup <?php echo e($f['name']); ?>?">
                  <?php echo csrf_field(); ?>
                  <button type="submit" class="btn btn-sm btn-danger-ghost">🗑</button>
                </form>
              </td>
            </tr>
          <?php endforeach; ?>
        </tbody>
      </table>
    <?php endif; ?>
  </div>

<?php else: ?>

  <!-- ================= RESTORE TAB ================= -->
  <div class="alert alert-warn">
    ⚠️ <strong>Danger zone:</strong> restoring replaces <em>all current content</em> with the backup file.
    Create a fresh backup first — this action cannot be undone.
  </div>

  <div class="admin-grid-2">
    <!-- Restore from stored backup -->
    <div class="admin-panel">
      <div class="panel-top"><h2>♻️ Restore from Saved Backup</h2></div>

      <?php if (!$files): ?>
        <p class="muted">You have no saved backups yet. Create one in the <a href="<?php echo url('/admin/backup'); ?>?tab=backup">Backup tab</a>.</p>
      <?php else: ?>
        <form method="post" action="<?php echo url('/admin/backup/restore'); ?>" data-confirm="This will OVERWRITE all current website content with the selected backup. Continue?">
          <?php echo csrf_field(); ?>
          <input type="hidden" name="source" value="existing">
          <div class="form-group">
            <label for="file">Choose backup file</label>
            <select id="file" name="file">
              <?php foreach ($files as $f): ?>
                <option value="<?php echo e($f['name']); ?>"><?php echo e($f['name']); ?> — <?php echo e($f['date']); ?></option>
              <?php endforeach; ?>
            </select>
          </div>
          <div class="form-group">
            <label for="confirm_existing">Type <strong>RESTORE</strong> to confirm</label>
            <input type="text" id="confirm_existing" name="confirm" placeholder="RESTORE" required>
          </div>
          <button type="submit" class="btn btn-danger">♻️ Restore Now</button>
        </form>
      <?php endif; ?>
    </div>

    <!-- Restore from upload -->
    <div class="admin-panel">
      <div class="panel-top"><h2>📤 Restore from Uploaded File</h2></div>
      <p class="muted">Upload a <code>.sql</code> file previously downloaded from this panel (max <?php echo e((string) round((int) config('backup.max_upload') / 1048576)); ?> MB).</p>

      <form method="post" action="<?php echo url('/admin/backup/restore'); ?>" enctype="multipart/form-data"
            data-confirm="This will OVERWRITE all current website content with the uploaded file. Continue?">
        <?php echo csrf_field(); ?>
        <input type="hidden" name="source" value="upload">
        <div class="form-group">
          <label for="sql_file">SQL backup file</label>
          <input type="file" id="sql_file" name="sql_file" accept=".sql" required>
        </div>
        <div class="form-group">
          <label for="confirm_upload">Type <strong>RESTORE</strong> to confirm</label>
          <input type="text" id="confirm_upload" name="confirm" placeholder="RESTORE" required>
        </div>
        <button type="submit" class="btn btn-danger">📤 Upload &amp; Restore</button>
      </form>
    </div>
  </div>

  <div class="admin-panel">
    <div class="panel-top"><h2>ℹ️ How Restore Works</h2></div>
    <ul class="info-list">
      <li>The uploaded or selected SQL file is executed inside a single transaction — if any statement fails, nothing is changed.</li>
      <li>All current tables are dropped and rebuilt from the backup (backup files contain <code>DROP TABLE IF EXISTS</code> statements).</li>
      <li>Uploaded images in <code>public/uploads</code> are <strong>not</strong> part of SQL restore — keep your own copy of that folder.</li>
      <li>You stay logged in; the users table is restored exactly as it was in the backup.</li>
    </ul>
  </div>

<?php endif; ?>
