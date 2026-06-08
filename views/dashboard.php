<?php
/**
 * Dashboard View
 *
 * Variables injected by StudentController::list():
 *   $students   (array)  — paginated student rows
 *   $total      (int)    — total matching records
 *   $totalPages (int)    — total pages for pagination
 *   $stats      (array)  — ['total','male','female','recent']
 *   $search     (string) — current search term
 *   $page       (int)    — current page number
 *   $sortBy     (string) — active sort column
 *   $order      (string) — 'ASC' or 'DESC'
 *
 * Helper functions (buildUrl, sortIcon, avatarUrl, initials, parseSkills)
 * are defined in config/helpers.php — no re-definition needed here.
 */

$safeSearch = htmlspecialchars($search, ENT_QUOTES, 'UTF-8');
?>

<!-- ── Statistics Cards (semantic <section>) ──────────────── -->
<section aria-label="Dashboard Statistics" class="stats-section">
  <div class="stats-grid">

    <div class="stat-card">
      <div class="stat-icon" aria-hidden="true">🎓</div>
      <div class="stat-info">
        <div class="stat-value"><?= number_format($stats['total']) ?></div>
        <div class="stat-label">Total Students</div>
      </div>
    </div>

    <div class="stat-card">
      <div class="stat-icon" aria-hidden="true">👨‍🎓</div>
      <div class="stat-info">
        <div class="stat-value"><?= number_format($stats['male']) ?></div>
        <div class="stat-label">Male Students</div>
      </div>
    </div>

    <div class="stat-card">
      <div class="stat-icon" aria-hidden="true">👩‍🎓</div>
      <div class="stat-info">
        <div class="stat-value"><?= number_format($stats['female']) ?></div>
        <div class="stat-label">Female Students</div>
      </div>
    </div>

    <div class="stat-card">
      <div class="stat-icon" aria-hidden="true">🆕</div>
      <div class="stat-info">
        <div class="stat-value"><?= number_format($stats['recent']) ?></div>
        <div class="stat-label">Added This Week</div>
      </div>
    </div>

  </div>
</section>

