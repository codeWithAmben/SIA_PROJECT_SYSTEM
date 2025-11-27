<?php
// Simple admin landing page
$datasets = ['animals','crops','users','tasks','notes'];
?><!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width,initial-scale=1">
  <title>Admin — Simple Farm</title>
  <script src="https://cdn.tailwindcss.com"></script>
  <script>
    tailwind.config = {
      theme: {
        extend: {
          fontFamily: {
            montserrat: ['Montserrat','sans-serif'],
            poppins: ['Poppins','sans-serif'],
          },
          colors: { farm: { green: '#4ade80', dark: '#14532d', earth: '#78350f', light: '#f0fdf4' } }
        }
      }
    }
  </script>
  <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;600;700&family=Poppins:wght@300;400;500&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="../css/style.css">
  <!-- Fonts are included above and the local CSS (`css/style.css`) provides fallbacks -->
</head>
<body class="bg-farm-light/95 min-h-screen">
  <?php include __DIR__ . '/../php/auth.php'; require_admin(); ?>
  <?php include __DIR__ . '/../php/partials/header.php'; ?>
  <main class="max-w-7xl mx-auto mt-24 px-4">
    <div class="flex gap-6">
      <?php include __DIR__ . '/../php/partials/sidebar.php'; ?>
      <div class="flex-1">
      <!-- Left column: main content -->
      <div class="lg:col-span-8">
        <div class="glass-panel rounded-xl p-6 dark">
          <div class="card-sheen"></div>
          <div class="card-inner">
            <div class="flex items-center justify-between mb-4">
              <h1 class="font-montserrat text-2xl font-bold">Admin Dashboard</h1>
              <div class="flex items-center gap-3">
                <a href="../index.php" class="text-sm text-gray-300 hover:text-white">View site</a>
                <a href="../php/logout.php" class="text-sm text-red-400 hover:text-white">Logout</a>
              </div>
            </div>

            <p class="text-gray-400 mb-4">Quick links to manage your datasets. Use the manager pages to add or edit records.</p>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
              <?php foreach ($datasets as $ds): ?>
                <a href="manage.php?file=<?=$ds?>" class="block p-6 glass-card rounded-lg border hover:shadow-lg transition">
                  <div class="flex items-center justify-between">
                    <div>
                      <h3 class="font-bold text-lg capitalize text-gray-900"><?=htmlspecialchars($ds)?></h3>
                      <p class="text-xs text-gray-500">Manage <?=$ds?> entries</p>
                    </div>
                    <div class="text-green-600 text-2xl"><i class="fa-solid fa-gear"></i></div>
                  </div>
                </a>
              <?php endforeach; ?>
            </div>

            <div class="mt-6 border-t pt-4 text-sm text-gray-400">
              <p>Pro Tip: Use <a href="../php/load-data.php?file=notes" class="text-blue-400">`php/load-data.php`</a> to retrieve JSON for each dataset. Use the manager page to add new entries.</p>
            </div>
          </div>
        </div>
      </div>
      <!-- Right column: stats & actions -> small tiles -->
      <div class="lg:col-span-4">
        <div class="space-y-4">
          <div class="glass-card p-4 rounded-lg">
            <div class="flex items-center justify-between">
              <div>
                <p class="text-sm text-gray-500">Data Export</p>
                <p class="font-bold">Download XML Archive</p>
              </div>
              <div>
                <a href="../php/export-data.php" class="btn">Export</a>
              </div>
            </div>
          </div>
          <div class="glass-card p-4 rounded-lg">
            <div class="flex items-center justify-between">
              <div>
                <p class="text-sm text-gray-500">Theme</p>
                <p class="font-bold">Admin Theme</p>
              </div>
              <div>
                <?php if ($isAdmin): ?>
                  <button id="themeToggleSmall" onclick="toggleAdminTheme()" class="btn">Toggle</button>
                <?php endif; ?>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
    </div>
  </main>
  <?php include __DIR__ . '/../php/partials/footer.php'; ?>
  <script src="../js/main.js" defer></script>
</body>
</html>
