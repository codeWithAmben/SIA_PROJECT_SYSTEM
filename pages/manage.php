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
  <link href="https://cdn.tailwindcss.com" rel="stylesheet">
  <link rel="stylesheet" href="../css/style.css">
  <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
  <style>body{font-family:Poppins,Inter,system-ui,Arial}.container{max-width:1200px;margin:0 auto}</style>
</head>
<body>
<?php include __DIR__ . '/../php/auth.php'; require_admin(); ?>
<?php include __DIR__ . '/../php/partials/header.php'; ?>
<div class="container mx-auto px-4">
  <div class="flex items-center justify-between mb-6">
    <a href="../index.php" class="text-sm text-gray-600 hover:text-gray-800">← Back to home</a>
    <div class="flex gap-2">
      <a class="text-sm text-gray-600" href="admin.php">Admin</a>
      <a class="text-sm text-gray-600" href="../php/export-data.php">Export data</a>
      <a class="text-sm text-red-500" href="../php/logout.php">Logout</a>
    </div>
  </div>
  <h1 class="font-montserrat text-2xl font-bold">Manage <?=htmlspecialchars(ucfirst($file))?></h1>

  <section>
    <h2>Existing entries</h2>
    <?php if (!$xml): ?>
      <p>No data file found. It will be created when you add content.</p>
    <?php else: ?>
      <?php
      $itemName = rtrim($file,'s');
      $items = $xml->{$itemName} ?? [];
      ?>
      <table>
        <thead><tr>
          <th>ID</th>
          <?php foreach ($fields as $k=>$label): ?><th><?=htmlspecialchars($label)?></th><?php endforeach; ?>
        </tr></thead>
        <tbody>
          <?php if (count($items)===0): ?><tr><td colspan="<?=1+count($fields)?>">No items yet.</td></tr><?php endif; ?>
          <?php foreach ($items as $it): ?>
          <tr>
            <td><?=htmlspecialchars((string)$it->id)?></td>
            <?php foreach ($fields as $k=>$label): ?><td><?=htmlspecialchars((string)$it->{$k})?></td><?php endforeach; ?>
          </tr>
          <?php endforeach; ?>
        </tbody>
      </table>
    <?php endif; ?>
  </section>

  <section>
    <h2>Add new <?=htmlspecialchars(rtrim(ucfirst($file),'s'))?></h2>
    <form method="post" action="../php/save-entity.php">
      <input type="hidden" name="file" value="<?=htmlspecialchars($file)?>">
      <?php foreach ($fields as $k=>$label): ?>
        <div style="margin-bottom:8px">
          <label for="<?=htmlspecialchars($k)?>"><?=htmlspecialchars($label)?></label><br>
          <?php if ($k === 'content'): ?>
            <textarea name="<?=htmlspecialchars($k)?>" id="<?=htmlspecialchars($k)?>" rows="3" style="width:100%" required></textarea>
          <?php else: ?>
            <input type="text" name="<?=htmlspecialchars($k)?>" id="<?=htmlspecialchars($k)?>" style="width:100%" required>
          <?php endif; ?>
        </div>
      <?php endforeach; ?>
      <button type="submit">Save</button>
    </form>
  </section>

</div>
</div>
<?php include __DIR__ . '/../php/partials/footer.php'; ?>
</body>
</html>
