<?php
/**
 * Utility functions for the 2nd Phone Shop application
 */

/**
 * Get a site setting value by its key
 * 
 * @param string $key The setting key to retrieve
 * @param mixed $default Optional default value if setting is not found
 * @return mixed The setting value or default value if not found
 */
function getSetting($key, $default = null) {
    global $conn;
    
    $stmt = $conn->prepare("SELECT setting_value FROM site_settings WHERE setting_key = ?");
    $stmt->bind_param('s', $key);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($row = $result->fetch_assoc()) {
        return $row['setting_value'];
    }
    
    return $default;
}

/**
 * Get all site settings for a specific group
 * 
 * @param string $group The setting group to retrieve
 * @return array Array of settings in the group
 */
function getSettingsByGroup($group) {
    global $conn;
    
    $settings = [];
    $stmt = $conn->prepare("SELECT * FROM site_settings WHERE setting_group = ? ORDER BY id");
    $stmt->bind_param('s', $group);
    $stmt->execute();
    $result = $stmt->get_result();
    
    while ($row = $result->fetch_assoc()) {
        $settings[$row['setting_key']] = $row['setting_value'];
    }
    
    return $settings;
}

/**
 * Get all public site settings
 * 
 * @return array Array of all public settings
 */
function getPublicSettings() {
    global $conn;
    
    $settings = [];
    $result = $conn->query("SELECT * FROM site_settings WHERE is_public = TRUE ORDER BY setting_group, id");
    
    while ($row = $result->fetch_assoc()) {
        $settings[$row['setting_key']] = $row['setting_value'];
    }
    
    return $settings;
} 