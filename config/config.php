<?php
// Application configuration
define('APP_NAME', 'Notes App');
define('DATA_DIR', __DIR__ . '/../data/');
define('USERS_FILE', DATA_DIR . 'users.json');
define('NOTES_FILE', DATA_DIR . 'notes.json');

// Create data directory if it doesn't exist
if (!file_exists(DATA_DIR)) {
    mkdir(DATA_DIR, 0755, true);
}

// Initialize files if they don't exist
if (!file_exists(USERS_FILE)) {
    file_put_contents(USERS_FILE, json_encode([]));
}
if (!file_exists(NOTES_FILE)) {
    file_put_contents(NOTES_FILE, json_encode([]));
}
?>