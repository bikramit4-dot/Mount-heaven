<?php $albums = $albums ?? []; $byAlbum = $byAlbum ?? []; ?>
<?php if ($success = flash('success')): ?><div class="alert alert-success">✅ <?php echo e($success); ?></div><?php endif; ?>
<?php if ($error = flash('error')): ?><div class="alert alert-error">⚠️ <?php echo e($error); ?></div><?php endif; ?>

<div class="admin-grid-2">
  <!-- Create album -->
  <form method="post" enctype="multipart/form-data" class="admin-panel" action="<?php echo url('/admin/gallery/albums'); ?>">
    <?php echo csrf_field(); ?>
    <div class="panel-top"><h2>➕ New Album</h2></div>
    <div class="form-group">
      <label for="album_title">Album Title *</label>
      <input type="text" id="album_title" name="title" required placeholder="e.g. Annual Day 2026">
    </div>
    <div class="form-row">
      <div class="form-group">
        <label for="cover">Cover Image</label>
        <input type="file" id="cover" name="cover" accept="image/*">
      </div>
      <div class="form-group">
        <label for="album_order">Sort Order</label>
        <input type="number" id="album_order" name="sort_order" value="0">
      </div>
    </div>
    <button type="submit" class="btn btn-primary">Create Album</button>
  </form>

  <!-- Upload photos -->
  <form method="post" enctype="multipart/form-data" class="admin-panel" action="<?php echo url('/admin/gallery/albums/0/photos'); ?>" id="photoUploadForm">
    <?php echo csrf_field(); ?>
    <div class="panel-top"><h2>📤 Upload Photos</h2></div>
    <div class="form-group">
      <label for="up_album">Choose Album *</label>
      <select id="up_album" name="album_id" required>
        <?php foreach ($albums as $a): ?>
          <option value="<?php echo (int) $a['id']; ?>"><?php echo e($a['title']); ?> (<?php echo count($byAlbum[(int) $a['id']] ?? []); ?> photos)</option>
        <?php endforeach; ?>
      </select>
    </div>
    <?php if (!$albums): ?><p class="muted">Create an album first.</p><?php endif; ?>
    <div class="form-group">
      <label for="photos">Photos * (JPG, PNG, GIF, WEBP — up to 5 MB each)</label>
      <input type="file" id="photos" name="photos[]" accept="image/*" multiple required>
    </div>
    <div class="form-group">
      <label for="caption">Caption (applied to all uploaded photos)</label>
      <input type="text" id="caption" name="caption">
    </div>
    <button type="submit" class="btn btn-primary" <?php echo $albums ? '' : 'disabled'; ?>>Upload Photos</button>
  </form>
</div>

<?php foreach ($albums as $a): ?>
  <div class="admin-panel">
    <div class="panel-top">
      <h2>📁 <?php echo e($a['title']); ?> <small class="muted">(<?php echo count($byAlbum[(int) $a['id']] ?? []); ?> photos)</small></h2>
      <form method="post" action="<?php echo url('/admin/gallery/albums/' . (int) $a['id'] . '/delete'); ?>" class="inline-form" data-confirm="Delete this album AND all its photos?">
        <?php echo csrf_field(); ?>
        <button type="submit" class="btn btn-sm btn-danger-ghost">🗑 Delete Album</button>
      </form>
    </div>

    <?php $photos = $byAlbum[(int) $a['id']] ?? []; ?>
    <?php if (!$photos): ?>
      <p class="muted">No photos in this album yet.</p>
    <?php else: ?>
      <div class="photo-grid">
        <?php foreach ($photos as $p): ?>
          <div class="photo-cell">
            <img src="<?php echo e(upload_url($p['image'])); ?>" alt="<?php echo e($p['caption']); ?>">
            <div class="photo-meta">
              <span><?php echo e(str_limit($p['caption'], 40)); ?></span>
              <form method="post" action="<?php echo url('/admin/gallery/photos/' . (int) $p['id'] . '/delete'); ?>" class="inline-form" data-confirm="Delete this photo?">
                <?php echo csrf_field(); ?>
                <button type="submit" class="photo-del" title="Delete photo">✕</button>
              </form>
            </div>
          </div>
        <?php endforeach; ?>
      </div>
    <?php endif; ?>
  </div>
<?php endforeach; ?>
