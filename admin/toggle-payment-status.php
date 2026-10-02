<?php

include '../config/db.php';
include 'auth.php';

$id = (int)$_GET['id'];

mysqli_query($conn, "
  UPDATE package_bookings
  SET payment_status = IF(payment_status='full', 'deposit', 'full')
  WHERE id = $id
");

header("Location: package-bookings");
