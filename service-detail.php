<?php
// Include header
include 'includes/header.php';

// Check if service ID is provided
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    redirect('services.php');
}

// Get service details
$service_id = $_GET['id'];
$service = getServiceById($service_id);

// If service not found, redirect to services page
if (!$service) {
    redirect('services.php');
}

// Handle enrollment form submission
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['enroll'])) {
    if (isLoggedIn()) {
        $user_id = $_SESSION['user_id'];
        
        // Enroll user in service
        if (enrollUserInService($user_id, $service_id)) {
            $success_message = "You have successfully enrolled in this service. Your status is pending confirmation.";
        } else {
            $error_message = "You are already enrolled in this service or an error occurred.";
        }
    } else {
        // Redirect to login page with return URL
        redirect('login.php?redirect=' . urlencode($_SERVER['REQUEST_URI']));
    }
}
?>

<!-- Service Detail Section -->
<section class="recipe-detail">
    <div class="container">
        <?php if (isset($success_message)): ?>
            <div class="alert alert-success">
                <?php echo $success_message; ?>
            </div>
        <?php endif; ?>
        
        <?php if (isset($error_message)): ?>
            <div class="alert alert-danger">
                <?php echo $error_message; ?>
            </div>
        <?php endif; ?>
        
        <div class="recipe-header">
            <h1><?php echo $service['title']; ?></h1>
            <div class="recipe-header-meta">
                <div><i class="far fa-clock"></i> <?php echo $service['duration']; ?></div>
                <div><i class="fas fa-users"></i> Max <?php echo $service['capacity']; ?> people</div>
                <div><i class="fas fa-tag"></i> $<?php echo number_format($service['price'], 2); ?></div>
            </div>
        </div>
        
        <div class="recipe-image">
            <img src="<?php echo $service['image_path']; ?>" alt="<?php echo $service['title']; ?>">
        </div>
        
        <div class="recipe-content-detail">
            <div class="recipe-ingredients">
                <h3>Service Details</h3>
                <ul>
                    <li><strong>Duration:</strong> <?php echo $service['duration']; ?></li>
                    <li><strong>Capacity:</strong> <?php echo $service['capacity']; ?> people</li>
                    <li><strong>Price:</strong> $<?php echo number_format($service['price'], 2); ?></li>
                    <li><strong>What to Bring:</strong> Just yourself and your enthusiasm for cooking!</li>
                    <li><strong>What's Included:</strong> All ingredients, equipment, and recipes</li>
                </ul>
                
                <form id="enroll-form" method="POST" data-logged-in="<?php echo isLoggedIn() ? 'true' : 'false'; ?>" style="margin-top: 2rem;">
                    <button type="submit" name="enroll" class="btn" style="width: 100%;">Enroll Now</button>
                </form>
            </div>
            
            <div class="recipe-instructions">
                <h3>Description</h3>
                <p><?php echo nl2br($service['description']); ?></p>
                
                <h3 style="margin-top: 2rem;">What You'll Learn</h3>
                <ul>
                    <li>Professional cooking techniques</li>
                    <li>Ingredient selection and preparation</li>
                    <li>Recipe execution and timing</li>
                    <li>Plating and presentation</li>
                    <li>Tips and tricks from professional chefs</li>
                </ul>
                
                <h3 style="margin-top: 2rem;">Who This Class is For</h3>
                <p>This class is suitable for all skill levels, from beginners to experienced home cooks. Whether you're looking to learn new skills or refine existing ones, our expert instructors will guide you through the process.</p>
            </div>
        </div>
    </div>
</section>

<?php
// Include footer
include 'includes/footer.php';
?>