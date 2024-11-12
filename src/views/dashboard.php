<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
session_start();
require_once 'src/controllers/FolderController.php';
require_once 'src/controllers/FileController.php';


// Check if user is logged in; if not, redirect to login page
if (!isset($_SESSION['user_id'])) {
    header('Location: auth/login.php');
    exit();
}

$message = '';
$error = '';

// Handle folder creation
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['folder_name'])) {
    $userId = $_SESSION['user_id'];
    $name = $_POST['folder_name'];
    $parentId = isset($_GET['parent_id']) ? $_GET['parent_id'] : null;
    
    $folderController = new FolderController();
    $response = $folderController->createFolder($userId, $name, $parentId);
    
    $message = $response === "Folder created successfully!" ? $response : $error = $response;
}

// Handle file upload
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['file'])) {
    $userId = $_SESSION['user_id'];
    $parentId = isset($_GET['parent_id']) ? $_GET['parent_id'] : null;
    $fileController = new FileController();
    $response = $fileController->uploadFile($userId, $_FILES['file'], $parentId);
    $message = $response === "File uploaded successfully!" ? $response : $error = $response;
}

// Fetch filters and sorting options from URL parameters
$filterType = isset($_GET['file_type']) ? $_GET['file_type'] : '';
$orderBy = isset($_GET['order_by']) ? $_GET['order_by'] : '';


// Fetch folders and files within the current folder
$folders = $files = [];
if (isset($_SESSION['user_id'])) {
    $userId = $_SESSION['user_id'];
    $folderController = new FolderController();
    $fileController = new FileController();
    $parentId = isset($_GET['parent_id']) ? $_GET['parent_id'] : null;

    // Get folders and files with filters applied
    $folders = $folderController->getUserFolders($userId, $parentId);
    $files = $fileController->getUserFiles($userId, $parentId, $filterType, $orderBy);
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Drive Clone</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css" rel="stylesheet">
    <style type="text/css">
        
        #previewContent {
    max-width: 100%;
    max-height: 400px;
    overflow: auto;
}

#previewContent table {
    width: 100%;
    border-collapse: collapse;
}

#previewContent th, #previewContent td {
    padding: 8px;
    text-align: left;
    border: 1px solid #ddd;
}

    </style>
