<?php $title = 'Gallery'; ?>

<section class="page-head">
  <div class="container">
    <h1>Gallery</h1>
    <p>Moments from campus life</p>
  </div>
</section>

<section class="section">
  <div class="container">
    <div class="filter-bar" id="galleryFilter">
      <button class="filter is-active" data-album="all">All</button>
      <?php foreach ($albums as $a): ?>
        <button class="filter" data-album="<?= (int) $a['id'] ?>"><?= e($a['title']) ?></button>
      <?php endforeach; ?>
    </div>

    <div class="gallery-grid" id="galleryGrid">
      <?php foreach ($albums as $a): ?>
        <?php foreach ($byAlbum[(int) $a['id']] ?? [] as $p): ?>
          <figure class="gallery-item" data-album="<?= (int) $a['id'] ?>">
            <img src="<?= e(upload_url($p['image'])) ?>" alt="<?= e($p['caption'] ?: $a['title']) ?>" loading="lazy">
            <figcaption><?= e($p['caption'] ?: $a['title']) ?> <small>— <?= e($a['title']) ?></small></figcaption>
          </figure>
        <?php endforeach; ?>
      <?php endforeach; ?>
    </div>

    <?php if (!$albums): ?>
      <p class="center-text muted">No photos have been added to the gallery yet.</p>
    <?php endif; ?>
  </div>
</section>

<!-- Lightbox -->
<div class="lightbox" id="lightbox" hidden>
  <button class="lightbox-close" id="lightboxClose" aria-label="Close">✕</button>
  <button class="lightbox-nav prev" id="lightboxPrev" aria-label="Previous">‹</button>
  <img src="" alt="" id="lightboxImg">
  <p id="lightboxCaption"></p>
  <button class="lightbox-nav next" id="lightboxNext" aria-label="Next">›</button>
</div>
