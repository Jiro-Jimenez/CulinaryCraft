<?php
// Include header
include 'includes/header.php';

// Check if recipe ID is provided
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    redirect('recipes.php');
}

// Get recipe details
$recipe_id = $_GET['id'];
$recipe = getRecipeById($recipe_id);

// If recipe not found, redirect to recipes page
if (!$recipe) {
    redirect('recipes.php');
}

// Check if recipe is premium and user has access
if ($recipe['is_premium'] && (!isLoggedIn() || !isClient())) {
    // Redirect to recipes page with message
    $_SESSION['message'] = "This is a premium recipe. Please enroll in a class to access it.";
    $_SESSION['message_type'] = "warning";
    redirect('recipes.php');
}

// Handle favorite button
if (isLoggedIn() && isClient() && isset($_POST['toggle_favorite'])) {
    $user_id = $_SESSION['user_id'];
    
    if (isFavorite($user_id, $recipe_id)) {
        removeFromFavorites($user_id, $recipe_id);
    } else {
        addToFavorites($user_id, $recipe_id);
    }
    
    // Redirect to same page to prevent form resubmission
    redirect("recipe-detail.php?id=$recipe_id");
}
?>

<!-- Recipe Detail Section -->
<section class="recipe-detail">
    <div class="container">
        <div class="recipe-header">
            <h1><?php echo $recipe['title']; ?></h1>
            <div class="recipe-header-meta">
                <div><i class="far fa-clock"></i> Prep: <?php echo $recipe['prep_time']; ?> mins</div>
                <div><i class="fas fa-fire"></i> Cook: <?php echo $recipe['cook_time']; ?> mins</div>
                <div><i class="fas fa-utensils"></i> Serves: <?php echo $recipe['servings']; ?></div>
                <div>
                    <span class="recipe-difficulty difficulty-<?php echo $recipe['difficulty']; ?>">
                        <?php echo ucfirst($recipe['difficulty']); ?>
                    </span>
                </div>
            </div>
            
            <?php if (isLoggedIn() && isClient()): ?>
                <form method="POST" style="margin-top: 1rem;">
                    <button type="submit" name="toggle_favorite" class="btn <?php echo isFavorite($_SESSION['user_id'], $recipe_id) ? 'btn-outline' : ''; ?>">
                        <i class="<?php echo isFavorite($_SESSION['user_id'], $recipe_id) ? 'fas' : 'far'; ?> fa-heart"></i>
                        <?php echo isFavorite($_SESSION['user_id'], $recipe_id) ? 'Remove from Favorites' : 'Add to Favorites'; ?>
                    </button>
                </form>
            <?php endif; ?>
        </div>
        
        <div class="recipe-image">
            <img src="<?php echo $recipe['image_path']; ?>" alt="<?php echo $recipe['title']; ?>">
        </div>
        
        <p style="text-align: center; margin-bottom: 2rem;"><?php echo $recipe['description']; ?></p>
        
        <div class="recipe-content-detail">
            <div class="recipe-ingredients">
                <h3>Ingredients</h3>
                <ul>
                    <?php
                    $ingredients = explode(',', $recipe['ingredients']);
                    foreach ($ingredients as $ingredient):
                    ?>
                        <li><?php echo trim($ingredient); ?></li>
                    <?php endforeach; ?>
                </ul>
            </div>
            
            <div class="recipe-instructions">
                <h3>Instructions</h3>
                <?php
                $instructions = explode("\n", $recipe['instructions']);
                echo '<ol>';
                foreach ($instructions as $step) {
                    if (trim($step) !== '') {
                        echo '<li>' . trim($step) . '</li>';
                    }
                }
                echo '</ol>';
                ?>
                
                <h3 style="margin-top: 2rem;">Tips</h3>
                <ul>
                    <li>Make sure all ingredients are at room temperature before starting.</li>
                    <li>Prep all ingredients before you begin cooking for a smoother process.</li>
                    <li>Taste and adjust seasoning as you go.</li>
                    <li>Let the dish rest for a few minutes before serving for the flavors to meld.</li>
                </ul>
            </div>
        </div>
    </div>
</section>

<?php
// Include footer
include 'includes/footer.php';
?>