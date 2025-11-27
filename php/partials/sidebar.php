<?php
// Simple reusable sidebar partial for admin pages
if (session_status() === PHP_SESSION_NONE) @session_start();
$script = isset($_SERVER['SCRIPT_NAME']) ? $_SERVER['SCRIPT_NAME'] : '';
$pathPrefix = (strpos($script, '/pages/') !== false) ? '../' : '';
$active = function($p){ return (strpos($_SERVER['REQUEST_URI'],$p)!==false) ? 'bg-farm-dark/10' : ''; };
?>
<aside class="hidden lg:block lg:w-64">
  <div class="p-4 space-y-4">
    <a href="<?= $pathPrefix ?>pages/admin.php" class="block p-3 rounded <?= $active('/pages/admin.php') ?> hover:bg-farm-light transition">Dashboard</a>
    <a href="<?= $pathPrefix ?>pages/manage.php?file=notes" class="block p-3 rounded <?= $active('file=notes') ?> hover:bg-farm-light transition">Notes</a>
    <a href="<?= $pathPrefix ?>pages/manage.php?file=animals" class="block p-3 rounded <?= $active('file=animals') ?> hover:bg-farm-light transition">Animals</a>
    <a href="<?= $pathPrefix ?>pages/manage.php?file=crops" class="block p-3 rounded <?= $active('file=crops') ?> hover:bg-farm-light transition">Crops</a>
    <a href="<?= $pathPrefix ?>pages/manage.php?file=users" class="block p-3 rounded <?= $active('file=users') ?> hover:bg-farm-light transition">Users</a>
    <a href="<?= $pathPrefix ?>pages/manage.php?file=tasks" class="block p-3 rounded <?= $active('file=tasks') ?> hover:bg-farm-light transition">Tasks</a>
    <a href="<?= $pathPrefix ?>php/export-data.php" class="block p-3 rounded hover:bg-farm-light transition">Export Data</a>
  </div>
</aside>
