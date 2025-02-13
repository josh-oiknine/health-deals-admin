<!-- Price History Table -->
<table class="table table-striped">
  <thead>
    <tr>
      <th scope="col">Price</th>
      <th scope="col">Created At</th>
    </tr>
  </thead>
  <tbody>
    <?php if (empty($history)): ?>
      <tr>
        <td colspan="2" class="text-center">No price history available</td>
      </tr>
    <?php else: ?>
      <?php foreach ($history as $entry): ?>
        <tr>
          <td>$<?= number_format($entry['price'], 2) ?></td>
          <td><?= date('Y-m-d H:i:s', strtotime($entry['created_at'])) ?></td>
        </tr>
      <?php endforeach; ?>
    <?php endif; ?>
  </tbody>
</table>
