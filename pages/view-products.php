<?php
// DB Connection First
$conn = mysqli_connect("localhost", "root", "", "shopping");
if (!$conn) {
  die("Connection failed: " . mysqli_connect_error());
}

// Delete product
if (isset($_GET['delete'])) {
  $id = $_GET['delete'];
  $result = mysqli_query($conn, "SELECT image FROM products WHERE id=$id");
  $row = mysqli_fetch_assoc($result);
  if ($row && file_exists("../" . $row['image'])) {
    unlink("../" . $row['image']);
  }
  mysqli_query($conn, "DELETE FROM products WHERE id=$id");
  header("Location: view-products.php");
  exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>View Products</title>
  <link rel="stylesheet" href="view-products.css" />
</head>
<body>

<?php include('header.php'); ?><br><br>

<div class="product-wrapper">
  <!-- Client Info Form -->
  <form class="product-form-container" method="POST" action="save-client-info.php">
    <h2>Client Information</h2>
    <input type="text" name="name" placeholder="Your Name" required />
    <input type="text" name="number" placeholder="Your Number" required />
    <input type="text" name="event" placeholder="Event Name" required />
    <input type="date" name="date" required />
    <input type="time" name="time" required />
    <input type="text" name="location" placeholder="Location" required />
    <button type="submit" class="checkout-btn">Submit</button>
  </form>

  <!-- Product Table -->
<div class="table-container">
  <div class="table-wrapper">
    <div class="product-list-heading">Product List</div>

    <div class="table-scroll-section">
      <table class="product-table" border="1" cellpadding="10">
        <thead>
          <tr>
            <th>Material</th>
            <th>Details</th>
            <th>Price</th>
            <th>Image</th>
            <th>Actions</th>
          </tr>
        </thead>
        <tbody>
          <?php
          $result = mysqli_query($conn, "SELECT * FROM products ORDER BY id DESC");
          $total = 0;
          while ($row = mysqli_fetch_assoc($result)) {
            echo "<tr>";
            echo "<td>" . htmlspecialchars($row['material']) . "</td>";
            echo "<td>" . htmlspecialchars($row['details']) . "</td>";
            echo "<td>₹" . number_format($row['price'], 2) . "</td>";
            echo "<td><img src='../" . $row['image'] . "' width='50' height='50'></td>";
            echo "<td>
                    <a href='add-product.php?edit={$row['id']}' class='btn-update'>Edit</a>
                    <a href='view-products.php?delete={$row['id']}' class='btn-delete' onclick=\"return confirm('Are you sure you want to delete this product?')\">Delete</a>
                  </td>";
            echo "</tr>";
            $total += $row['price'];
          }
          ?>
        </tbody>
      </table>
    </div>

    <div class="total-section">
      <h4>Total: ₹<?= number_format($total, 2) ?></h4>
    </div>
  </div>
</div>


</body>
</html>
