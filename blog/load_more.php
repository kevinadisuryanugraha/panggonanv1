<?php
require_once __DIR__ . '/../config/db.php';

$offset = isset($_GET['offset']) ? (int) $_GET['offset'] : 0;
$limit = 6;

$stmt = $pdo->prepare("SELECT * FROM journals WHERE status = 'approved' ORDER BY id DESC LIMIT :limit OFFSET :offset");
$stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
$stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
$stmt->execute();

$journals = $stmt->fetchAll(PDO::FETCH_ASSOC);

if (empty($journals)) {
    exit;
}

foreach ($journals as $post): ?>
<div class="journal-card">
  <?php if ($post['media_type'] === 'video' && !empty($post['media_url'])): ?>
  <div class="journal-img-wrap" style="position: relative">
    <video src="../<?= htmlspecialchars($post['media_url']) ?>#t=0.001" preload="metadata" controls muted loop
      playsinline style="width: 100%; height: auto; display: block;" onplay="
          this.parentElement.querySelector('.vid-play-btn').style.opacity = '0';
          this.parentElement.querySelector('.vid-play-btn').style.pointerEvents = 'none';
        " onpause="
          this.parentElement.querySelector('.vid-play-btn').style.opacity = '1';
          this.parentElement.querySelector('.vid-play-btn').style.pointerEvents = 'auto';
        "></video>
    <div class="vid-play-btn" style="position: absolute; inset: 0; display: flex; align-items: center; justify-content: center; transition: opacity 0.3s ease; cursor: pointer;" onclick="this.parentElement.querySelector('video').play()">
      <div style="width: 64px; height: 64px; border-radius: 50%; background: rgba(0, 0, 0, 0.55); backdrop-filter: blur(6px); display: flex; align-items: center; justify-content: center; border: 2px solid rgba(212, 175, 55, 0.5);">
        <svg width="24" height="28" viewBox="0 0 24 28" fill="none"><path d="M3 1.5L22 14L3 26.5V1.5Z" fill="#d4af37" /></svg>
      </div>
    </div>
  </div>
  <?php elseif (!empty($post['media_url'])): ?>
  <div class="journal-img-wrap">
    <img src="../<?= htmlspecialchars($post['media_url']) ?>" loading="lazy" alt="Visual Panggonan" class="journal-img" decoding="async" />
  </div>
  <?php endif; ?>
  <div class="journal-content">
    <?php if (!empty($post['date_label'])): ?>
    <div class="journal-date"><?= htmlspecialchars($post['date_label']) ?></div>
    <?php else: ?>
    <div class="journal-date">Oleh <?= htmlspecialchars($post['author']) ?></div>
    <?php endif; ?>
    <div class="journal-text" style="font-size: 0.88rem">
      <?= $post['text'] ?>
    </div>
  </div>
</div>
<?php endforeach;
