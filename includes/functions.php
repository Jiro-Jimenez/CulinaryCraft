<?php
// Include config file
require_once 'config.php';

// Function to register a new user
function registerUser($username, $email, $password, $first_name, $last_name) {
    global $conn;

    // Check connection
    if ($conn === false) {
        error_log("Database connection failed.");
        return false;
    }

    // Hash password
    $hashed_password = password_hash($password, PASSWORD_DEFAULT);

    // Prepare SQL statement
    $sql = "INSERT INTO users (username, email, password, first_name, last_name, role) 
            VALUES (?, ?, ?, ?, ?, 'guest')";

    $stmt = $conn->prepare($sql);

    // Check if prepare was successful
    if ($stmt === false) {
        error_log("Prepare failed: " . $conn->error);
        return false;
    }

    $stmt->bind_param("sssss", $username, $email, $hashed_password, $first_name, $last_name);

    if ($stmt->execute()) {
        return $conn->insert_id;
    } else {
        error_log("Execute failed: " . $stmt->error);
        return false;
    }
}


// Function to login a user
function loginUser($username, $password) {
    global $conn;
    
    // Define the SQL query
    $sql = "SELECT user_id, username, password, role FROM users WHERE username = ?";
    
    // Prepare statement
    $stmt = $conn->prepare($sql);
    
    // Check if prepare was successful
    if ($stmt === false) {
        error_log("Prepare failed in loginUser: " . $conn->error . " for query: " . $sql);
        return false;
    }
    
    // Use standard bind_param syntax (no named parameters)
    $stmt->bind_param("s", $username);
    
    // Execute the statement
    if (!$stmt->execute()) {
        error_log("Execute failed in loginUser: " . $stmt->error);
        return false;
    }
    
    // Get the result
    $result = $stmt->get_result();
    
    // Check if user exists
    if ($result->num_rows === 1) {
        $user = $result->fetch_assoc();
        
        // Verify password
        if (password_verify($password, $user['password'])) {
            // Password is correct, return user data
            return $user;
        }
    }
    
    // Login failed
    return false;
}

// Function to get all services
function getAllServices() {
    global $conn;
    
    $sql = "SELECT * FROM services WHERE is_active = 1 ORDER BY created_at DESC";
    $result = $conn->query($sql);
    
    $services = [];
   if ($result && $result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $services[] = $row;
    }
}

return $services;
}

// Function to get a service by ID
function getServiceById($service_id) {
    global $conn;
    
    $sql = "SELECT * FROM services WHERE service_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $service_id);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows == 1) {
        return $result->fetch_assoc();
    }
    
    return false;
}

// Function to get public recipes (limited to 5 for non-clients)
function getPublicRecipes() {
    global $conn;
    
    $sql = "SELECT * FROM recipes WHERE is_premium = 0 ORDER BY created_at DESC LIMIT 5";
    $result = $conn->query($sql);
    
    $recipes = [];
   if ($result && $result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $recipes[] = $row;
    }
}

    return $recipes;
}

// Function to get all recipes (for clients and admins)
function getAllRecipes() {
    global $conn;
    
    $sql = "SELECT * FROM recipes ORDER BY created_at DESC";
    $result = $conn->query($sql);
    
    $recipes = [];
    if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            $recipes[] = $row;
        }
    }
    
    return $recipes;
}

// Function to get a recipe by ID
function getRecipeById($recipe_id) {
    global $conn;
    
    $sql = "SELECT * FROM recipes WHERE recipe_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $recipe_id);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows == 1) {
        return $result->fetch_assoc();
    }
    
    return false;
}

// Function to check if a recipe is favorited by a user
function isFavorite($user_id, $recipe_id) {
    global $conn;
    
    $sql = "SELECT * FROM favorites WHERE user_id = ? AND recipe_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ii", $user_id, $recipe_id);
    $stmt->execute();
    $result = $stmt->get_result();
    
    return $result->num_rows > 0;
}

// Function to add a recipe to favorites
function addToFavorites($user_id, $recipe_id) {
    global $conn;
    
    // Check if already a favorite
    if (isFavorite($user_id, $recipe_id)) {
        return true;
    }
    
    $sql = "INSERT INTO favorites (user_id, recipe_id) VALUES (?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ii", $user_id, $recipe_id);
    
    return $stmt->execute();
}

// Function to remove a recipe from favorites
function removeFromFavorites($user_id, $recipe_id) {
    global $conn;
    
    $sql = "DELETE FROM favorites WHERE user_id = ? AND recipe_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ii", $user_id, $recipe_id);
    
    return $stmt->execute();
}

// Function to get user's favorite recipes
function getUserFavorites($user_id) {
    global $conn;
    
    $sql = "SELECT r.* FROM recipes r 
            JOIN favorites f ON r.recipe_id = f.recipe_id 
            WHERE f.user_id = ? 
            ORDER BY f.created_at DESC";
    
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $result = $stmt->get_result();
    
    $recipes = [];
    if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            $recipes[] = $row;
        }
    }
    
    return $recipes;
}

