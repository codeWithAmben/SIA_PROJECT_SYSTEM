<?php
// Simple admin landing page
$datasets = ['animals','crops','users','tasks','notes'];
?><!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width,initial-scale=1">
  <title>Admin — Simple Farm</title>
  <link href="https://cdn.tailwindcss.com" rel="stylesheet">
  <link rel="stylesheet" href="../css/style.css">
  <style>body{font-family:Poppins,Inter,system-ui,Arial}</style>
</head>
<body class="bg-farm-light/95 min-h-screen">
  <?php include __DIR__ . '/../php/auth.php'; require_admin(); ?>
  <?php include __DIR__ . '/../php/partials/header.php'; ?>
  <main class="max-w-4xl mx-auto mt-24 px-4">
    <div class="bg-white rounded-xl shadow p-6">
      <div class="flex items-center justify-between mb-4">
        <h1 class="font-montserrat text-2xl font-bold">Admin Dashboard</h1>
        <div class="flex items-center gap-3">
          <a href="../index.php" class="text-sm text-gray-500 hover:text-gray-800">View site</a>
          <a href="../php/logout.php" class="text-sm text-red-500 hover:text-red-700">Logout</a>
        </div>
      </div>

      <p class="text-gray-600 mb-4">Quick links to manage your datasets. Use the manager pages to add or edit records.</p>

      <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
        <?php foreach ($datasets as $ds): ?>
          <a href="manage.php?file=<?=$ds?>" class="block p-4 bg-farm-light rounded-lg border border-gray-100 hover:shadow-md transition">
            <div class="flex items-center justify-between">
              <div>
                <h3 class="font-bold text-lg capitalize"><?=htmlspecialchars($ds)?></h3>
                <p class="text-xs text-gray-500">Manage <?=$ds?> entries</p>
              </div>
              <div class="text-green-600 text-2xl"><i class="fa-solid fa-gear"></i></div>
            </div>
          </a>
        <?php endforeach; ?>
      </div>

      <div class="mt-6 border-t pt-4 text-sm text-gray-500">
        <p>Pro Tip: Use <a href="../php/load-data.php?file=notes" class="text-blue-600">`php/load-data.php`</a> to retrieve JSON for each dataset. Use the manager page to add new entries.</p>
      </div>
    </div>
  </main>
  <?php include __DIR__ . '/../php/partials/footer.php'; ?>
</body>
</html>
