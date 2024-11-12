<?php
require_once 'config/config.php';

class FileController {

    // Method to retrieve files for a specific user within a folder
    public function getUserFiles($userId, $parentId = null, $fileType = null, $orderByTime = 'ASC') {
        global $pdo; // Use the global $pdo for database connection

        // Start the base query to fetch files based on user ID and folder ID (if provided)
        $query = "SELECT * FROM file WHERE user_id = :user_id";
        
        // If a parent folder ID is provided, filter files within that folder
        if ($parentId !== null && $parentId !== "") {
            $query .= " AND folder_id = :folder_id";
        }

        // If a file type is provided, filter by file type using LIKE
        if ($fileType !== null) {
            $query .= " AND mime_type LIKE :file_type";
        }

        // Add the ordering by creation date (ascending or descending)
        $query .= " ORDER BY created_at $orderByTime";

        // Prepare the statement
        $stmt = $pdo->prepare($query);
        $stmt->bindParam(':user_id', $userId, PDO::PARAM_INT);

        // Bind folder ID if provided
        if ($parentId !== null && $parentId !== "") {
            $stmt->bindParam(':folder_id', $parentId, PDO::PARAM_INT);
        }

        // Bind file type using LIKE if provided
        if ($fileType !== null) {
            $likeFileType = "%" . $fileType . "%"; // Add wildcards for LIKE
            $stmt->bindParam(':file_type', $likeFileType, PDO::PARAM_STR);
        }

        // Execute the query
        $stmt->execute();
        
        // Return the list of files
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Method to upload a file
    public function uploadFile($userId, $file, $folderId = null) {
        global $pdo; // Assuming you're using PDO for database connection

        // Check if the file array has the correct structure
        if (is_array($file) && isset($file['name'], $file['tmp_name'], $file['type'], $file['size'])) {
            $fileName = $file['name'];
            $fileTmpPath = $file['tmp_name'];
            $fileMimeType = $file['type'];
            $fileSize = $file['size'];

            // Set the upload path (e.g., "uploads/")
            $uploadPath = "uploads/";

            // Generate a unique file name (to prevent overwriting existing files)
            $uniqueFileName = uniqid(time() . "_", true) . "_" . basename($fileName);
            $destinationPath = $uploadPath . $uniqueFileName;

            // If folderId is empty, set it to NULL
            if (empty($folderId)) {
                $folderId = NULL;
            }

            // Move uploaded file to the specified path
            if (move_uploaded_file($fileTmpPath, $destinationPath)) {
                // Insert the file info into the database
                $stmt = $pdo->prepare("
                    INSERT INTO file (user_id, folder_id, name, file_path, mime_type, file_size, created_at) 
                    VALUES (?, ?, ?, ?, ?, ?, NOW())
                ");
                $stmt->execute([$userId, $folderId, $uniqueFileName, $destinationPath, $fileMimeType, $fileSize]);

                return "File uploaded successfully!";
            } else {
                return "Error uploading the file. Please try again.";
            }
        } else {
            return "Invalid file data or missing file.";
        }
    }
}
