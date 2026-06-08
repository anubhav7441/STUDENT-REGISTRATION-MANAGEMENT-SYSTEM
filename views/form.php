<?php
/**
 * Add / Edit Student Form View
 *
 * PDF-Specified Field Types:
 *   Full Name     → type="text"        (required)
 *   Email Address → type="email"       (required)
 *   Phone Number  → type="number"      (required, 10 digits)
 *   Gender        → radio buttons      (required: Male/Female/Other)
 *   Date of Birth → type="date"        (required)
 *   Country       → <select> dropdown  (required)
 *   Skills        → checkboxes         (optional)
 *   Address       → <textarea>         (optional)
 *   Profile Image → type="file"        (optional, JPG/PNG/WEBP, max 2MB)
 *   Submit        → type="submit"
 *
 * Variables injected by StudentController:
 *   $isEdit  (bool)       — true when editing an existing record
 *   $student (array|null) — existing DB row on edit, null on add
 *   $old     (array)      — POST data to repopulate on validation failure
 *   $errors  (array)      — validation error messages keyed by field name
 *
 * Helper functions (fval, ferr, fcls, parseSkills) are defined in config/helpers.php
 */

// ── Parse currently selected skills ──────────────────────────
// $old['skills'] may be array (from POST) or string (from DB/re-join)
// parseSkills() handles both cases cleanly
$rawSkills      = $old['skills'] ?? $student['skills'] ?? '';
$selectedSkills = parseSkills($rawSkills);

// Available skill options for checkboxes
$availableSkills = [
    'PHP', 'MySQL', 'HTML', 'CSS', 'JavaScript',
    'React', 'Vue.js', 'Angular', 'Node.js', 'Python',
    'Java', 'Laravel', 'WordPress', 'Django', 'Docker',
    'Git', 'AWS', 'Figma', 'TypeScript', 'MongoDB',
];

// List of countries for dropdown
$countries = [
    'Afghanistan', 'Australia', 'Bangladesh', 'Brazil', 'Canada',
    'China', 'Egypt', 'France', 'Germany', 'Ghana',
    'India', 'Indonesia', 'Iran', 'Iraq', 'Italy',
    'Japan', 'Jordan', 'Kenya', 'Malaysia', 'Mexico',
    'Morocco', 'Myanmar', 'Nepal', 'Netherlands', 'Nigeria',
    'Pakistan', 'Philippines', 'Russia', 'Saudi Arabia', 'Singapore',
    'South Africa', 'South Korea', 'Spain', 'Sri Lanka', 'Sweden',
    'Thailand', 'Turkey', 'Ukraine', 'United Arab Emirates',
    'United Kingdom', 'United States', 'Vietnam', 'Zimbabwe',
];
?>

