<?php /** @var array $groups @var array $settings @var string $activeTab */ ?>
<?php $success = flash('success'); $error = flash('error'); ?>

<?php if ($activeTab === ''): ?>

  <?php /* ---------- Landing: card grid, one card per section ---------- */ ?>
  <div class="page-intro">
    <h1>Site Settings</h1>
    <p>Everything here updates your live website instantly. Pick a section to edit — each has its own <strong>Save</strong> button.</p>
  </div>

  <?php if ($success): ?><div class="alert alert-success">✅ <?= e($success) ?></div><?php endif; ?>
  <?php if ($error): ?><div class="alert alert-error">⚠️ <?= e($error) ?></div><?php endif; ?>

  <div class="settings-cards">
    <?php $i = 0; foreach ($groups as $key => $g): $i++; ?>
      <a class="settings-card tone-<?= $i % 4 === 1 ? 'green' : ($i % 4 === 2 ? 'orange' : ($i % 4 === 3 ? 'blue' : 'dark')) ?>"
         href="<?= url('/admin/settings') ?>?group=<?= e($key) ?>">
        <span class="settings-card-icon"><?= e($g['icon']) ?></span>
        <span class="settings-card-text">
          <strong><?= e($g['title']) ?></strong>
          <small><?= count($g['fields']) ?> field<?= count($g['fields']) === 1 ? '' : 's' ?></small>
        </span>
        <span class="settings-card-arrow" aria-hidden="true">→</span>
      </a>
    <?php endforeach; ?>
  </div>

<?php else: ?>

  <?php /* ---------- Section page: only this group's form ---------- */ ?>
  <?php $group = $groups[$activeTab]; ?>

  <div class="page-intro">
    <h1><?= e($group['icon']) ?> <?= e($group['title']) ?> <span class="settings-crumb">· Site Settings</span></h1>
    <p><?= e($group['intro']) ?></p>
  </div>

  <nav class="settings-quicknav" aria-label="Settings sections">
    <?php foreach ($groups as $key => $g): ?>
      <a class="chip <?= $key === $activeTab ? 'is-active' : '' ?>"
         href="<?= url('/admin/settings') ?>?group=<?= e($key) ?>"><?= e($g['icon'] . ' ' . $g['title']) ?></a>
    <?php endforeach; ?>
    <a class="chip chip-back" href="<?= url('/admin/settings') ?>">← All Sections</a>
  </nav>

  <?php if ($success): ?><div class="alert alert-success">✅ <?= e($success) ?></div><?php endif; ?>
  <?php if ($error): ?><div class="alert alert-error">⚠️ <?= e($error) ?></div><?php endif; ?>

  <section class="admin-panel settings-box" id="box-<?= e($activeTab) ?>">
    <form method="post" action="<?= url('/admin/settings') ?>?group=<?= e($activeTab) ?>"
          enctype="multipart/form-data" class="stack">
      <?= csrf_field() ?>
      <input type="hidden" name="group" value="<?= e($activeTab) ?>">

      <?php foreach ($group['fields'] as $fkey => $meta): ?>
        <?php [$label, $type, $help] = $meta; ?>
        <?php $value = $settings[$fkey] ?? ''; ?>

        <?php if ($type === 'bool'): ?>
          <div class="form-group setting-row">
            <label class="check setting-check" for="<?= e($fkey) ?>">
              <input type="checkbox" id="<?= e($fkey) ?>" name="<?= e($fkey) ?>" value="1"
                     <?php echo ($value === '1' || $value === '') ? 'checked' : ''; ?>>
              <span>
                <strong><?= e($label) ?></strong>
                <small><?= e($help) ?></small>
              </span>
            </label>
          </div>

        <?php elseif ($type === 'image'): ?>
          <div class="form-group setting-row">
            <label><?= e($label) ?></label>
            <div class="upload-row setting-upload">
              <?php if ($value !== ''): ?>
                <img class="thumb thumb-lg" src="<?= e(upload_url($value)) ?>" alt="<?= e($label) ?>">
                <input type="hidden" name="_current_<?= e($fkey) ?>" value="<?= e($value) ?>">
              <?php else: ?>
                <span class="thumb thumb-lg thumb-empty">No image</span>
              <?php endif; ?>
              <div class="upload-controls">
                <input type="file" name="<?= e($fkey) ?>" accept="image/*">
                <small><?= e($help) ?></small>
              </div>
            </div>
          </div>

        <?php else: ?>
          <?php
            $inputType = match ($type) {
                'email'    => 'email',
                'url'      => 'url',
                'number'   => 'number',
                'password' => 'password',
                default    => 'text',
            };
          ?>
          <div class="form-group setting-row">
            <label for="<?= e($fkey) ?>"><?= e($label) ?></label>
            <?php if ($type === 'textarea'): ?>
              <textarea id="<?= e($fkey) ?>" name="<?= e($fkey) ?>" rows="4"
                        placeholder="<?= e($label) ?>"><?= e($value) ?></textarea>
            <?php else: ?>
              <input type="<?= e($inputType) ?>" id="<?= e($fkey) ?>" name="<?= e($fkey) ?>"
                     value="<?= e($value) ?>" placeholder="<?= e($help) ?>"
                     <?php if ($type === 'password'): ?>autocomplete="new-password"<?php endif; ?>>
            <?php endif; ?>
            <small class="help-text"><?= e($help) ?></small>
          </div>
        <?php endif; ?>
      <?php endforeach; ?>

      <div class="save-bar">
        <button type="submit" class="btn btn-gold btn-lg">💾 Save <?= e($group['title']) ?></button>
        <span class="muted">Saves only this section.</span>
        <a class="btn btn-ghost btn-sm" href="<?= url('/admin/settings') ?>">← All Sections</a>
      </div>
    </form>
  </section>

<?php endif; ?>
