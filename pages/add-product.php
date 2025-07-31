<?php
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
  echo "<p style='color:red;text-align:center;'>Product deleted.</p>";
}

// Update logic
$updateMode = false;
$editData = ['material' => '', 'details' => '', 'price' => '', 'id' => ''];

if (isset($_GET['edit'])) {
  $updateMode = true;
  $id = $_GET['edit'];
  $result = mysqli_query($conn, "SELECT * FROM products WHERE id=$id");
  if ($row = mysqli_fetch_assoc($result)) {
    $editData = $row;
  }
}

// Handle form submit
if ($_SERVER["REQUEST_METHOD"] == "POST") {
  $material = $_POST['material'];
  $details = $_POST['details'];
  $price = $_POST['price'];

  if (isset($_POST['update_id'])) {
    $update_id = $_POST['update_id'];

    // Optional: Handle image change
    if (!empty($_FILES['image']['name'])) {
      $imageName = $_FILES['image']['name'];
      $tempName = $_FILES['image']['tmp_name'];
      $uniqueName = uniqid() . "_" . basename($imageName);
      $uploadPath = "../uploads/" . $uniqueName;
      move_uploaded_file($tempName, $uploadPath);
      $imagePath = "uploads/" . $uniqueName;

      $sql = "UPDATE products SET material='$material', details='$details', price='$price', image='$imagePath' WHERE id=$update_id";
    } else {
      $sql = "UPDATE products SET material='$material', details='$details', price='$price' WHERE id=$update_id";
    }

    mysqli_query($conn, $sql);
    echo "<p style='color:orange;text-align:center;'>Product updated!</p>";
    $updateMode = false;
    $editData = ['material' => '', 'details' => '', 'price' => '', 'id' => ''];
  } else {
    // New insert
    $imageName = $_FILES['image']['name'];
    $tempName = $_FILES['image']['tmp_name'];
    $uniqueName = uniqid() . "_" . basename($imageName);
    $uploadPath = "../uploads/" . $uniqueName;

    if (move_uploaded_file($tempName, $uploadPath)) {
      $dbImagePath = "uploads/" . $uniqueName;
      $sql = "INSERT INTO products (material, details, price, image)
              VALUES ('$material', '$details', '$price', '$dbImagePath')";
      mysqli_query($conn, $sql);
      echo "<p style='color:green;text-align:center;'>Product added successfully!</p>";
    } else {
      echo "<p style='color:red;text-align:center;'>Image upload failed.</p>";
    }
  }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Add Product</title>
  <link rel="stylesheet" href="add-product.css" />
</head>
<body>

<?php include('header.php'); ?><br><br>

<div class="product-wrapper">
  <form class="product-form-container" method="POST" enctype="multipart/form-data">
    <h2><?= $updateMode ? "Update Product" : "Add a New Product" ?></h2>

    <input type="text" name="material" value="<?= $editData['material'] ?>" placeholder="Enter Material" required />
    <textarea name="details" placeholder="Enter Details" required><?= $editData['details'] ?></textarea>
    <input type="number" name="price" value="<?= $editData['price'] ?>" placeholder="Enter Price" required />
    <input type="file" name="image" accept="image/*" <?= $updateMode ? "" : "required" ?> />

    <?php if ($updateMode): ?>
      <input type="hidden" name="update_id" value="<?= $editData['id'] ?>" />
    <?php endif; ?>

    <button type="submit"><?= $updateMode ? "Update Product" : "Add The Product" ?></button>
  </form>
<div class="table-container">
    <h3 class="product-list-heading">Product List</h3>

    <div class="product-table-wrapper">
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
<a href='add-product.php?edit={$row['id']}' class='btn-update' style='text-decoration:none;'>Edit</a>
<a href='add-product.php?delete={$row['id']}' class='btn-delete' style='text-decoration:none;' onclick=\"return confirm('Are you sure?')\">Delete</a>

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
      <a href="view-products.php"><button class="checkout-btn">Proceed to Checkout</button></a>
    </div>
  </div>
</div>

</body>
</html>
