
<?php
// Include admin header
include '../includes/admin-header.php';

// Handle form submission
$error = false;
$success = false;

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Get form data
    $title = sanitize($_POST['title']);
    $description = sanitize($_POST['description']);
    $price = sanitize($_POST['price']);
    $duration = sanitize($_POST['duration']);
    $capacity = sanitize($_POST['capacity']);
    
    // Handle image upload
    $image_path = 'assets/images/services/default.jpg'; // Default image
    
    if (isset($_FILES['image']) && $_FILES['image']['error'] == 0) {
        $upload_dir = '../assets/images/services/';
        $temp_name = $_FILES['image']['tmp_name'];
        $file_name = time() . '_' . $_FILES['image']['name'];
        
        // Move uploaded file
        if (move_uploaded_file($temp_name, $upload_dir . $file_name)) {
            $image_path = 'assets/images/services/' . $file_name;
        } else {
            $error = "Failed to upload image.";
        }
    }
    
    // Validate form data
    if (empty($title) || empty($description) || empty($price) || empty($duration) || empty($capacity)) {
        $error = "Please fill in all required fields.";
    } elseif (!is_numeric($price) || !is_numeric($capacity)) {
        $error = "Price and capacity must be numbers.";
    } else {
        // Add service
        if (addService($title, $description, $price, $duration, $capacity, $image_path)) {
            $success = "Service added successfully.";
            
            // Clear form data
            $title = $description = $price = $duration = $capacity = '';
        } else {
            $error = "Failed to add service.";
        }
    }
}
?>

<!-- Page Header -->
<div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1.5rem;">
    <h2>Add New Service</h2>
    <a href="services.php" class="btn btn-outline">Back to Services</a>
</div>

<?php if ($error): ?>
    <div class="alert alert-danger">
        <?php echo $error; ?>
    </div>
<?php endif; ?>

<?php if ($success): ?>
    <div class="alert alert-success">
        <?php echo $success; ?>
    </div>
<?php endif; ?>

<!-- Add Service Form -->
<div class="form-container">
    <form method="POST" enctype="multipart/form-data" class="admin-form">
        <div class="form-grid">
            <div class="form-group">
                <label for="title">Service Title</label>
                <input type="text" id="title" name="title" value="<?php echo isset($title) ? $title : ''; ?>" required>
            </div>
            
            <div class="form-group">
                <label for="image">Service Image</label>
                <input type="file" id="image" name="image" class="image-upload" data-preview="image-preview" accept="image/*">
                <div style="margin-top: 0.5rem;">
                    <img id="image-preview" src="#" alt="Preview" style="max-width: 100%; max-height: 200px; display: none;">
                </div>
            </div>
            
            <div class="form-group full-width">
                <label for="description">Description</label>
                <textarea id="description" name="description" required><?php echo isset($description) ? $description : ''; ?></textarea>
            </div>
            
            <div class="form-group">
                <label for="price">Price ($)</label>
                <input type="number" id="price" name="price" value="<?php echo isset($price) ? $price : ''; ?>" min="0" step="0.01" required>
            </div>
            
            <div class="form-group">
                <label for="duration">Duration (e.g., "3 hours")</label>
                <input type="text" id="duration" name="duration" value="<?php echo isset($duration) ? $duration : ''; ?>" required>
            </div>
            
            <div class="form-group">
                <label for="capacity">Capacity (max people)</label>
                <input type="number" id="capacity" name="capacity" value="<?php echo isset($capacity) ? $capacity : ''; ?>" min="1" required>
            </div>
        </div>
        
        <div class="form-actions">
            <button type="reset" class="btn btn-outline">Reset</button>
            <button type="submit" class="btn">Add Service</button>
        </div>
    </form>
</div>

<?php
// Include admin footer
include '../includes/admin-footer.php';
?>