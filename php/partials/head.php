<?php
// Reusable head partial. Should be included at top of HTML <head>
if (session_status() === PHP_SESSION_NONE) session_start();
require_once __DIR__ . '/../csrf.php';
$csrf = htmlspecialchars(csrf_get_token(), ENT_QUOTES, 'UTF-8');
// compute path prefix relative to pages folder
$script = isset($_SERVER['SCRIPT_NAME']) ? $_SERVER['SCRIPT_NAME'] : '';
$pathPrefix = (strpos($script, '/pages/') !== false) ? '../' : '';
?>

  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width,initial-scale=1">
  <meta name="csrf-token" content="<?= $csrf ?>">
  <script>window.CSRF_TOKEN = '<?= $csrf ?>';</script>

  <!-- Tailwind + Theme config -->
  <script src="https://cdn.tailwindcss.com"></script>
  <script>
    tailwind.config = {
      theme: {
        extend: {
          fontFamily: { montserrat: ['Montserrat','sans-serif'], poppins: ['Poppins','sans-serif'] },
          colors: { farm: { green: '#4ade80', dark: '#14532d', earth: '#78350f', light: '#f0fdf4' } }
        }
      }
    }
  </script>

  <!-- Google Fonts -->
  <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;600;700&family=Poppins:wght@300;400;500&display=swap" rel="stylesheet">
  <!-- FontAwesome -->
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
  <!-- Site CSS -->
  <link rel="stylesheet" href="<?= $pathPrefix ?>css/style.css">

  <!-- Place for page title; pages should still set <title> after including this file -->
