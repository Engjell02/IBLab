<?php
class Database {
    public static function getUsers() {
        $data = file_get_contents(USERS_FILE);
        return json_decode($data, true);
    }

    public static function saveUsers($users) {
        file_put_contents(USERS_FILE, json_encode($users, JSON_PRETTY_PRINT));
    }

    public static function getNotes() {
        $data = file_get_contents(NOTES_FILE);
        return json_decode($data, true);
    }

    public static function saveNotes($notes) {
        file_put_contents(NOTES_FILE, json_encode($notes, JSON_PRETTY_PRINT));
    }

    public static function findUserByUsername($username) {
        $users = self::getUsers();
        foreach ($users as $user) {
            if ($user['username'] === $username) {
                return $user;
            }
        }
        return null;
    }

    public static function findUserByEmail($email) {
        $users = self::getUsers();
        foreach ($users as $user) {
            if ($user['email'] === $email) {
                return $user;
            }
        }
        return null;
    }

    public static function findUserById($userId) {
        $users = self::getUsers();
        foreach ($users as $user) {
            if ($user['id'] === $userId) {
                return $user;
            }
        }
        return null;
    }

    public static function createUser($username, $email, $password, $role = 'user') {
        $users = self::getUsers();

        $newUser = [
            'id' => uniqid(),
            'username' => $username,
            'email' => $email,
            'password' => password_hash($password, PASSWORD_DEFAULT),
            'is_admin' => false,
            'created_at' => date('Y-m-d H:i:s'),
            'two_factor_enabled' => false,
            'two_factor_secret' => null,
            'role' => $role,
            'organization_id' => 'org_001',
            'department' => 'General',
            'manager_id' => null,
            'temporary_roles' => []
        ];

        $users[] = $newUser;
        self::saveUsers($users);

        return $newUser;
    }

    public static function deleteUser($userId) {
        $users = self::getUsers();
        $users = array_filter($users, function($user) use ($userId) {
            return $user['id'] !== $userId;
        });
        self::saveUsers(array_values($users));
    }

    public static function getUserNotes($userId) {
        $notes = self::getNotes();
        return array_filter($notes, function($note) use ($userId) {
            return $note['user_id'] === $userId;
        });
    }

    public static function findNoteById($noteId, $userId = null) {
        $notes = self::getNotes();
        foreach ($notes as $note) {
            if ($note['id'] === $noteId) {
                if ($userId === null || $note['user_id'] === $userId) {
                    return $note;
                }
            }
        }
        return null;
    }

    public static function createNote($userId, $title, $content) {
        $notes = self::getNotes();

        $newNote = [
            'id' => uniqid(),
            'user_id' => $userId,
            'title' => $title,
            'content' => $content,
            'created_at' => date('Y-m-d H:i:s'),
            'updated_at' => date('Y-m-d H:i:s')
        ];

        $notes[] = $newNote;
        self::saveNotes($notes);

        return $newNote;
    }

    public static function updateNote($noteId, $title, $content) {
        $notes = self::getNotes();

        foreach ($notes as &$note) {
            if ($note['id'] === $noteId) {
                $note['title'] = $title;
                $note['content'] = $content;
                $note['updated_at'] = date('Y-m-d H:i:s');
                self::saveNotes($notes);
                return true;
            }
        }

        return false;
    }

    public static function deleteNote($noteId) {
        $notes = self::getNotes();
        $notes = array_filter($notes, function($note) use ($noteId) {
            return $note['id'] !== $noteId;
        });
        self::saveNotes(array_values($notes));
    }

    public static function deleteUserNotes($userId) {
        $notes = self::getNotes();
        $notes = array_filter($notes, function($note) use ($userId) {
            return $note['user_id'] !== $userId;
        });
        self::saveNotes(array_values($notes));
    }

    public static function updateUser($userId, $updates) {
        $users = self::getUsers();

        foreach ($users as &$user) {
            if ($user['id'] === $userId) {
                $user = array_merge($user, $updates);
                self::saveUsers($users);
                return true;
            }
        }

        return false;
    }

    public static function assignRole($userId, $role) {
        return self::updateUser($userId, ['role' => $role]);
    }

    public static function getUsersByRole($role) {
        $users = self::getUsers();
        return array_filter($users, function($user) use ($role) {
            return isset($user['role']) && $user['role'] === $role;
        });
    }

    public static function shareNote($noteId, $userId, $permission, $expiresAt = null) {
        $notes = self::getNotes();

        foreach ($notes as &$note) {
            if ($note['id'] === $noteId) {
                if (!isset($note['shared_with'])) {
                    $note['shared_with'] = [];
                }

                $note['shared_with'][] = [
                    'user_id' => $userId,
                    'permission' => $permission,
                    'granted_at' => date('Y-m-d H:i:s'),
                    'expires_at' => $expiresAt
                ];

                self::saveNotes($notes);
                return true;
            }
        }

        return false;
    }

    public static function getSharedNotes($userId) {
        $notes = self::getNotes();
        $shared = [];

        foreach ($notes as $note) {
            if (isset($note['shared_with'])) {
                foreach ($note['shared_with'] as $share) {
                    if ($share['user_id'] === $userId) {
                        // Check if not expired
                        if (!$share['expires_at'] || strtotime($share['expires_at']) >= time()) {
                            $shared[] = $note;
                            break;
                        }
                    }
                }
            }
        }

        return $shared;
    }
}
?>