<!-- ── Student Records Card ───────────────────────────────── -->
<section aria-label="Student Records">
<div class="card">

  <!-- Card Header: title + search filter (PDF: search filter ABOVE the list) -->
  <div class="card-header">
    <div class="card-title">
      <span class="icon" style="background:var(--clr-indigo-100);" aria-hidden="true">📋</span>
      Student Records
      <span class="record-count">(<?= number_format($total) ?> total)</span>
    </div>

    <!-- Search Form — PDF: search by Name, Email, Phone Number, Country -->
    <form
      method="GET"
      action="<?= BASE_URL ?>"
      class="search-bar"
      role="search"
      id="search-form"
    >
      <input type="hidden" name="sort"  value="<?= htmlspecialchars($sortBy, ENT_QUOTES, 'UTF-8') ?>">
      <input type="hidden" name="order" value="<?= htmlspecialchars($order,  ENT_QUOTES, 'UTF-8') ?>">

      <div class="search-wrap">
        <span class="search-icon" aria-hidden="true">🔍</span>
        <input
          id="search-input"
          type="search"
          name="search"
          class="search-input"
          placeholder="Search by name, email, phone or country…"
          value="<?= $safeSearch ?>"
          autocomplete="off"
          aria-label="Search students"
        >
      </div>
      <button type="submit" class="btn btn-primary btn-sm">Search</button>
      <?php if ($search !== ''): ?>
        <a href="<?= BASE_URL ?>" class="btn btn-secondary btn-sm">✕ Clear</a>
      <?php endif; ?>
    </form>
  </div>

  <!-- Empty State -->
  <?php if (empty($students)): ?>
    <div class="empty-state">
      <div class="empty-icon" aria-hidden="true">
        <?= $search !== '' ? '🔍' : '🎓' ?>
      </div>
      <h3><?= $search !== '' ? 'No results found' : 'No students registered yet' ?></h3>
      <p>
        <?php if ($search !== ''): ?>
          No students match <strong>"<?= $safeSearch ?>"</strong>.
          Try a different name, email, phone number or country.
        <?php else: ?>
          Get started by registering your first student.
        <?php endif; ?>
      </p>
      <?php if ($search === ''): ?>
        <a href="<?= BASE_URL ?>?action=add" class="btn btn-primary" style="margin-top:.75rem;">
          ＋ Add First Student
        </a>
      <?php endif; ?>
    </div>

  <?php else: ?>

    <!-- Student Data Table (PDF: display submitted student data in table format) -->
    <div class="table-wrap">
      <table class="data-table" role="grid" aria-label="Student Records">
        <thead>
          <tr>
            <!-- Sortable columns -->
            <th scope="col">
              <a href="<?= buildUrl(['sort'=>'full_name','order'=>($sortBy==='full_name'&&$order==='ASC')?'DESC':'ASC','page'=>1]) ?>">
                Name <?= sortIcon('full_name', $sortBy, $order) ?>
              </a>
            </th>
            <th scope="col">Email</th>
            <th scope="col">
              <a href="<?= buildUrl(['sort'=>'phone','order'=>($sortBy==='phone'&&$order==='ASC')?'DESC':'ASC','page'=>1]) ?>">
                Phone <?= sortIcon('phone', $sortBy, $order) ?>
              </a>
            </th>
            <th scope="col">
              <a href="<?= buildUrl(['sort'=>'gender','order'=>($sortBy==='gender'&&$order==='ASC')?'DESC':'ASC','page'=>1]) ?>">
                Gender <?= sortIcon('gender', $sortBy, $order) ?>
              </a>
            </th>
            <th scope="col">
              <a href="<?= buildUrl(['sort'=>'country','order'=>($sortBy==='country'&&$order==='ASC')?'DESC':'ASC','page'=>1]) ?>">
                Country <?= sortIcon('country', $sortBy, $order) ?>
              </a>
            </th>
            <th scope="col">Skills</th>
            <th scope="col">
              <a href="<?= buildUrl(['sort'=>'created_at','order'=>($sortBy==='created_at'&&$order==='ASC')?'DESC':'ASC','page'=>1]) ?>">
                Registered <?= sortIcon('created_at', $sortBy, $order) ?>
              </a>
            </th>
            <th scope="col" style="text-align:right;">Actions</th>
          </tr>
        </thead>
        <tbody>
          <?php foreach ($students as $s):
            $imgUrl    = avatarUrl($s['profile_image']);
            $ini       = initials($s['full_name']);
            $skillArr  = parseSkills($s['skills'] ?? '');
            $genderCls = match($s['gender']) {
              'Male'   => 'badge-male',
              'Female' => 'badge-female',
              default  => 'badge-other',
            };
          ?>
          <tr>

            <!-- Name + Avatar -->
            <td data-label="Name">
              <div class="student-cell">
                <?php if ($imgUrl): ?>
                  <img
                    src="<?= $imgUrl ?>"
                    alt="<?= htmlspecialchars($s['full_name'], ENT_QUOTES, 'UTF-8') ?>"
                    class="avatar"
                    loading="lazy"
                  >
                <?php else: ?>
                  <div class="avatar-placeholder" aria-hidden="true">
                    <?= htmlspecialchars($ini, ENT_QUOTES, 'UTF-8') ?>
                  </div>
                <?php endif; ?>
                <span class="student-name">
                  <?= htmlspecialchars($s['full_name'], ENT_QUOTES, 'UTF-8') ?>
                </span>
              </div>
            </td>

            <!-- Email -->
            <td data-label="Email">
              <a href="mailto:<?= htmlspecialchars($s['email'], ENT_QUOTES, 'UTF-8') ?>"
                 class="email-link">
                <?= htmlspecialchars($s['email'], ENT_QUOTES, 'UTF-8') ?>
              </a>
            </td>

            <!-- Phone -->
            <td data-label="Phone">
              <?= htmlspecialchars($s['phone'], ENT_QUOTES, 'UTF-8') ?>
            </td>

            <!-- Gender badge -->
            <td data-label="Gender">
              <span class="badge <?= $genderCls ?>">
                <?= htmlspecialchars($s['gender'], ENT_QUOTES, 'UTF-8') ?>
              </span>
            </td>

            <!-- Country -->
            <td data-label="Country">
              <?= htmlspecialchars($s['country'], ENT_QUOTES, 'UTF-8') ?>
            </td>

            <!-- Skills chips (max 3 shown + overflow count) -->
            <td data-label="Skills">
              <?php if (!empty($skillArr)): ?>
                <div class="skills-chips">
                  <?php foreach (array_slice($skillArr, 0, 3) as $skill): ?>
                    <span class="chip">
                      <?= htmlspecialchars($skill, ENT_QUOTES, 'UTF-8') ?>
                    </span>
                  <?php endforeach; ?>
                  <?php if (count($skillArr) > 3): ?>
                    <span class="chip chip-more">+<?= count($skillArr) - 3 ?></span>
                  <?php endif; ?>
                </div>
              <?php else: ?>
                <span style="color:var(--clr-text-light); font-size:.8rem;">—</span>
              <?php endif; ?>
            </td>

            <!-- Registration date -->
            <td data-label="Registered">
              <?= htmlspecialchars(date('d M Y', strtotime($s['created_at'])), ENT_QUOTES, 'UTF-8') ?>
            </td>

            <!-- Actions: View / Edit / Delete (PDF: CRUD operations) -->
            <td data-label="Actions">
              <div class="actions-cell" style="justify-content:flex-end;">
                <a href="<?= BASE_URL ?>?action=view&id=<?= (int)$s['id'] ?>"
                   class="btn btn-secondary btn-icon btn-sm"
                   title="View Profile">👁</a>
                <a href="<?= BASE_URL ?>?action=edit&id=<?= (int)$s['id'] ?>"
                   class="btn btn-secondary btn-icon btn-sm"
                   title="Edit Student">✏️</a>
                <button
                  type="button"
                  onclick="confirmDelete(<?= (int)$s['id'] ?>, '<?= htmlspecialchars(addslashes($s['full_name']), ENT_QUOTES, 'UTF-8') ?>')"
                  class="btn btn-danger btn-icon btn-sm"
                  title="Delete Student">🗑️</button>
              </div>
            </td>

          </tr>
          <?php endforeach; ?>
        </tbody>
      </table>
    </div>

    <!-- ── Pagination ──────────────────────────────────────── -->
    <?php if ($totalPages > 1):
      $from = (($page - 1) * RECORDS_PER_PAGE) + 1;
      $to   = min($page * RECORDS_PER_PAGE, $total);
    ?>
    <nav class="pagination" aria-label="Table pagination">
      <div class="pagination-info">
        Showing <strong><?= $from ?></strong>–<strong><?= $to ?></strong>
        of <strong><?= number_format($total) ?></strong> students
      </div>
      <div class="pagination-pages">

        <a class="page-link <?= $page <= 1 ? 'disabled' : '' ?>"
           href="<?= buildUrl(['page' => 1]) ?>" aria-label="First page">«</a>
        <a class="page-link <?= $page <= 1 ? 'disabled' : '' ?>"
           href="<?= buildUrl(['page' => $page - 1]) ?>" aria-label="Previous page">‹ Prev</a>

        <?php
          $pages = [];
          for ($i = 1; $i <= $totalPages; $i++) {
              if ($i === 1 || $i === $totalPages || abs($i - $page) <= 2) {
                  $pages[] = $i;
              }
          }
          $prev = null;
          foreach ($pages as $p):
            if ($prev !== null && $p - $prev > 1): ?>
              <span class="page-link disabled" aria-hidden="true">…</span>
            <?php endif; ?>
            <a class="page-link <?= $p === $page ? 'active' : '' ?>"
               href="<?= buildUrl(['page' => $p]) ?>"
               <?= $p === $page ? 'aria-current="page"' : '' ?>>
              <?= $p ?>
            </a>
          <?php $prev = $p; endforeach; ?>

        <a class="page-link <?= $page >= $totalPages ? 'disabled' : '' ?>"
           href="<?= buildUrl(['page' => $page + 1]) ?>" aria-label="Next page">Next ›</a>
        <a class="page-link <?= $page >= $totalPages ? 'disabled' : '' ?>"
           href="<?= buildUrl(['page' => $totalPages]) ?>" aria-label="Last page">»</a>

      </div>
    </nav>
    <?php endif; ?>

  <?php endif; ?>

</div>
</section>
