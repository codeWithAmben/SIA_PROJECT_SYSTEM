<?php
// Reusable nav/header partial
// Computes path prefix for including correct relative links
// Expect session to be started by the including file
if (session_status() === PHP_SESSION_NONE) @session_start();
$script = isset($_SERVER['SCRIPT_NAME']) ? $_SERVER['SCRIPT_NAME'] : '';
$pathPrefix = (strpos($script, '/pages/') !== false) ? '../' : '';
$isIndex = basename($script) === 'index.php';
$userName = isset($_SESSION['user']) ? htmlspecialchars($_SESSION['user']['name']) : null;
$isAdmin = isset($_SESSION['admin_logged']) && $_SESSION['admin_logged'] === true;
?>
<nav class="glass-header fixed w-full z-50" aria-label="Main Navigation">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between h-16 items-center">
            <div class="flex-shrink-0 flex items-center gap-2">
                <i class="fa-solid fa-tractor text-farm-dark text-2xl"></i>
                <a href="<?= $pathPrefix ?>index.php" class="font-montserrat font-bold text-xl text-farm-dark">GreenAcres</a>
            </div>
            <div class="hidden md:flex space-x-8">
                <a href="<?= $pathPrefix ?>index.php#home" class="text-gray-600 hover:text-farm-dark font-medium transition">Home</a>
                <a href="<?= $pathPrefix ?>index.php#map" class="text-gray-600 hover:text-farm-dark font-medium transition">Farm Map</a>
                <a href="<?= $pathPrefix ?>index.php#notes" class="text-gray-600 hover:text-farm-dark font-medium transition">Notes</a>
                <a href="<?= $pathPrefix ?>pages/admin.php" class="text-gray-600 hover:text-farm-dark font-medium transition">Admin</a>
                <a href="<?= $pathPrefix ?>pages/manage.php?file=notes" class="text-gray-600 hover:text-farm-dark font-medium transition">Manage</a>
            </div>
            <div class="flex items-center gap-3">
                <?php if ($userName): ?>
                    <span class="hidden sm:inline-block text-sm text-gray-600">Signed in as <strong class="text-farm-dark"><?= $userName ?></strong></span>
                    <a href="<?= $pathPrefix ?>php/logout.php" class="text-sm text-red-500 hover:text-red-700">Logout</a>
                    <?php if ($isAdmin): ?>
                        <button id="themeToggle" onclick="toggleAdminTheme()" class="ml-3 px-2 py-1 border rounded text-sm bg-white/20 hover:bg-white/30">Theme</button>
                    <?php endif; ?>
                <?php else: ?>
                    <?php if ($isIndex): ?>
                        <button onclick="openAuthModal(); return false;" class="flex items-center gap-2 bg-white border border-gray-300 text-gray-700 px-4 py-2 rounded-full shadow-sm hover:bg-gray-50 hover:shadow-md transition font-medium text-sm">
                            <i class="fa-solid fa-user-circle text-gray-400"></i>
                            Login / Register
                        </button>
                    <?php else: ?>
                        <a href="<?= $pathPrefix ?>index.php?auth=1" class="flex items-center gap-2 bg-white border border-gray-300 text-gray-700 px-4 py-2 rounded-full shadow-sm hover:bg-gray-50 hover:shadow-md transition font-medium text-sm">
                            <i class="fa-solid fa-user-circle text-gray-400"></i>
                            Login / Register
                        </a>
                    <?php endif; ?>
                <?php endif; ?>
            </div>
        </div>
    </div>
</nav>

<div class="h-16"></div>

<?php // flash area displayed after redirect; only show if set ?>
<?php if (isset($_SESSION['flash'])): ?>
    <div class="fixed top-6 left-1/2 -translate-x-1/2 bg-green-600 text-white px-4 py-2 rounded shadow z-50">
        <?= htmlspecialchars($_SESSION['flash']); unset($_SESSION['flash']); ?>
    </div>
<?php endif; ?>
