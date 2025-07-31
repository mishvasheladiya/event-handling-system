<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Responsive Header</title>
  <link rel="stylesheet" href="header.css" />
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
</head>
<body>

  <header class="header">
    <div class="header-container">
      <div class="logo">
        <img src="logo.jpg" alt="Logo">
      </div>

      <!-- Toggle Button -->
      <div class="toggle" id="menuToggle">
        <i class="fas fa-bars"></i>
      </div>

      <!-- Navigation Links -->
      <nav class="nav-links" id="navLinks">
        <a href="add-product.php">Add Products</a>
        <a href="view-products.php">View Product</a>
        <a href="estimate.php">Estimate</a>
      </nav>
    </div>
  </header>

  <script>
    // Mobile menu toggle
    document.getElementById("menuToggle").addEventListener("click", function () {
      document.getElementById("navLinks").classList.toggle("active");
    });
  </script>

</body>
</html>
