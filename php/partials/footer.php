<?php
if (session_status() === PHP_SESSION_NONE) @session_start();
$script = isset($_SERVER['SCRIPT_NAME']) ? $_SERVER['SCRIPT_NAME'] : '';
$pathPrefix = (strpos($script, '/pages/') !== false) ? '../' : '';
?>
<footer class="bg-gray-900/90 text-gray-400 py-12 text-center backdrop-blur-sm mt-12">
    <div class="flex justify-center gap-6 mb-8">
        <a href="#" class="hover:text-white transition"><i class="fa-brands fa-twitter text-xl"></i></a>
        <a href="#" class="hover:text-white transition"><i class="fa-brands fa-facebook text-xl"></i></a>
    </div>
    <p class="font-poppins text-sm">&copy; <?= date('Y') ?> Simple Farm System.</p>
    <div class="mt-3 text-xs text-gray-500">
        <a href="<?= $pathPrefix ?>php/export-data.php" class="text-gray-400 hover:text-white">Export data</a>
        <?php if (isset($_SESSION['admin_logged']) && $_SESSION['admin_logged'] === true): ?>
            &nbsp;·&nbsp; <a href="<?= $pathPrefix ?>pages/admin.php" class="text-gray-400 hover:text-white">Admin</a>
            &nbsp;·&nbsp; <a href="<?= $pathPrefix ?>php/logout.php" class="text-red-400 hover:text-white">Logout</a>
        <?php endif; ?>
    </div>
</footer>