// Function to enroll a user in a service
function enrollUserInService($user_id, $service_id) {
    global $conn;
    
    // Check if already enrolled
    $check_sql = "SELECT * FROM enrollments WHERE user_id = ? AND service_id = ?";
    $check_stmt = $conn->prepare($check_sql);
    $check_stmt->bind_param("ii", $user_id, $service_id);
    $check_stmt->execute();
    $check_result = $check_stmt->get_result();
    
    if ($check_result->num_rows > 0) {
        return false; // Already enrolled
    }
    
    // Enroll user
    $sql = "INSERT INTO enrollments (user_id, service_id, status) VALUES (?, ?, 'pending')";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ii", $user_id, $service_id);
    
    if ($stmt->execute()) {
        // Update user role to client if not already
        $update_sql = "UPDATE users SET role = 'client' WHERE user_id = ? AND role = 'guest'";
        $update_stmt = $conn->prepare($update_sql);
        $update_stmt->bind_param("i", $user_id);
        $update_stmt->execute();
        
        return true;
    }
    
    return false;
}

// Function to get all users (for admin)
function getAllUsers() {
    global $conn;
    
    $sql = "SELECT * FROM users ORDER BY created_at DESC";
    $result = $conn->query($sql);
    
    $users = [];
    if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            $users[] = $row;
        }
    }
    
    return $users;
}

// Function to get user by ID
function getUserById($user_id) {
    global $conn;
    
    $sql = "SELECT * FROM users WHERE user_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows == 1) {
        return $result->fetch_assoc();
    }
    
    return false;
}

// Function to update user
function updateUser($user_id, $username, $email, $first_name, $last_name, $role) {
    global $conn;
    
    $sql = "UPDATE users SET username = ?, email = ?, first_name = ?, last_name = ?, role = ? WHERE user_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("sssssi", $username, $email, $first_name, $last_name, $role, $user_id);
    
    return $stmt->execute();
}

// Function to add a new service
function addService($title, $description, $price, $duration, $capacity, $image_path) {
    global $conn;
    
    $sql = "INSERT INTO services (title, description, price, duration, capacity, image_path) 
            VALUES (?, ?, ?, ?, ?, ?)";
    
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ssdsss", $title, $description, $price, $duration, $capacity, $image_path);
    
    return $stmt->execute();
}

// Function to update a service
function updateService($service_id, $title, $description, $price, $duration, $capacity, $image_path, $is_active) {
    global $conn;
    
    $sql = "UPDATE services SET title = ?, description = ?, price = ?, duration = ?, capacity = ?, 
            image_path = ?, is_active = ? WHERE service_id = ?";
    
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ssdssiii", $title, $description, $price, $duration, $capacity, $image_path, $is_active, $service_id);
    
    return $stmt->execute();
}

// Function to add a new recipe
function addRecipe($title, $description, $ingredients, $instructions, $prep_time, $cook_time, $servings, $difficulty, $image_path, $is_premium) {
    global $conn;
    
    $sql = "INSERT INTO recipes (title, description, ingredients, instructions, prep_time, cook_time, servings, difficulty, image_path, is_premium) 
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
    
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ssssiiiisi", $title, $description, $ingredients, $instructions, $prep_time, $cook_time, $servings, $difficulty, $image_path, $is_premium);
    
    return $stmt->execute();
}

// Function to update a recipe
function updateRecipe($recipe_id, $title, $description, $ingredients, $instructions, $prep_time, $cook_time, $servings, $difficulty, $image_path, $is_premium) {
    global $conn;
    
    $sql = "UPDATE recipes SET title = ?, description = ?, ingredients = ?, instructions = ?, 
            prep_time = ?, cook_time = ?, servings = ?, difficulty = ?, image_path = ?, is_premium = ? 
            WHERE recipe_id = ?";
    
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ssssiiissii", $title, $description, $ingredients, $instructions, $prep_time, $cook_time, $servings, $difficulty, $image_path, $is_premium, $recipe_id);
    
    return $stmt->execute();
}

// Function to get dashboard stats for admin
function getDashboardStats() {
    global $conn;
    
    $stats = [];
    
    // Total users
    $sql = "SELECT COUNT(*) as total FROM users";
    $result = $conn->query($sql);
    $stats['total_users'] = $result->fetch_assoc()['total'];
    
    // Total clients
    $sql = "SELECT COUNT(*) as total FROM users WHERE role = 'client'";
    $result = $conn->query($sql);
    $stats['total_clients'] = $result->fetch_assoc()['total'];
    
    // Total services
    $sql = "SELECT COUNT(*) as total FROM services";
    $result = $conn->query($sql);
    $stats['total_services'] = $result->fetch_assoc()['total'];
    
    // Total recipes
    $sql = "SELECT COUNT(*) as total FROM recipes";
    $result = $conn->query($sql);
    $stats['total_recipes'] = $result->fetch_assoc()['total'];
    
    // Total enrollments
    $sql = "SELECT COUNT(*) as total FROM enrollments";
    $result = $conn->query($sql);
    $stats['total_enrollments'] = $result->fetch_assoc()['total'];
    
    // Recent enrollments
    $sql = "SELECT e.*, u.username, s.title FROM enrollments e 
            JOIN users u ON e.user_id = u.user_id 
            JOIN services s ON e.service_id = s.service_id 
            ORDER BY e.enrollment_date DESC LIMIT 5";
    $result = $conn->query($sql);
    
    $stats['recent_enrollments'] = [];
    if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            $stats['recent_enrollments'][] = $row;
        }
    }
    
    return $stats;
}
?>