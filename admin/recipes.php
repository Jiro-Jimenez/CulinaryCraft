
<?php
// Include admin header
include '../includes/admin-header.php';

// Get all recipes
$recipes = getAllRecipes();

// Handle delete recipe
if (isset($_GET['delete']) && is_numeric($_GET['delete'])) {
    $recipe_id = $_GET['delete'];
    
    // Delete recipe
    $delete_sql = "DELETE FROM recipes WHERE recipe_id = ?";
    $delete_stmt = $conn->prepare($delete_sql);
    $delete_stmt->bind_param("i", $recipe_id);
    
    if ($delete_stmt->execute()) {
        $success_message = "Recipe deleted successfully.";
        // Refresh recipes list
        $recipes = getAllRecipes();
    } else {
        $error_message = "Failed to delete recipe.";
    }
}
?>

<!-- Page Header -->
<div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1.5rem;">
    <h2>Recipe Management</h2>
    <a href="add-recipe.php" class="btn">Add New Recipe</a>
</div>

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

<!-- Recipes Table -->
<div class="table-container">
    <table>
        <thead>
            <tr>
                <th>ID</th>
                <th>Image</th>
                <th>Title</th>
                <th>Difficulty</th>
                <th>Premium</th>
                <th>Created</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php if (empty($recipes)): ?>
                <tr>
                    <td colspan="7" style="text-align: center;">No recipes found.</td>
                </tr>
            <?php else: ?>
                <?php foreach ($recipes as $recipe): ?>
                    <tr>
                        <td><?php echo $recipe['recipe_id']; ?></td>
                        <td>
                            <img src="<?php echo $recipe['image_path']; ?>" alt="<?php echo $recipe['title']; ?>" style="width: 50px; height: 50px; object-fit: cover; border-radius: 4px;">
                        </td>
                        <td><?php echo $recipe['title']; ?></td>
                        <td>
                            <span class="recipe-difficulty difficulty-<?php echo $recipe['difficulty']; ?>">
                                <?php echo ucfirst($recipe['difficulty']); ?>
                            </span>
                        </td>
                        <td>
                            <span class="status <?php echo $recipe['is_premium'] ? 'status-active' : 'status-inactive'; ?>">
                                <?php echo $recipe['is_premium'] ? 'Premium' : 'Free'; ?>
                            </span>
                        </td>
                        <td><?php echo date('M d, Y', strtotime($recipe['created_at'])); ?></td>
                        <td>
                            <div class="action-buttons">
                                <a href="../recipe-detail.php?id=<?php echo $recipe['recipe_id']; ?>" class="btn btn-sm btn-view" target="_blank">View</a>
                                <a href="edit-recipe.php?id=<?php echo $recipe['recipe_id']; ?>" class="btn btn-sm btn-edit">Edit</a>
                                <a href="recipes.php?delete=<?php echo $recipe['recipe_id']; ?>" class="btn btn-sm btn-delete" onclick="return confirm('Are you sure you want to delete this recipe?')">Delete</a>
                            </div>
                        </td>
                    </tr>
                <?php endforeach; ?>
            <?php endif; ?>
        </tbody>
    </table>
</div>

<?php
// Include admin footer
include '../includes/admin-footer.php';
?>