</head>
<body>
    <div class="container">
        <h1 class="mt-4">My Drive</h1>
        <div class="d-flex justify-content-between mt-3">
            <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#uploadModal">Upload File</button>
            <button class="btn btn-secondary" data-bs-toggle="modal" data-bs-target="#createFolderModal">Create Folder</button>
        </div>
   <!-- Filter Form -->
        <form method="GET" class="row mt-3">
            <input type="hidden" name="parent_id" value="<?php echo $parentId; ?>">
            <div class="col-md-3">
                <select name="file_type" class="form-select">
                    <option value="">All Types</option>
                    <option value="image" <?php echo $filterType == 'image' ? 'selected' : ''; ?>>Images</option>
                    <option value="pdf" <?php echo $filterType == 'pdf' ? 'selected' : ''; ?>>PDF</option>
                    <!-- Add other file types as needed -->
                </select>
            </div>
            <div class="col-md-3">
                <select name="order_by" class="form-select">
                    <option value="">Order By</option>
                    <option value="asc" <?php echo $orderBy == 'asc' ? 'selected' : ''; ?>>Oldest First</option>
                    <option value="desc" <?php echo $orderBy == 'desc' ? 'selected' : ''; ?>>Newest First</option>
                </select>
            </div>
            <div class="col-md-3">
                <button type="submit" class="btn btn-primary">Apply Filters</button>
            </div>
        </form>
        <!-- Success or Error Message -->
        <?php if ($message): ?>
            <div class="alert alert-success mt-3"><?php echo $message; ?></div>
        <?php elseif ($error): ?>
            <div class="alert alert-danger mt-3"><?php echo $error; ?></div>
        <?php endif; ?>

        <div class="row mt-4">
            <!-- Display Folders as Cards -->
            <?php if (count($folders) > 0): ?>
                <?php foreach ($folders as $folder): ?>
                    <div class="col-md-3 mb-4">
                        <a href="dashboard?parent_id=<?php echo $folder['id']; ?>" class="text-decoration-none">
                            <div class="card">
                                <div class="card-body text-center">
                                    <!-- Folder Icon -->
                                    <i class="fas fa-folder fa-3x mb-3"></i>
                                    <h5 class="card-title"><?php echo htmlspecialchars($folder['name']); ?></h5>
                                </div>
                            </div>
                        </a>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <p>No folders found.</p>
            <?php endif; ?>

            <!-- Display Files as Cards with Preview and Download buttons -->
            <?php if (count($files) > 0): ?>
                <?php foreach ($files as $file): 

                    // var_dump($file);die;
                    ?>
                    <div class="col-md-3 mb-4">
                        <div class="card">
                            <div class="card-body text-center">
                                <!-- File Icon -->
                                <i class="fas fa-file fa-3x mb-3"></i>
                                <h5 class="card-title"><?php echo htmlspecialchars($file['name']); ?></h5>
                                <!-- Preview and Download Buttons -->
                          <button onclick="previewFile('<?php echo $file['file_path']; ?>', '<?php echo $file['mime_type']; ?>', '<?php echo htmlspecialchars($file['name']); ?>')" class="btn btn-sm btn-info">Preview</button>

                                <a href="<?php echo $file['file_path']; ?>" download class="btn btn-sm btn-success">Download</a>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <p>No files found.</p>
            <?php endif; ?>
        </div>
    </div>

    <!-- Upload Modal -->
    <div class="modal fade" id="uploadModal" tabindex="-1">
        <div class="modal-dialog">
            <form action="" method="post" enctype="multipart/form-data">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Upload File</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <input type="file" name="file" class="form-control" required>
                    </div>
                    <div class="modal-footer">
                        <button type="submit" class="btn btn-primary">Upload</button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Create Folder Modal -->
    <div class="modal fade" id="createFolderModal" tabindex="-1">
        <div class="modal-dialog">
            <form action="" method="post">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Create Folder</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <input type="text" name="folder_name" class="form-control" placeholder="Folder Name" required>
                    </div>
                    <div class="modal-footer">
                        <button type="submit" class="btn btn-primary">Create</button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Preview Modal -->
    <div class="modal fade" id="previewModal" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="previewModalTitle">File Preview</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body" id="previewContent">
                    <!-- Content will be dynamically injected here -->
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/xlsx/0.17.0/xlsx.full.min.js"></script>

    <script>
function previewFile(url, fileType,fileName) {
    let content = '';

  // Set the modal title with the file name
    document.getElementById('previewModalTitle').textContent = `Preview File - ${fileName}`;

    if (fileType.startsWith('image/')) {
        content = `<img src="${url}" class="img-fluid" alt="Image Preview">`;
        document.getElementById('previewContent').innerHTML = content;

    } else if (fileType === 'application/pdf') {
        content = `<iframe src="${url}" width="100%" height="500px" style="border: none;"></iframe>`;
           document.getElementById('previewContent').innerHTML = content;

    } else if (fileType === 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet' || fileType === 'application/vnd.ms-excel') {
        fetch(url)
            .then(response => response.arrayBuffer())
            .then(data => {
                const workbook = XLSX.read(data, { type: 'array' });
                const firstSheetName = workbook.SheetNames[0];
                const worksheet = workbook.Sheets[firstSheetName];
                const jsonData = XLSX.utils.sheet_to_json(worksheet, { header: 1 });

                // Create a formatted HTML table
                let htmlTable = '<table class="table table-bordered table-striped">';
                jsonData.forEach((row, rowIndex) => {
                    htmlTable += '<tr>';
                    row.forEach(cell => {
                        if (rowIndex === 0) {
                            // Table headers
                            htmlTable += `<th>${cell || ''}</th>`;
                        } else {
                            // Table cells
                            htmlTable += `<td>${cell || ''}</td>`;
                        }
                    });
                    htmlTable += '</tr>';
                });
                htmlTable += '</table>';

                document.getElementById('previewContent').innerHTML = htmlTable;
            })
            .catch(error => {
                console.error("Error loading Excel file:", error);
                document.getElementById('previewContent').innerHTML = `<p>Error loading preview. <a href="${url}" download>Click here to download</a>.</p>`;
            });
    } else {
        content = `<p>Preview not available for this file type. <a href="${url}" download>Click here to download</a>.</p>`;
        document.getElementById('previewContent').innerHTML = content;
    }


    const previewModal = new bootstrap.Modal(document.getElementById('previewModal'));
    previewModal.show();
}

    </script>
</body>
</html>
