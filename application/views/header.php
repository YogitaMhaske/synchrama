<!doctype html>
<html>
<head>
  <meta charset="utf-8">
  <title>Synchrama</title>
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <!-- Bootstrap CDN -->
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/css/bootstrap.min.css">
  <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/js/bootstrap.bundle.min.js"></script>
</head>
<body>
<nav class="navbar navbar-expand-lg navbar-light bg-light">
  <a class="navbar-brand" href="<?= site_url() ?>">Synchrama</a>
  <div class="ml-auto">
    <?php if($this->session->userdata('logged_in')): ?>
      <span class="mr-2">Hi, <?= htmlspecialchars($this->session->userdata('first_name')) ?></span>
      <a href="<?= site_url('logout') ?>" class="btn btn-sm btn-outline-danger">Logout</a>
    <?php else: ?>
      <a href="<?= site_url('login') ?>" class="btn btn-sm btn-outline-primary">Login</a>
      <a href="<?= site_url('register') ?>" class="btn btn-sm btn-outline-success">Register</a>
    <?php endif; ?>
  </div>
</nav>
<div class="container mt-4">
