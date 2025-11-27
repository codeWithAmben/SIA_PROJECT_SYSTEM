<?php
// Generic manage page: pages/manage.php?file=notes|animals|crops|users|tasks
$allowed = ['animals','crops','users','tasks','notes'];
$file = isset($_GET['file']) ? preg_replace('/[^a-z]/', '', $_GET['file']) : '';
if (!$file || !in_array($file, $allowed)) {
  header('Location: ../index.php');
    exit;
}

$path = __DIR__ . '/../data/' . $file . '.xml';
libxml_use_internal_errors(true);
$xml = file_exists($path) ? simplexml_load_file($path) : null;

// Determine fields by schema mapping
$fieldsByFile = [
    'notes' => ['title' => 'Title','content' => 'Content'],
    'animals' => ['name' => 'Name','species' => 'Species','age' => 'Age'],
    'crops' => ['name' => 'Name','variety' => 'Variety','yield' => 'Yield'],
    'users' => ['name' => 'Name','role' => 'Role','email' => 'Email'],
    'tasks' => ['title' => 'Title','due' => 'Due Date','assigned_to' => 'Assigned To']
];
$fields = $fieldsByFile[$file] ?? ['name'=>'Name'];

// Render HTML
?><!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width,initial-scale=1">
  <title>Manage <?=htmlspecialchars(ucfirst($file))?></title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
      tailwind.config = {
        theme: {
          extend: {
            fontFamily: {
              montserrat: ['Montserrat','sans-serif'],
              poppins: ['Poppins','sans-serif'],
            },
            colors: {
              farm: { green: '#4ade80', dark: '#14532d', earth: '#78350f', light: '#f0fdf4' }
            }
          }
        }
      }
    </script>
    <!-- Google Fonts (local fallback: also included in CSS as font utility classes) -->
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;600;700&family=Poppins:wght@300;400;500&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="../css/style.css">
  <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
  <!-- Moved font family and container rules to css/style.css for consistency -->
</head>
<body>
<?php include __DIR__ . '/../php/auth.php'; require_admin(); ?>
<?php include __DIR__ . '/../php/partials/header.php'; ?>
  <div class="container mx-auto px-4">
    <div class="flex gap-6">
      <?php include __DIR__ . '/../php/partials/sidebar.php'; ?>
      <div class="flex-1">
  <div class="flex items-center justify-between mb-6">
    <a href="../index.php" class="text-sm text-gray-600 hover:text-gray-800">← Back to home</a>
    <div class="flex gap-2">
      <a class="text-sm text-gray-600" href="admin.php">Admin</a>
      <a class="text-sm text-gray-600" href="../php/export-data.php">Export data</a>
      <a class="text-sm text-red-500" href="../php/logout.php">Logout</a>
    </div>
  </div>
  <div class="glass-panel rounded-xl p-6">
    <div class="card-sheen"></div>
    <div class="card-inner">
    <h1 class="font-montserrat text-2xl font-bold text-farm-dark">Manage <?=htmlspecialchars(ucfirst($file))?></h1>

  <section>
    <h2>Existing entries</h2>
    <?php if (!$xml): ?>
      <p>No data file found. It will be created when you add content.</p>
    <?php else: ?>
      <?php
      $itemName = rtrim($file,'s');
      $items = $xml->{$itemName} ?? [];
      ?>
      <table class="table-auto w-full">
        <thead><tr>
          <th class="w-12">ID</th>
            <?php foreach ($fields as $k=>$label): ?><th><?=htmlspecialchars($label)?></th><?php endforeach; ?>
          <th class="w-24">Actions</th>
        </tr></thead>
        <tbody>
          <?php if (count($items)===0): ?><tr><td colspan="<?=1+count($fields)?>">No items yet.</td></tr><?php endif; ?>
          <?php foreach ($items as $it): ?>
          <tr data-id="<?=htmlspecialchars((string)$it->id)?>">
            <td><?=htmlspecialchars((string)$it->id)?></td>
            <?php foreach ($fields as $k=>$label): ?><td data-label="<?=htmlspecialchars($label)?>"><?=htmlspecialchars((string)$it->{$k})?></td><?php endforeach; ?>
            <td class="text-right">
              <button type="button" class="btn" onclick="openEditModal('<?=htmlspecialchars($file)?>', <?=htmlspecialchars((string)$it->id)?>)">Edit</button>
              <button type="button" class="btn secondary" onclick="deleteEntity('<?=htmlspecialchars($file)?>', <?=htmlspecialchars((string)$it->id)?>)">Delete</button>
            </td>
          </tr>
          <?php endforeach; ?>
        </tbody>
      </table>
    <?php endif; ?>
  </section>
  <!-- Edit Modal -->
  <div id="editModal" class="fixed inset-0 z-50 hidden" role="dialog" aria-modal="true">
    <div class="fixed inset-0 bg-gray-900 bg-opacity-80" onclick="closeModal('editModal')"></div>
    <div class="flex min-h-full items-center justify-center p-4">
      <div class="glass-panel rounded-lg p-4 max-w-xl w-full">
        <div class="card-sheen"></div>
        <div class="card-inner">
          <h3 class="font-bold mb-3">Edit Item</h3>
          <form id="editForm" class="space-y-4">
            <input type="hidden" name="file" value="<?=htmlspecialchars($file)?>">
            <input type="hidden" name="id" value="">
            <?php foreach ($fields as $k=>$label): ?>
              <div>
                <label class="form-label block mb-1"><?=htmlspecialchars($label)?></label>
                <?php if ($k === 'content'): ?>
                  <textarea name="<?=htmlspecialchars($k)?>" class="form-control" rows="3"></textarea>
                <?php else: ?>
                  <input type="text" name="<?=htmlspecialchars($k)?>" class="form-control">
                <?php endif; ?>
              </div>
            <?php endforeach; ?>
            <div class="flex justify-end gap-2">
              <button type="button" class="btn secondary" onclick="closeModal('editModal')">Cancel</button>
              <button type="submit" class="btn">Save</button>
            </div>
          </form>
        </div>
      </div>
    </div>
  </div>
  </div>

  <section>
    <h2 class="mt-4 mb-3">Add new <?=htmlspecialchars(rtrim(ucfirst($file),'s'))?></h2>
    <form method="post" action="../php/save-entity.php" class="space-y-4">
      <input type="hidden" name="file" value="<?=htmlspecialchars($file)?>">
      <?php foreach ($fields as $k=>$label): ?>
        <div class="form-row">
          <label for="<?=htmlspecialchars($k)?>" class="form-label block mb-1"><?=htmlspecialchars($label)?></label>
          <?php if ($k === 'content'): ?>
            <textarea name="<?=htmlspecialchars($k)?>" id="<?=htmlspecialchars($k)?>" rows="3" class="form-control" required></textarea>
          <?php else: ?>
            <input type="text" name="<?=htmlspecialchars($k)?>" id="<?=htmlspecialchars($k)?>" class="form-control" required>
          <?php endif; ?>
        </div>
      <?php endforeach; ?>
      <div class="flex gap-3">
        <button type="submit" class="btn">Save</button>
        <button type="reset" class="btn secondary">Reset</button>
      </div>
    </form>
  </section>
  </div>
  </div>

</div>
</div>
<?php include __DIR__ . '/../php/partials/footer.php'; ?>
<script src="../js/main.js" defer></script>
</body>
</html>
