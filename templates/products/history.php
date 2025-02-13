<table class="table table-striped">
  <thead>
    <tr>
      <th scope="col">Price</th>
      <th scope="col">Created At</th>
    </tr>
  </thead>
  <tbody>
    <?php foreach ($history as $entry): ?>
      <tr>
        <td><?= htmlspecialchars($entry['price']) ?></td>
        <td><?= htmlspecialchars($entry['created_at']) ?></td>
      </tr>
    <?php endforeach; ?>
  </tbody>
</table>