<section aria-label="<?= $isEdit ? 'Edit Student Form' : 'Student Registration Form' ?>">
<div style="max-width:900px; margin:0 auto;">
<div class="card">

  <!-- ── Card Header ────────────────────────────────────── -->
  <div class="card-header">
    <div class="card-title">
      <span class="icon" style="background:var(--clr-indigo-100);" aria-hidden="true">
        <?= $isEdit ? '✏️' : '➕' ?>
      </span>
      <?= $isEdit ? 'Edit Student Record' : 'Register New Student' ?>
    </div>
    <a href="<?= BASE_URL ?>" class="btn btn-secondary btn-sm">← Back to Dashboard</a>
  </div>

  <div class="card-body">

    <!-- Server-side error summary (PDF: validate all required fields) -->
    <?php if (!empty($errors)): ?>
    <div class="alert alert-danger" role="alert" aria-live="assertive">
      <span style="font-size:1.1rem; flex-shrink:0;" aria-hidden="true">⚠️</span>
      <div>
        <strong>Please fix the following errors:</strong>
        <ul style="margin:.45rem 0 0 1.1rem; font-size:.875rem; line-height:2;">
          <?php foreach ($errors as $err): ?>
            <li><?= htmlspecialchars($err, ENT_QUOTES, 'UTF-8') ?></li>
          <?php endforeach; ?>
        </ul>
      </div>
    </div>
    <?php endif; ?>

    <!-- ── Main Form (PDF: POST method, enctype for file upload) ── -->
    <form
      id="student-form"
      method="POST"
      action="<?= BASE_URL ?>?action=<?= $isEdit ? 'edit&id=' . (int)($student['id'] ?? 0) : 'add' ?>"
      enctype="multipart/form-data"
      novalidate
    >
      <div class="form-grid">

        <!-- 1. Full Name — type="text" (PDF requirement) -->
        <div class="form-group">
          <label class="form-label" for="full_name">
            Full Name <span class="req" aria-label="required">*</span>
          </label>
          <input
            id="full_name"
            type="text"
            name="full_name"
            class="form-control <?= fcls('full_name', $errors, $old ?? []) ?>"
            value="<?= fval('full_name', $old ?? [], $student) ?>"
            placeholder="Enter full name"
            required
            maxlength="120"
            autocomplete="name"
          >
          <div class="form-error" role="alert" aria-live="polite">
            <?= ferr('full_name', $errors) ?>
          </div>
        </div>

        <!-- 2. Email Address — type="email" (PDF requirement) -->
        <div class="form-group">
          <label class="form-label" for="email">
            Email Address <span class="req" aria-label="required">*</span>
          </label>
          <input
            id="email"
            type="email"
            name="email"
            class="form-control <?= fcls('email', $errors, $old ?? []) ?>"
            value="<?= fval('email', $old ?? [], $student) ?>"
            placeholder="Enter email address"
            required
            maxlength="180"
            autocomplete="email"
          >
          <div class="form-error" role="alert" aria-live="polite">
            <?= ferr('email', $errors) ?>
          </div>
        </div>

        <!-- 3. Phone Number — type="number" (PDF requirement) -->
        <div class="form-group">
          <label class="form-label" for="phone">
            Phone Number <span class="req" aria-label="required">*</span>
          </label>
          <input
            id="phone"
            type="number"
            name="phone"
            class="form-control <?= fcls('phone', $errors, $old ?? []) ?>"
            value="<?= fval('phone', $old ?? [], $student) ?>"
            placeholder="Enter 10-digit phone number"
            required
            min="1000000000"
            max="9999999999"
          >
          <div class="form-error" role="alert" aria-live="polite">
            <?= ferr('phone', $errors) ?>
          </div>
        </div>

        <!-- 4. Date of Birth — type="date" (PDF requirement) -->
        <div class="form-group">
          <label class="form-label" for="date_of_birth">
            Date of Birth <span class="req" aria-label="required">*</span>
          </label>
          <input
            id="date_of_birth"
            type="date"
            name="date_of_birth"
            class="form-control <?= fcls('date_of_birth', $errors, $old ?? []) ?>"
            value="<?= fval('date_of_birth', $old ?? [], $student) ?>"
            max="<?= date('Y-m-d') ?>"
            required
          >
          <div class="form-error" role="alert" aria-live="polite">
            <?= ferr('date_of_birth', $errors) ?>
          </div>
        </div>

        <!-- 5. Gender — Radio Buttons (PDF requirement) -->
        <div class="form-group">
          <fieldset>
            <legend class="form-label">
              Gender <span class="req" aria-label="required">*</span>
            </legend>
            <?php $selGender = fval('gender', $old ?? [], $student); ?>
            <div class="radio-group <?= isset($errors['gender']) ? 'group-invalid' : (!empty($old ?? []) && !isset($errors['gender']) ? 'group-valid' : '') ?>">
              <?php foreach (['Male', 'Female', 'Other'] as $g): ?>
                <label class="radio-label" for="gender_<?= strtolower($g) ?>">
                  <input
                    type="radio"
                    id="gender_<?= strtolower($g) ?>"
                    name="gender"
                    value="<?= $g ?>"
                    class="radio-input"
                    <?= $selGender === $g ? 'checked' : '' ?>
                    required
                  >
                  <span class="radio-custom" aria-hidden="true"></span>
                  <?= $g ?>
                </label>
              <?php endforeach; ?>
            </div>
            <div class="form-error" role="alert" aria-live="polite">
              <?= ferr('gender', $errors) ?>
            </div>
          </fieldset>
        </div>

        <!-- 6. Country — Select Dropdown (PDF requirement) -->
        <div class="form-group">
          <label class="form-label" for="country">
            Country <span class="req" aria-label="required">*</span>
          </label>
          <?php $selCountry = fval('country', $old ?? [], $student); ?>
          <select
            id="country"
            name="country"
            class="form-control <?= fcls('country', $errors, $old ?? []) ?>"
            required
          >
            <option value="" disabled <?= $selCountry === '' ? 'selected' : '' ?>>
              -- Select Country --
            </option>
            <?php foreach ($countries as $c): ?>
              <option
                value="<?= htmlspecialchars($c, ENT_QUOTES, 'UTF-8') ?>"
                <?= $selCountry === $c ? 'selected' : '' ?>
              >
                <?= htmlspecialchars($c, ENT_QUOTES, 'UTF-8') ?>
              </option>
            <?php endforeach; ?>
          </select>
          <div class="form-error" role="alert" aria-live="polite">
            <?= ferr('country', $errors) ?>
          </div>
        </div>

        <!-- 7. Skills — Checkboxes (PDF requirement) — full width -->
        <div class="form-group full">
          <fieldset>
            <legend class="form-label">Skills</legend>
            <div class="checkbox-grid">
              <?php foreach ($availableSkills as $skill):
                $checkId  = 'skill_' . htmlspecialchars(
                    strtolower(preg_replace('/[^a-z0-9]/i', '_', $skill)),
                    ENT_QUOTES, 'UTF-8'
                );
                $isChecked = in_array($skill, $selectedSkills, true);
              ?>
                <label class="checkbox-label" for="<?= $checkId ?>">
                  <input
                    type="checkbox"
                    id="<?= $checkId ?>"
                    name="skills[]"
                    value="<?= htmlspecialchars($skill, ENT_QUOTES, 'UTF-8') ?>"
                    class="checkbox-input"
                    <?= $isChecked ? 'checked' : '' ?>
                  >
                  <span class="checkbox-custom" aria-hidden="true"></span>
                  <?= htmlspecialchars($skill, ENT_QUOTES, 'UTF-8') ?>
                </label>
              <?php endforeach; ?>
            </div>
            <div class="form-hint">Select all skills that apply.</div>
          </fieldset>
        </div>

        <!-- 8. Address — Textarea (PDF requirement) — full width -->
        <div class="form-group full">
          <label class="form-label" for="address">Address</label>
          <textarea
            id="address"
            name="address"
            class="form-control"
            placeholder="Enter full address (street, city, state…)"
            rows="3"
            maxlength="1000"
          ><?= fval('address', $old ?? [], $student) ?></textarea>
        </div>

        <!-- 9. Profile Image — File Upload (PDF requirement) — full width -->
        <div class="form-group full">
          <label class="form-label" for="profile_image">Profile Image</label>

          <label class="file-label" for="profile_image">
            <?php $existingImg = $student['profile_image'] ?? null; ?>
            <img
              id="image-preview"
              src="<?= $existingImg ? htmlspecialchars(UPLOAD_URL . $existingImg, ENT_QUOTES, 'UTF-8') : '' ?>"
              class="file-preview"
              alt="Current profile image preview"
              style="display:<?= $existingImg ? 'block' : 'none' ?>;"
            >
            <span aria-hidden="true" style="font-size:1.3rem;">📷</span>
            <span class="file-name" id="file-name-label">
              <?= $existingImg
                  ? htmlspecialchars(basename($existingImg), ENT_QUOTES, 'UTF-8')
                  : 'Click to choose an image…' ?>
            </span>
          </label>

          <input
            id="profile_image"
            type="file"
            name="profile_image"
            accept=".jpg,.jpeg,.png,.webp,image/jpeg,image/png,image/webp"
            aria-label="Upload profile image"
          >
          <div class="form-hint">
            Accepted: JPG, JPEG, PNG, WEBP &nbsp;·&nbsp; Maximum size: 2 MB
          </div>
          <?php if (!empty($errors['profile_image'])): ?>
            <div class="form-error" role="alert">
              <?= ferr('profile_image', $errors) ?>
            </div>
          <?php endif; ?>
        </div>

      </div><!-- /.form-grid -->

      <!-- Submit Button (PDF requirement) -->
      <div class="form-actions">
        <button type="submit" class="btn btn-primary">
          <?= $isEdit ? '💾 Save Changes' : '✅ Register Student' ?>
        </button>
        <a href="<?= BASE_URL ?>" class="btn btn-secondary">Cancel</a>
      </div>

    </form>

  </div><!-- /.card-body -->
</div><!-- /.card -->
</div>
</section>
