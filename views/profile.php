<?php
/**
 * Student Profile View (Read-Only)
 *
 * Variable injected by StudentController::view(): $student (array)
 * Helper functions (avatarUrl, initials, parseSkills) from config/helpers.php
 */

$imgUrl    = avatarUrl($student['profile_image'] ?? null);
$ini       = initials($student['full_name']);
$skills    = parseSkills($student['skills'] ?? '');

$genderCls = match($student['gender']) {
    'Male'   => 'badge-male',
    'Female' => 'badge-female',
    default  => 'badge-other',
};

// Calculate age from date_of_birth
$age = '';
if (!empty($student['date_of_birth'])) {
    $dob = new DateTime($student['date_of_birth']);
    $age = (new DateTime())->diff($dob)->y . ' years old';
}
?>

<section aria-label="Student Profile">
<div style="max-width:760px; margin:0 auto;">
<article class="card">

  <!-- ── Profile Header ──────────────────────────────────── -->
  <header class="profile-header">

    <?php if ($imgUrl): ?>
      <img
        src="<?= $imgUrl ?>"
        alt="Profile photo of <?= htmlspecialchars($student['full_name'], ENT_QUOTES, 'UTF-8') ?>"
        class="profile-avatar"
        loading="lazy"
      >
    <?php else: ?>
      <div class="profile-avatar-placeholder" aria-hidden="true">
        <?= htmlspecialchars($ini, ENT_QUOTES, 'UTF-8') ?>
      </div>
    <?php endif; ?>

    <div class="profile-meta" style="flex:1; min-width:0;">
      <h2><?= htmlspecialchars($student['full_name'], ENT_QUOTES, 'UTF-8') ?></h2>
      <div class="meta-sub">
        <a href="mailto:<?= htmlspecialchars($student['email'], ENT_QUOTES, 'UTF-8') ?>">
          <?= htmlspecialchars($student['email'], ENT_QUOTES, 'UTF-8') ?>
        </a>
      </div>
      <div style="display:flex; gap:.5rem; flex-wrap:wrap; margin-top:.55rem;">
        <span class="badge <?= $genderCls ?>">
          <?= htmlspecialchars($student['gender'], ENT_QUOTES, 'UTF-8') ?>
        </span>
        <?php if ($age): ?>
          <span class="badge" style="background:var(--clr-indigo-100); color:var(--clr-indigo-700);">
            🎂 <?= htmlspecialchars($age, ENT_QUOTES, 'UTF-8') ?>
          </span>
        <?php endif; ?>
        <span class="badge" style="background:var(--clr-blue-100); color:#1d4ed8;">
          🌍 <?= htmlspecialchars($student['country'], ENT_QUOTES, 'UTF-8') ?>
        </span>
      </div>
    </div>

    <div style="display:flex; gap:.6rem; align-self:flex-start; flex-wrap:wrap;">
      <a href="<?= BASE_URL ?>?action=edit&id=<?= (int)$student['id'] ?>"
         class="btn btn-secondary btn-sm">✏️ Edit</a>
      <button
        type="button"
        onclick="confirmDelete(<?= (int)$student['id'] ?>, '<?= htmlspecialchars(addslashes($student['full_name']), ENT_QUOTES, 'UTF-8') ?>')"
        class="btn btn-danger btn-sm">🗑️ Delete</button>
    </div>

  </header>

  <!-- ── Student Detail Fields ────────────────────────────── -->
  <div class="profile-details">

    <div class="detail-item">
      <div class="detail-label">📞 Phone Number</div>
      <div class="detail-value">
        <a href="tel:<?= htmlspecialchars($student['phone'], ENT_QUOTES, 'UTF-8') ?>" style="color:inherit;">
          <?= htmlspecialchars($student['phone'], ENT_QUOTES, 'UTF-8') ?>
        </a>
      </div>
    </div>

    <div class="detail-item">
      <div class="detail-label">🎂 Date of Birth</div>
      <div class="detail-value">
        <?= !empty($student['date_of_birth'])
            ? htmlspecialchars(date('d F Y', strtotime($student['date_of_birth'])), ENT_QUOTES, 'UTF-8')
            : '—' ?>
      </div>
    </div>

    <div class="detail-item">
      <div class="detail-label">🌍 Country</div>
      <div class="detail-value">
        <?= htmlspecialchars($student['country'], ENT_QUOTES, 'UTF-8') ?>
      </div>
    </div>

    <div class="detail-item">
      <div class="detail-label">🚻 Gender</div>
      <div class="detail-value">
        <span class="badge <?= $genderCls ?>">
          <?= htmlspecialchars($student['gender'], ENT_QUOTES, 'UTF-8') ?>
        </span>
      </div>
    </div>

    <div class="detail-item">
      <div class="detail-label">📅 Registered On</div>
      <div class="detail-value">
        <?= htmlspecialchars(date('d F Y', strtotime($student['created_at'])), ENT_QUOTES, 'UTF-8') ?>
      </div>
    </div>

    <div class="detail-item">
      <div class="detail-label">🔄 Last Updated</div>
      <div class="detail-value">
        <?= htmlspecialchars(date('d F Y, g:i A', strtotime($student['updated_at'])), ENT_QUOTES, 'UTF-8') ?>
      </div>
    </div>

    <?php if (!empty($student['address'])): ?>
    <div class="detail-item" style="grid-column:1/-1;">
      <div class="detail-label">📍 Address</div>
      <div class="detail-value" style="white-space:pre-line;">
        <?= htmlspecialchars($student['address'], ENT_QUOTES, 'UTF-8') ?>
      </div>
    </div>
    <?php endif; ?>

    <?php if (!empty($skills)): ?>
    <div class="detail-item" style="grid-column:1/-1;">
      <div class="detail-label">💡 Skills</div>
      <div style="display:flex; flex-wrap:wrap; gap:.4rem; margin-top:.5rem;">
        <?php foreach ($skills as $skill): ?>
          <span class="chip">
            <?= htmlspecialchars($skill, ENT_QUOTES, 'UTF-8') ?>
          </span>
        <?php endforeach; ?>
      </div>
    </div>
    <?php endif; ?>

  </div><!-- /.profile-details -->

  <!-- Footer actions -->
  <footer style="padding:1rem 1.75rem; border-top:1px solid var(--clr-border-s); display:flex; gap:.75rem; flex-wrap:wrap;">
    <a href="<?= BASE_URL ?>" class="btn btn-secondary btn-sm">← Back to List</a>
    <a href="<?= BASE_URL ?>?action=edit&id=<?= (int)$student['id'] ?>"
       class="btn btn-primary btn-sm">✏️ Edit Record</a>
  </footer>

</article>
</div>
</section>
