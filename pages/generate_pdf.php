<?php
require_once '/vendor/autoload.php';  // ✅ correct path based on where you extracted dompdf


use Dompdf\Dompdf;

$conn = mysqli_connect("localhost", "root", "", "shopping");
if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}

function safe($str) {
    return htmlspecialchars($str ?? 'N/A');
}
function formatCurrency($amount) {
    return "₹" . number_format($amount, 2);
}
function formatDate($dateStr) {
    return date("d-m-Y", strtotime($dateStr));
}

$client = mysqli_fetch_assoc(mysqli_query($conn, "SELECT * FROM clients ORDER BY id DESC LIMIT 1"));
$products = mysqli_query($conn, "SELECT * FROM products");

// Start output buffering
ob_start();
?>

<!-- Your existing HTML (use inline styles or minimal CSS) -->
<h2>Estimate</h2>
<p><strong>Name:</strong> <?= safe($client['name']) ?></p>
<p><strong>Number:</strong> <?= safe($client['number']) ?></p>
<p><strong>Event:</strong> <?= safe($client['event']) ?></p>
<p><strong>Location:</strong> <?= safe($client['location']) ?></p>
<p><strong>Date:</strong> <?= formatDate($client['date']) ?> | <strong>Time:</strong> <?= safe($client['time']) ?></p>

<table border="1" cellspacing="0" cellpadding="5" width="100%">
  <thead>
    <tr>
      <th>Material</th>
      <th>Details</th>
      <th>Price</th>
    </tr>
  </thead>
  <tbody>
    <?php 
      $total = 0;
      while ($row = mysqli_fetch_assoc($products)):
        $total += $row['price'];
    ?>
    <tr>
      <td><?= safe($row['material']) ?></td>
      <td><?= safe($row['details']) ?></td>
      <td><?= formatCurrency($row['price']) ?></td>
    </tr>
    <?php endwhile; ?>
    <tr>
      <td colspan="2"><strong>Total</strong></td>
      <td><strong><?= formatCurrency($total) ?></strong></td>
    </tr>
  </tbody>
</table>

<?php
$html = ob_get_clean();

// Generate PDF
$dompdf = new Dompdf();
$dompdf->loadHtml($html);
$dompdf->setPaper('A4', 'portrait');
$dompdf->render();
$dompdf->stream("Estimate.pdf", array("Attachment" => false)); // Set to true to download
exit;
