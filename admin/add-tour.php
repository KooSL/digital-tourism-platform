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

    $title      = $_POST['title'];
    $type = $_POST['type'];
    $duration   = $_POST['duration'];
    $price      = $_POST['price'];
    $price_usd = $_POST['price_usd'];
    $old_price = $_POST['old_price'];
    $overview   = $_POST['overview'];
    $highlights = $_POST['highlights'];
    $includes   = $_POST['includes'];
    $excludes   = $_POST['excludes'];
    $status     = $_POST['status'];
    $is_popular = $_POST['is_popular'];

    /* IMAGE UPLOAD */
    $banner = time() . '_' . $_FILES['banner']['name'];
    move_uploaded_file(
        $_FILES['banner']['tmp_name'],
        "uploads/images/tours/" . $banner
    );

    /* PDF UPLOAD */
    $pdf = '';
    if (!empty($_FILES['pdf']['name'])) {
        $pdf = time() . '_' . $_FILES['pdf']['name'];
        move_uploaded_file(
            $_FILES['pdf']['tmp_name'],
            "uploads/pdf/" . $pdf
        );
    }

    /* INSERT TOUR */
    $stmt = $conn->prepare("
        INSERT INTO tours
        (title, type, duration, price, price_usd, old_price, overview, highlights, includes, excludes, banner_image, pdf_file, is_popular, status)
        VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
    ");

    $stmt->bind_param(
        "sssdddssssssii",
        $title,
        $type,
        $duration,
        $price,
        $price_usd,
        $old_price,
        $overview,
        $highlights,
        $includes,
        $excludes,
        $banner,
        $pdf,
        $is_popular,
        $status
    );

    if ($stmt->execute()) {

        $tour_id = $stmt->insert_id;

        /* INSERT ITINERARY (IF EXISTS) */
        if (
            !empty($_POST['day_no']) &&
            count($_POST['day_no']) === count($_POST['itinerary_title']) &&
            count($_POST['day_no']) === count($_POST['itinerary_desc'])
        ) {

            $itStmt = $conn->prepare("
                INSERT INTO tour_itineraries
                (tour_id, day_number, title, description)
                VALUES (?, ?, ?, ?)
            ");

            for ($i = 0; $i < count($_POST['day_no']); $i++) {

                $day     = (int) $_POST['day_no'][$i];
                $itTitle = $_POST['itinerary_title'][$i];
                $itDesc  = $_POST['itinerary_desc'][$i];

                if ($day && $itTitle && $itDesc) {
                    $itStmt->bind_param("iiss", $tour_id, $day, $itTitle, $itDesc);
                    $itStmt->execute();
                }
            }

            $itStmt->close();
        }

        $_SESSION['success'] = "Tour added successfully.";
        header("Location: manage-tours");
        exit();
    } else {
        $_SESSION['error'] = "Error adding tour.";
        header("Location: add-tour");
        exit();
    }

    $stmt->close();
}

?>

<div class="admin-content">
    <h2>Add New Tour</h2>

    <?php include 'includes/admin-alert.php'; ?>

    <form method="POST" enctype="multipart/form-data" class="admin-form validate-form">

        <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token']; ?>">

        <div class="form-group">
            <input type="text" name="title" id="title" placeholder="Tour Title" data-validate="name">
            <small class="error"></small>
        </div>

        <label>Type</label>
        <select name="type">
            <option value="domestic">Domestic</option>
            <option value="international">International</option>
        </select>

        <div class="form-group">
            <input type="text" name="duration" id="duration" placeholder="Duration (e.g. 7 Days)" data-validate="duration">
            <small class="error"></small>
        </div>

        <div class="form-group">
            <input type="number" step="0.01" name="price" id="price" placeholder="Price in NPR (e.g. 85000)" data-validate="price">
            <small class="error"></small>
        </div>

        <div class="form-group">
            <input type="number" step="0.01" name="price_usd" id="price_usd" placeholder="Price in USD (e.g. 799)" data-validate="price">
            <small class="error"></small>
        </div>

        <div class="form-group">
            <input type="number" step="0.01" name="old_price" id="old_price" placeholder="Old Price in NPR (e.g. 100000)" data-validate="price">
            <small class="error"></small>
        </div>

        <div class="form-group">
            <textarea name="overview" id="overview" placeholder="Trip Overview" data-validate="text20"></textarea>
            <small class="error"></small>
        </div>

        <div class="form-group">
            <textarea name="highlights" id="highlights" placeholder="Trip Highlights (one per line)" data-validate="text10"></textarea>
            <small class="error"></small>
        </div>

        <label>Add Itinerary</label>
        <div id="itinerary-wrapper">
            <div class="itinerary-row">
                <div class="form-group">
                    <input type="number" name="day_no[]" placeholder="Day 1" class="day-no">
                    <small class="error"></small>
                </div>

                <div class="form-group">
                    <input type="text" name="itinerary_title[]" placeholder="Title" class="it-title">
                    <small class="error"></small>
                </div>

                <div class="form-group">
                    <textarea name="itinerary_desc[]" placeholder="Description" class="it-desc"></textarea>
                    <small class="error"></small>
                </div>

                <button type="button" class="remove-itinerary">Remove</button>
            </div>
        </div>

        <button type="button" class="additinerarybtn" onclick="addItinerary()">+ Add Day</button>

        <div class="form-group">
            <textarea name="includes" id="includes" placeholder="Cost Includes" data-validate="text10"></textarea>
            <small class="error"></small>
        </div>

        <div class="form-group">
            <textarea name="excludes" id="excludes" placeholder="Cost Excludes" data-validate="text10"></textarea>
            <small class="error"></small>
        </div>

        <div class="file_input">
            <label>Banner Image *</label>
            <input type="file" name="banner" accept="image/*" required>
        </div>

        <div class="file_input">
            <label>Trip PDF</label>
            <input type="file" name="pdf" accept="application/pdf">
        </div>

        <label>Is Popular?</label>
        <select name="is_popular">
            <option value="0">No</option>
            <option value="1">Yes</option>
        </select>

        <label>Status</label>
        <select name="status" required>
            <option value="1">Active</option>
            <option value="0">Inactive</option>
        </select>

        <button name="submit">Add Tour</button>
    </form>
</div>

<script src="assets/js/itinerary-days-add-remove.js"></script>
<!-- <script src="assets/js/tour-validation.js"></script> -->
<script src="assets/js/form-validator.js"></script>
<script src="assets/js/itinerary-validation.js"></script>
<script src="assets/js/admin-alert.js"></script>

<?php include 'includes/footer.php'; ?>