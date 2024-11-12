<?php
require_once '../../../config/config.php';

class ShareController {
    public function share($userId, $fileOrFolderId, $accessType, $type = 'file') {
        global $pdo;
        $column = $type == 'file' ? 'file_id' : 'folder_id';
        $stmt = $pdo->prepare("INSERT INTO share ($column, user_id, access_type, created_at) VALUES (?, ?, ?, NOW())");
        return $stmt->execute([$fileOrFolderId, $userId, $accessType]);
    }
}
