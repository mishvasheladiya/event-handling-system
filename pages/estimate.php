<?php
$conn = mysqli_connect("localhost", "root", "", "shopping");
if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}

function getLatestClient($conn) {
    $result = mysqli_query($conn, "SELECT * FROM clients ORDER BY id DESC LIMIT 1");
    return mysqli_fetch_assoc($result);
}

function getAllProducts($conn) {
    return mysqli_query($conn, "SELECT * FROM products");
}

function formatDate($dateStr) {
    return date("d-m-Y", strtotime($dateStr));
}

function formatCurrency($amount) {
    return "₹" . number_format($amount, 2);
}

function safe($str) {
    return htmlspecialchars($str ?? 'N/A');
}

$client = getLatestClient($conn);
$products = getAllProducts($conn);
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Estimate</title>
  <style>
    body {
      font-family: 'Segoe UI', sans-serif;
      background: #f7f7f7;
      padding: 20px;
      margin: 0;
    }
    .estimate-wrapper {
      max-width: 900px;
      background: #fff;
      padding: 30px;
      margin: auto;
      border-radius: 10px;
      box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
    }
    .logo {
      width: 120px;
    }
    h2 {
      text-align: center;
      margin-bottom: 20px;
    }
    .client-info .row {
      display: flex;
      justify-content: space-between;
    }
    .client-info p {
      margin: 5px 0;
    }
    table {
      width: 100%;
      border-collapse: collapse;
      margin-top: 20px;
    }
    table, th, td {
      border: 1px solid #ccc;
    }
    th, td {
      padding: 10px;
      text-align: left;
    }
    td img {
      width: 60px;
      height: auto;
    }
    .total-section {
      text-align: right;
      margin-top: 15px;
      font-size: 18px;
      font-weight: bold;
    }
    .button-container {
      margin-top: 20px;
      text-align: center;
    }
    .btn {
      padding: 10px 20px;
      background-color: #ff6aa0;
      color: #fff;
      border: none;
      border-radius: 6px;
      cursor: pointer;
      margin: 5px;
    }
    .btn:hover {
      background-color: #e8558a;
    }
  </style>
</head>
<body>

<div class="estimate-wrapper" id="estimate-section">
  <img src="logo.jpg" alt="logo" class="logo" />
 
<p style="
  font-size: 14px;
  margin-top: 10px;
  display: flex;
  flex-wrap: wrap;
  justify-content: center;
  align-items: center;
  gap: 20px;
  text-align: center;
">
  <span style="display: flex; align-items: center; gap: 6px;">
    <img src="men.png" alt="Icon" style="width:20px; height:20px;" />
    Mishva Sheladiya
  </span>
  <span style="display: flex; align-items: center; gap: 6px;">
    <img src="number.png" alt="Number Icon" style="width:18px; height:18px;" />
    (+91) 98765 43210
  </span>
  <span style="display: flex; align-items: center; gap: 6px;">
    <img src="instagram.jpg" alt="Instagram Icon" style="width:20px; height:20px;" />
    @mishva
  </span>
</p>






  
  <?php if ($client): ?>
  <div class="client-info">
    <div class="row">
      <p><strong>Customer Name:</strong> <?= safe($client['name']) ?></p>
      <p><strong>Number:</strong> <?= safe($client['number']) ?></p>
    </div>
    <div class="row">
      <p><strong>Event Name:</strong> <?= safe($client['event']) ?></p>
      <p><strong>Location:</strong> <?= safe($client['location']) ?></p>
    </div>
    <div class="row">
      <p><strong>Date:</strong> <?= formatDate($client['date']) ?></p>
      <p><strong>Time:</strong> <?= safe($client['time']) ?></p>
    </div>
  </div>
  <?php else: ?>
    <p>No client data found.</p>
  <?php endif; ?>
  <div class="scrollable-table-wrapper">
  <table>
    <thead>
      <tr>
        <th>Material</th>
        <th>Details</th>
        <th>Price</th>
        <th>Image</th>
      </tr>
    </thead>
    <tbody>
      <?php 
        $total = 0;
        if (mysqli_num_rows($products) > 0):
          while ($row = mysqli_fetch_assoc($products)):
            $total += $row['price'];
      ?>
        <tr>
          <td><?= safe($row['material']) ?></td>
          <td><?= safe($row['details']) ?></td>
          <td><?= formatCurrency($row['price']) ?></td>
          <td><img src="../<?= safe($row['image']) ?>" alt="Product Image"></td>
        </tr>
      <?php endwhile; else: ?>
        <tr><td colspan="4">No products found.</td></tr>
      <?php endif; ?>
    </tbody>
  </table>
  </div>

  <div class="total-section">
    Total Estimate: <?= formatCurrency($total) ?>
  </div>

  <div class="button-container">
    <button class="btn" onclick="downloadPDF()">Download PDF</button>
    <button class="btn" onclick="sharePDF()">Share as PDF</button>
  </div>
</div>

<script src="https://cdnjs.cloudflare.com/ajax/libs/html2pdf.js/0.10.1/html2pdf.bundle.min.js"></script>
<script>
  function downloadPDF() {
    const element = document.getElementById("estimate-section");
    const opt = {
      margin: 0.3,
      filename: 'Estimate.pdf',
      image: { type: 'jpeg', quality: 0.98 },
      html2canvas: { scale: 2 },
      jsPDF: { unit: 'in', format: 'letter', orientation: 'portrait' }
    };
    html2pdf().set(opt).from(element).save();
  }

  async function sharePDF() {
    const element = document.getElementById("estimate-section");
    const opt = {
      margin: 0.3,
      filename: 'Estimate.pdf',
      image: { type: 'jpeg', quality: 0.98 },
      html2canvas: { scale: 2 },
      jsPDF: { unit: 'in', format: 'letter', orientation: 'portrait' }
    };
    const blob = await html2pdf().set(opt).from(element).outputPdf('blob');
    const file = new File([blob], "Estimate.pdf", { type: "application/pdf" });

    if (navigator.canShare && navigator.canShare({ files: [file] })) {
      try {
        await navigator.share({
          title: "Estimate",
          text: "Please find the event estimate attached.",
          files: [file],
        });
      } catch (err) {
        alert("Sharing cancelled or failed.");
        console.error(err);
      }
    } else {
      alert("Your device does not support PDF sharing.");
    }
  }
</script>

</body>
</html>
