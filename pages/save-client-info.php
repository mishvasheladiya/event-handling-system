<?php
$conn = mysqli_connect("localhost", "root", "", "shopping");
if (!$conn) {
  die("Connection failed: " . mysqli_connect_error());
}

if ($_SERVER["REQUEST_METHOD"] === "POST") {
  $name = $_POST['name'];
  $number = $_POST['number'];
  $event = $_POST['event'];
  $date = $_POST['date'];
  $time = $_POST['time'];
  $location = $_POST['location'];

  $sql = "INSERT INTO clients (name, number, event, date, time, location) VALUES ('$name', '$number', '$event', '$date', '$time', '$location')";
  if (mysqli_query($conn, $sql)) {
    echo "<script>alert('Client info submitted'); window.location.href='view-products.php';</script>";
  } else {
    echo "Error: " . mysqli_error($conn);
  }
}
?>
