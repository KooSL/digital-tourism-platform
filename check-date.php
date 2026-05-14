<?php
include 'config/db.php';

$bus_id = intval($_POST['bus_id']);
$date = $_POST['date'];

$query = mysqli_query($conn, "
  SELECT id FROM buses
  WHERE id = '$bus_id' AND travel_date = '$date'
  LIMIT 1
");

if (mysqli_num_rows($query) > 0) {
  echo "available";
} else {
  echo "not_available";
}