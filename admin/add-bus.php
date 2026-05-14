<?php
include '../config/db.php';
include 'auth.php';
include 'includes/header.php';
include 'includes/sidebar.php';

if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

if (isset($_POST['submit'])) {

    if (
        !isset($_POST['csrf_token']) ||
        !hash_equals($_SESSION['csrf_token'], $_POST['csrf_token'])
    ) {
        die("CSRF validation failed.");
    }

    $bus_name = $_POST['bus_name'];
    $bus_number = $_POST['bus_number'];
    $from   = $_POST['from_location'];
    $to     = $_POST['to_location'];
    $travel_date = $_POST['travel_date'];
    $departure_time = $_POST['departure_time'];
    $arrival_time = $_POST['arrival_time'];
    $price = $_POST['price'];
    $total_seats = $_POST['total_seats'];
    $desc   = $_POST['description'];
    $status = $_POST['status'];

    /* IMAGE UPLOAD */
    $imageName = null;

    if (!empty($_FILES['image']['name'])) {
        $imageName = time() . '_' . basename($_FILES['image']['name']);
        move_uploaded_file(
            $_FILES['image']['tmp_name'],
            "uploads/images/buses/" . $imageName
        );
    }

    /* PREPARED STATEMENT */
    $stmt = $conn->prepare(
        "INSERT INTO buses 
        (bus_name, bus_number, from_location, to_location, travel_date, departure_time, arrival_time, price, total_seats, description, banner_image, status) 
        VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)"
    );

    $stmt->bind_param(
        "sssssiiiissi",
        $bus_name,
        $bus_number,
        $from,
        $to,
        $travel_date,
        $departure_time,
        $arrival_time,
        $price,
        $total_seats,
        $desc,
        $imageName,
        $status
    );

    if ($stmt->execute()) {
        header("Location: manage-buses");
        exit();
    } else {
        echo "<script>alert('Error adding bus');</script>";
    }

    $stmt->close();
}
?>

<div class="admin-content">
    <h2>Add New Bus</h2>

    <form method="POST" enctype="multipart/form-data" class="admin-form validate-form">

        <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token']; ?>">

        <div class="form-group">
            <input type="text" name="bus_name" id="bus_name" placeholder="Bus Name" data-validate="name">
            <small class="error"></small>
        </div>

        <div class="form-group">
            <input type="text" name="bus_number" id="bus_number" placeholder="Bus Number">
            <small class="error"></small>
        </div>

        <div class="form-group">
            <input type="text" name="from_location" id="from_location" placeholder="From Location" data-validate="city">
            <small class="error"></small>
        </div>

        <div class="form-group">
            <input type="text" name="to_location" id="to_location" placeholder="To Location"
                data-validate="city">
            <small class="error"></small>
        </div>

        <div class="form-group">
            <input type="date" name="travel_date" id="travel_date" placeholder="Travel Date"
                data-validate="date">
            <small class="error"></small>
        </div>

        <div class="form-group">
            <input type="text" name="departure_time" id="departure_time" placeholder="Departure Time"
                data-validate="time">
            <small class="error"></small>
        </div>

        <div class="form-group">
            <input type="text" name="arrival_time" id="arrival_time" placeholder="Arrival Time"
                data-validate="time">
            <small class="error"></small>
        </div>

        <div class="form-group">
            <input type="text" name="price" id="price" placeholder="Price"
                data-validate="number">
            <small class="error"></small>
        </div>

        <div class="form-group">
            <input type="number" name="total_seats" id="total_seats" placeholder="Total Seats"
                data-validate="number">
            <small class="error"></small>
        </div>

        <!-- <input type="text" name="price" placeholder="Starting Price"> -->

        <div class="form-group">
            <textarea name="description" id="description" placeholder="Description" data-validate="text20"></textarea>
            <small class="error"></small>
        </div>

        <div class="file_input">
            <label>Bus Image</label>
            <input type="file" name="image" accept="image/*" required>
        </div>

        <!-- <label>Group Fare</label>
        <select name="group_fare" required>
            <option value="1">Yes</option>
            <option value="0">No</option>
        </select> -->

        <label>Status</label>
        <select name="status" required>
            <option value="1">Active</option>
            <option value="0">Inactive</option>
        </select>

        <button type="submit" name="submit">Add Bus</button>
    </form>
</div>

<script src="assets/js/form-validator.js"></script>
<?php include 'includes/footer.php'; ?>