
<?php
// Include admin header
include '../includes/admin-header.php';

// Get dashboard stats
$stats = getDashboardStats();
?>

<!-- Dashboard Stats -->
<div class="dashboard-cards">
    <div class="card stat-card">
        <i class="fas fa-users"></i>
        <div class="stat-card-content">
            <h3><?php echo $stats['total_users']; ?></h3>
            <p>Total Users</p>
        </div>
    </div>
    
    <div class="card stat-card">
        <i class="fas fa-user-check"></i>
        <div class="stat-card-content">
            <h3><?php echo $stats['total_clients']; ?></h3>
            <p>Total Clients</p>
        </div>
    </div>
    
    <div class="card stat-card">
        <i class="fas fa-concierge-bell"></i>
        <div class="stat-card-content">
            <h3><?php echo $stats['total_services']; ?></h3>
            <p>Total Services</p>
        </div>
    </div>
    
    <div class="card stat-card">
        <i class="fas fa-utensils"></i>
        <div class="stat-card-content">
            <h3><?php echo $stats['total_recipes']; ?></h3>
            <p>Total Recipes</p>
        </div>
    </div>
</div>

<!-- Recent Enrollments -->
<div class="table-container">
    <div class="table-header">
        <h2>Recent Enrollments</h2>
        <a href="services.php" class="btn btn-sm">View All Services</a>
    </div>
    
    <table>
        <thead>
            <tr>
                <th>ID</th>
                <th>User</th>
                <th>Service</th>
                <th>Date</th>
                <th>Status</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php if (empty($stats['recent_enrollments'])): ?>
                <tr>
                    <td colspan="6" style="text-align: center;">No enrollments found.</td>
                </tr>
            <?php else: ?>
                <?php foreach ($stats['recent_enrollments'] as $enrollment): ?>
                    <tr>
                        <td><?php echo $enrollment['enrollment_id']; ?></td>
                        <td><?php echo $enrollment['username']; ?></td>
                        <td><?php echo $enrollment['title']; ?></td>
                        <td><?php echo date('M d, Y', strtotime($enrollment['enrollment_date'])); ?></td>
                        <td>
                            <span class="status status-<?php echo strtolower($enrollment['status']); ?>">
                                <?php echo ucfirst($enrollment['status']); ?>
                            </span>
                        </td>
                        <td>
                            <div class="action-buttons">
                                <a href="#" class="btn btn-sm btn-edit">Edit</a>
                                <a href="#" class="btn btn-sm btn-delete">Delete</a>
                            </div>
                        </td>
                    </tr>
                <?php endforeach; ?>
            <?php endif; ?>
        </tbody>
    </table>
</div>

<!-- Quick Stats -->
<div class="card" style="margin-top: 2rem;">
    <h2 style="margin-bottom: 1.5rem; color: var(--olive-green);">System Overview</h2>
    
    <div style="display: grid; grid-template-columns: repeat(auto-fill, minmax(200px, 1fr)); gap: 1.5rem;">
        <div>
            <h3 style="font-size: 1.2rem; margin-bottom: 0.5rem;">User Statistics</h3>
            <ul style="list-style: disc; padding-left: 1.5rem;">
                <li>Total Users: <?php echo $stats['total_users']; ?></li>
                <li>Total Clients: <?php echo $stats['total_clients']; ?></li>
                <li>Admins: <?php echo $stats['total_users'] - $stats['total_clients']; ?></li>
            </ul>
        </div>
        
        <div>
            <h3 style="font-size: 1.2rem; margin-bottom: 0.5rem;">Content Statistics</h3>
            <ul style="list-style: disc; padding-left: 1.5rem;">
                <li>Total Services: <?php echo $stats['total_services']; ?></li>
                <li>Total Recipes: <?php echo $stats['total_recipes']; ?></li>
                <li>Total Enrollments: <?php echo $stats['total_enrollments']; ?></li>
            </ul>
        </div>
        
        <div>
            <h3 style="font-size: 1.2rem; margin-bottom: 0.5rem;">System Status</h3>
            <ul style="list-style: disc; padding-left: 1.5rem;">
                <li>PHP Version: <?php echo phpversion(); ?></li>
                <li>MySQL Version: <?php echo $conn->server_info; ?></li>
                <li>Server: <?php echo $_SERVER['SERVER_SOFTWARE']; ?></li>
            </ul>
        </div>
    </div>
</div>

<?php
// Include admin footer
include '../includes/admin-footer.php';
?>