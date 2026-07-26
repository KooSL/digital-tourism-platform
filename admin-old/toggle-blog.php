<?php

include '../config/db.php';
include 'auth.php';

$id = (int)$_GET['id'];

mysqli_query($conn, "
  UPDATE blogs
  SET status = IF(status=1, 0, 1)
  WHERE id = $id
");

header("Location: manage-blogs");

?>
