<?php
require_once '../config/config.php';
require_once '../includes/auth.php';
require_once '../includes/authorization.php';
require_once '../includes/functions.php';
require_once '../includes/database.php';

requireLogin();
requirePermission('team.view');

$currentUser = Database::findUserById(getCurrentUserId());
$managedUsers = Authorization::getManagedUsers(getCurrentUserId());
$rolesConfig = include '../config/roles.php';

$teamNotes = [];
foreach ($managedUsers as $user) {
    $userNotes = Database::getUserNotes($user['id']);
    foreach ($userNotes as $note) {
        $note['author'] = $user['username'];
        $teamNotes[] = $note;
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manager Dashboard - <?php echo APP_NAME; ?></title>
    <link rel="stylesheet" href="assets/style.css">
</head>
<body>
<div class="navbar">
    <h1>Manager Dashboard</h1>
    <div class="navbar-right">
        <a href="dashboard.php">My Notes</a>
        <a href="logout.php">Logout</a>
    </div>
</div>

<div class="container">
    <div class="section">
        <h2>My Team</h2>
        <table>
            <thead>
            <tr>
                <th>Username</th>
                <th>Email</th>
                <th>Role</th>
                <th>Department</th>
                <th>Notes Count</th>
            </tr>
            </thead>
            <tbody>
            <?php foreach ($managedUsers as $user): ?>
                <?php
                $userNotes = Database::getUserNotes($user['id']);
                $role = $user['role'] ?? 'user';
                ?>
                <tr>
                    <td><?php echo htmlspecialchars($user['username']); ?></td>
                    <td><?php echo htmlspecialchars($user['email']); ?></td>
                    <td><?php echo htmlspecialchars($rolesConfig['roles'][$role]['name']); ?></td>
                    <td><?php echo htmlspecialchars($user['department'] ?? 'N/A'); ?></td>
                    <td><?php echo count($userNotes); ?></td>
                </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
    </div>

    <div class="section">
        <h2>Team Notes</h2>
        <?php if (empty($teamNotes)): ?>
            <p style="text-align: center; color: #999; padding: 40px;">
                No notes from your team members yet.
            </p>
        <?php else: ?>
            <div class="notes-grid">
                <?php foreach ($teamNotes as $note): ?>
                    <div class="note-card">
                        <h3><?php echo htmlspecialchars($note['title']); ?></h3>
                        <p><?php echo nl2br(htmlspecialchars(substr($note['content'], 0, 150))); ?><?php echo strlen($note['content']) > 150 ? '...' : ''; ?></p>
                        <small style="color: #999;">
                            Author: <?php echo htmlspecialchars($note['author']); ?><br>
                            Created: <?php echo $note['created_at']; ?>
                        </small>
                        <div class="note-actions">
                            <a href="notes/view_note.php?id=<?php echo $note['id']; ?>" class="btn btn-secondary">View</a>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>
</div>
</body>
</html>