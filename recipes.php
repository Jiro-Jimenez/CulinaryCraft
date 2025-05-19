<?php
// Include header
include 'includes/header.php';

// Get recipes based on user role
if (isLoggedIn() && isClient()) {
    $recipes = getAllRecipes();
    $full_access = true;
} else {
    $recipes = getPublicRecipes();
    $full_access = false;
}
?>

<!-- Hero Section -->
<section class="hero">
    <div class="container">
        <h1>Our Recipes</h1>
        <p>Explore our collection of delicious recipes from around the world.</p>
        <?php if (!$full_access): ?>
            <p><small>Note: Only showing 5 public recipes. Enroll in a class to access all recipes.</small></p>
        <?php endif; ?>
    </div>
</section>

<!-- Recipes Section -->
<section class="recipes">
    <div class="container">
        <div class="recipe-grid">
            <?php foreach ($recipes as $recipe): ?>
                <div class="recipe-card">
                    <div class="recipe-img">
                        <img src="<?php echo $recipe['image_path']; ?>" alt="<?php echo $recipe['title']; ?>">
                    </div>
                    <div class="recipe-content">
                        <h3><?php echo $recipe['title']; ?></h3>
                        <p><?php echo substr($recipe['description'], 0, 100) . '...'; ?></p>
                        <div class="recipe-meta">
                            <span><i class="far fa-clock"></i> <?php echo $recipe['prep_time'] + $recipe['cook_time']; ?> mins</span>
                            <span><i class="fas fa-utensils"></i> <?php echo $recipe['servings']; ?> servings</span>
                        </div>
                        <span class="recipe-difficulty difficulty-<?php echo $recipe['difficulty']; ?>">
                            <?php echo ucfirst($recipe['difficulty']); ?>
                        </span>
                        <a href="recipe-detail.php?id=<?php echo $recipe['recipe_id']; ?>" class="btn btn-outline">View Recipe</a>
                    </div>
                    <?php if (isLoggedIn() && isClient()): ?>
                        <button class="favorite-btn <?php echo isFavorite($_SESSION['user_id'], $recipe['recipe_id']) ? 'active' : ''; ?>" data-recipe-id="<?php echo $recipe['recipe_id']; ?>">
                            <i class="<?php echo isFavorite($_SESSION['user_id'], $recipe['recipe_id']) ? 'fas' : 'far'; ?> fa-heart"></i>
                        </button>
                    <?php endif; ?>
                </div>
            <?php endforeach; ?>
        </div>
        
        <?php if (!$full_access): ?>
            <div style="text-align: center; margin-top: 3rem; padding: 2rem; background-color: var(--off-white); border-radius: 8px;">
                <h2>Want Access to All Recipes?</h2>
                <p>Enroll in one of our cooking classes to get full access to our premium recipe collection.</p>
                <a href="services.php" class="btn" style="margin-top: 1rem;">View Our Classes</a>
            </div>
        <?php endif; ?>
    </div>
</section>

<?php
// Include footer
include 'includes/footer.php';
?>