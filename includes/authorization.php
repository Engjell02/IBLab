<?php
require_once __DIR__ . '/../config/roles.php';
require_once __DIR__ . '/database.php';

class Authorization {
    private static $roles;

    public static function init() {
        $rolesConfig = include __DIR__ . '/../config/roles.php';
        self::$roles = $rolesConfig['roles'];
    }

    /**
     * Check if user has permission
     */
    public static function can($permission) {
        if (!isLoggedIn()) {
            return false;
        }

        $user = Database::findUserById(getCurrentUserId());
        if (!$user) {
            return false;
        }

        // Check active role
        $role = $user['role'] ?? 'user';

        // Check temporary roles (JIT access)
        if (self::hasTemporaryPermission($user, $permission)) {
            return true;
        }

        // Check permanent role
        return self::roleHasPermission($role, $permission);
    }

    /**
     * Check if role has permission
     */
    private static function roleHasPermission($role, $permission) {
        if (!isset(self::$roles[$role])) {
            return false;
        }

        $rolePerms = self::$roles[$role]['permissions'];

        // Super admin has all permissions
        if (in_array('*', $rolePerms)) {
            return true;
        }

        // Check specific permission
        if (in_array($permission, $rolePerms)) {
            return true;
        }

        // Check wildcard permissions (e.g., notes.* matches notes.view)
        foreach ($rolePerms as $perm) {
            if (str_ends_with($perm, '.*')) {
                $prefix = substr($perm, 0, -2);
                if (str_starts_with($permission, $prefix . '.')) {
                    return true;
                }
            }
        }

        return false;
    }

    /**
     * Check temporary roles (JIT Access)
     */
    private static function hasTemporaryPermission($user, $permission) {
        if (!isset($user['temporary_roles']) || empty($user['temporary_roles'])) {
            return false;
        }

        $now = time();

        foreach ($user['temporary_roles'] as $tempRole) {
            // Check if expired
            $expiresAt = strtotime($tempRole['expires_at']);
            if ($expiresAt < $now) {
                continue; // Expired
            }

            // Check if role has permission
            if (self::roleHasPermission($tempRole['role'], $permission)) {
                return true;
            }
        }

        return false;
    }

    /**
     * Grant temporary access (JIT)
     */
    public static function grantTemporaryAccess($userId, $role, $duration, $reason) {
        $users = Database::getUsers();

        foreach ($users as &$user) {
            if ($user['id'] === $userId) {
                if (!isset($user['temporary_roles'])) {
                    $user['temporary_roles'] = [];
                }

                $user['temporary_roles'][] = [
                    'role' => $role,
                    'granted_at' => date('Y-m-d H:i:s'),
                    'expires_at' => date('Y-m-d H:i:s', time() + $duration),
                    'granted_by' => getCurrentUserId(),
                    'reason' => $reason
                ];

                break;
            }
        }

        Database::saveUsers($users);
    }

    /**
     * Clean expired temporary roles
     */
    public static function cleanExpiredAccess() {
        $users = Database::getUsers();
        $now = time();
        $cleaned = false;

        foreach ($users as &$user) {
            if (isset($user['temporary_roles'])) {
                $validRoles = [];

                foreach ($user['temporary_roles'] as $tempRole) {
                    $expiresAt = strtotime($tempRole['expires_at']);
                    if ($expiresAt >= $now) {
                        $validRoles[] = $tempRole;
                    } else {
                        $cleaned = true;
                    }
                }

                $user['temporary_roles'] = $validRoles;
            }
        }

        if ($cleaned) {
            Database::saveUsers($users);
        }
    }

    /**
     * Check if user can access note
     */
    public static function canAccessNote($noteId, $permission = 'read') {
        $note = Database::findNoteById($noteId);
        if (!$note) {
            return false;
        }

        $userId = getCurrentUserId();

        // Owner has all permissions
        if ($note['user_id'] === $userId) {
            return true;
        }

        // Check if note is shared with user
        if (isset($note['shared_with'])) {
            foreach ($note['shared_with'] as $share) {
                if ($share['user_id'] === $userId) {
                    // Check if expired
                    if ($share['expires_at'] && strtotime($share['expires_at']) < time()) {
                        continue;
                    }

                    // Check permission level
                    return self::hasResourcePermission($share['permission'], $permission);
                }
            }
        }

        // Admin can view all
        if (self::can('notes.view_all')) {
            return true;
        }

        return false;
    }

    /**
     * Check resource-specific permission
     */
    private static function hasResourcePermission($granted, $required) {
        $rolesConfig = include __DIR__ . '/../config/roles.php';
        $permissions = $rolesConfig['resource_permissions'];

        if (!isset($permissions[$granted])) {
            return false;
        }

        return in_array($required, $permissions[$granted]);
    }

    /**
     * Get user's role level
     */
    public static function getRoleLevel($role) {
        if (!isset(self::$roles[$role])) {
            return 0;
        }

        return self::$roles[$role]['level'];
    }

    /**
     * Check if user is manager of another user
     */
    public static function isManagerOf($managerId, $employeeId) {
        $employee = Database::findUserById($employeeId);

        if (!$employee || !isset($employee['manager_id'])) {
            return false;
        }

        return $employee['manager_id'] === $managerId;
    }

    /**
     * Get all users managed by a manager
     */
    public static function getManagedUsers($managerId) {
        $users = Database::getUsers();
        $managed = [];

        foreach ($users as $user) {
            if (isset($user['manager_id']) && $user['manager_id'] === $managerId) {
                $managed[] = $user;
            }
        }

        return $managed;
    }
}

// Initialize roles
Authorization::init();

/**
 * Helper function - check permission
 */
function can($permission) {
    return Authorization::can($permission);
}

/**
 * Helper function - require permission or redirect
 */
function requirePermission($permission, $redirectTo = '../public/dashboard.php') {
    if (!can($permission)) {
        setErrorMessage('You do not have permission to access this resource.');
        redirect($redirectTo);
    }
}
?>