<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title><?= htmlspecialchars($pageTitle ?? APP_NAME, ENT_QUOTES, 'UTF-8') ?></title>
  <meta name="description" content="Student Registration Management System — Admin Dashboard">
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link rel="stylesheet" href="<?= BASE_URL ?>/assets/css/style.css">
  <link rel="icon" href="data:image/svg+xml,<svg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 100 100'><text y='.9em' font-size='90'>🎓</text></svg>">
</head>
<body>

<div id="toast-container" aria-live="polite" aria-atomic="true"></div>

<!-- ── Delete Confirmation Modal ─────────────────────────────── -->
<div id="delete-modal" class="modal-overlay" role="dialog" aria-modal="true" aria-labelledby="modal-title">
  <div class="modal-box">
    <div class="modal-icon">🗑️</div>
    <h3 id="modal-title">Delete Student?</h3>
    <p>You are about to permanently delete <strong id="delete-name">this student</strong>.<br>This action <strong>cannot be undone</strong>.</p>
    <div class="modal-actions">
      <button type="button" class="btn btn-secondary" onclick="closeDeleteModal()">Cancel</button>
      <form id="delete-form" method="POST" action="<?= BASE_URL ?>?action=delete" style="display:inline;">
        <input type="hidden" id="delete-id" name="id" value="">
        <button type="submit" class="btn btn-danger">Yes, Delete</button>
      </form>
    </div>
  </div>
</div>

<!-- ── Page Wrapper ───────────────────────────────────────────── -->
<div class="page-wrapper">

  <!-- ── Site Header (semantic <header>) ───────────────────────── -->
  <header class="site-header">
    <a href="<?= BASE_URL ?>" class="site-logo" style="text-decoration:none;">
      <div class="logo-icon" aria-hidden="true">🎓</div>
      <div>
        <h1><?= APP_NAME ?></h1>
        <span>Admin Dashboard &nbsp;·&nbsp; v<?= APP_VERSION ?></span>
      </div>
    </a>
    <nav class="header-actions" aria-label="Main navigation">
      <a href="<?= BASE_URL ?>" class="btn btn-secondary">🏠 Dashboard</a>
      <a href="<?= BASE_URL ?>?action=add" class="btn btn-primary">＋ Add Student</a>
    </nav>
  </header>

  <!-- ── Main Content (semantic <main>) ────────────────────────── -->
  <main>
