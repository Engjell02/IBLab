<?php
require_once '../config/config.php';
require_once '../includes/auth.php';
require_once '../includes/functions.php';
require_once '../includes/database.php';

requireAdmin();

$users = Database::getUsers();
$notes = Database::getNotes();
$successMessage = getSuccessMessage();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Panel - <?php echo APP_NAME; ?></title>
    <link rel="stylesheet" href="assets/style.css">
</head>
<body>
<div class="navbar">
    <h1>Admin Panel</h1>
    <a href="dashboard.php">Back to Dashboard</a>
</div>

<div class="container">
    <?php if ($successMessage): ?>
        <div class="success"><?php echo htmlspecialchars($successMessage); ?></div>
    <?php endif; ?>

    <div class="stats">
        <div class="stat-card">
            <h3>Total Users</h3>
            <div class="number"><?php echo count($users); ?></div>
        </div>
        <div class="stat-card">
            <h3>Total Notes</h3>
            <div class="number"><?php echo count($notes); ?></div>
        </div>
        <div class="stat-card">
            <h3>Admin Users</h3>
            <div class="number"><?php echo count(array_filter($users, function($u) { return $u['is_admin']; })); ?></div>
        </div>
    </div>

    <div class="section">
        <h2>All Users</h2>
        <table>
            <thead>
            <tr>
                <th>Username</th>
                <th>Email</th>
                <th>Admin</th>
                <th>Registered</th>
                <th>Actions</th>
            </tr>
            </thead>
            <tbody>
            <?php foreach ($users as $user): ?>
                <tr>
                    <td><?php echo htmlspecialchars($user['username']); ?></td>
                    <td><?php echo htmlspecialchars($user['email']); ?></td>
                    <td><?php echo $user['is_admin'] ? 'Yes' : 'No'; ?></td>
                    <td><?php echo $user['created_at']; ?></td>
                    <td>
                        <?php if ($user['id'] !== getCurrentUserId()): ?>
                            <form method="POST" action="../handlers/admin_delete_user_handler.php" style="display: inline;">
                                <input type="hidden" name="user_id" value="<?php echo $user['id']; ?>">
                                <button type="submit" class="btn btn-danger" onclick="return confirm('Delete user and all their notes?');">Delete</button>
                            </form>
                        <?php endif; ?>
                    </td>
                </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
    </div>

    <div class="section">
        <h2>All Notes</h2>
        <table>
            <thead>
            <tr>
                <th>Title</th>
                <th>Author</th>
                <th>Created</th>
                <th>Actions</th>
            </tr>
            </thead>
            <tbody>
            <?php
            foreach ($notes as $note):
                $author = 'Unknown';
                foreach ($users as $user) {
                    if ($user['id'] === $note['user_id']) {
                        $author = $user['username'];
                        break;
                    }
                }
                ?>
                <tr>
                    <td><?php echo htmlspecialchars($note['title']); ?></td>
                    <td><?php echo htmlspecialchars($author); ?></td>
                    <td><?php echo $note['created_at']; ?></td>
                    <td>
                        <form method="POST" action="../handlers/admin_delete_note_handler.php" style="display: inline;">
                            <input type="hidden" name="note_id" value="<?php echo $note['id']; ?>">
                            <button type="submit" class="btn btn-danger" onclick="return confirm('Are you sure?');">Delete</button>
                        </form>
                    </td>
                </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>
</body>
</html>
