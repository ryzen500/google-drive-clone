<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

require_once 'config/config.php';

class FolderController {
    public function createFolder($userId, $name, $parentId = null) {
        global $pdo;
        $stmt = $pdo->prepare("INSERT INTO folder (user_id, name, parent_id, created_at) VALUES (?, ?, ?, NOW())");
        
        $message = "";
        if ($stmt->execute([$userId, $name, $parentId])) {
            $message = "Folder created successfully!";
        }else{
            $message ="Upload File Failed";
        }
        return $message;
    }

        // Fetch folders for a specific user

    public function getUserFolders($userId, $parentId = null) {
    global $pdo;
    $query = "SELECT * FROM folder WHERE user_id = ? AND parent_id " . ($parentId ? "= ?" : "IS NULL") . " ORDER BY created_at DESC";
    $stmt = $pdo->prepare($query);
    $stmt->execute($parentId ? [$userId, $parentId] : [$userId]);
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

}
