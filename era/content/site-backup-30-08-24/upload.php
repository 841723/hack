<?php

require_once('functions.global.php');
require_once('layout.php');

if ($_SESSION['eravalid'] != true) {
    header('location: login.php');
    die();
}

$currentUser = $_SESSION['erauser'];

echo deliverTop("Era - Upload");

// Add professional styling
echo <<<HTML
<style>
    body {
        font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        background-color: #f8f9fa;
        margin: 0;
        padding: 0;
    }

    .upload-container {
        max-width: 600px;
        margin: 40px auto;
        padding: 30px;
        background: #fff;
        border-radius: 8px;
        box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
        text-align: center;
    }

    .upload-container h2 {
        margin-bottom: 20px;
        color: #333;
    }

    .upload-container input[type="file"] {
        margin: 10px 0 20px 0;
        width: 100%;
    }

    .upload-container label {
        font-weight: 200;
        display: block;
        margin-bottom: 8px;
        text-align: left;
    }

     .sidebar label {
    font-weight: normal !important;
}

    .upload-container button {
        padding: 10px 24px;
        font-size: 16px;
        background-color: #007BFF;
        color: #fff;
        border: none;
        border-radius: 4px;
        cursor: pointer;
        transition: background-color 0.3s ease;
    }

    .upload-container button:hover {
        background-color: #0056b3;
    }

    .upload-success, .upload-error {
        text-align: center;
        padding: 20px;
        margin: 20px auto;
        max-width: 600px;
        border-radius: 6px;
        font-size: 18px;
    }

    .upload-success {
        background-color: #d4edda;
        color: #155724;
        border: 1px solid #c3e6cb;
    }

    .upload-error {
        background-color: #f8d7da;
        color: #721c24;
        border: 1px solid #f5c6cb;
    }

    #download-link {
        margin-top: 20px;
        text-align: center;
    }

    #download-url {
        width: 80%;
        padding: 10px;
        font-size: 16px;
        border-radius: 4px;
        border: 1px solid #ccc;
        margin-bottom: 10px;
    }

    #copybutton {
        padding: 10px 20px;
        font-size: 16px;
        background-color: #28a745;
        color: white;
        border: none;
        border-radius: 4px;
        cursor: pointer;
    }

    #copybutton:hover {
        background-color: #218838;
    }
</style>
HTML;

if ($_POST['fsubmitted'] == "true") {

    $target_dir = "files/";

    // If the user is uploading multiple files, we'll ZIP them
    if (count($_FILES["upfile"]["name"]) > 1) {
        $target_file = $target_dir . "Era_User$currentUser " . date('Y-m-d H_i_s') . ".zip";
    } else {
        $target_file = $target_dir . basename($_FILES["upfile"]["name"][0]);
    }

    $uploadOk = true;

    // Check for harmful patterns
    if (strpos($target_file, "'") !== false || strpos($target_file, '"') !== false) {
        echo '<div class="upload-error">Error: Tampering attempt detected.</div>';
        $uploadOk = false;
    }

    $fileType = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));

    if (file_exists($target_file)) {
        echo '<div class="upload-error">Error: File already exists</div>';
        $uploadOk = false;
    }

    $fileListId = contactDB("SELECT * FROM files;", 0);

    if ($uploadOk == false) {
        echo '<div class="upload-error">Error: File was not uploaded</div>';
    } else {
        if (count($_FILES["upfile"]["name"]) > 1) {
            $zip_archive = new ZipArchive;
            $zip_archive->open($target_file, ZipArchive::CREATE);

            foreach ($_FILES["upfile"]["tmp_name"] as $key => $tmp_file_name) {
                $zip_archive->addFile($tmp_file_name, basename($_FILES["upfile"]["name"][$key]));
            }

            $file_upload_complete = $zip_archive->close();
        } else {
            $file_upload_complete = move_uploaded_file($_FILES["upfile"]["tmp_name"][0], $target_file);
        }

        if ($file_upload_complete) {
            $newFileId = rand(1, 9999);
            while (in_array($newFileId, $fileListId)) {
                $newFileId = rand(1, 9999);
            }

            $current_date = time();

            $publish = contactDB("INSERT INTO files (fileid, filepath, fileowner, filedate)
                                  VALUES ($newFileId, '$target_file', $currentUser, $current_date);", 0);

            echo '<div class="upload-success">Upload Successful!</div>';

            $download_link = get_download_link($newFileId);

            echo <<<HTML
<script type="text/javascript">
    function resetCopyButton() {
        document.getElementById("copybutton").innerHTML = "<strong>Copy Link</strong>";
    }

    function copyToClipboard() {
        let downloadlink = "{$download_link}";
        navigator.clipboard.writeText(downloadlink).then(function() {
            document.getElementById("copybutton").innerHTML = "<strong>Copied!</strong>";
            setTimeout(resetCopyButton, 3000);
        }, function() {
            document.getElementById("copybutton").innerHTML = "<strong>Could not copy</strong>";
            setTimeout(resetCopyButton, 3000);
        });
    }
</script>

<div id="download-link">
    <label for="download-url"><strong>Download Link</strong></label><br>
    <input type="text" id="download-url" value="{$download_link}" readonly><br>
HTML;

            if (isSSL() || isLocalhost()) {
                echo <<<HTML
    <button id="copybutton" onclick="copyToClipboard()">
        <strong>Copy Link</strong>
    </button>
HTML;
            }

            echo "</div>";
        } else {
            echo '<div class="upload-error">Error uploading file</div>';
        }
    }
}

echo '<script type="text/javascript">
function checkLimit(files) {
    if (files.length > ' . ini_get('max_file_uploads') . ') {
        alert("You may only upload up to ' . ini_get('max_file_uploads') . ' files at once on this server\\nFor administrators: this setting can be changed in php.ini");

        let list = new DataTransfer();
        for (let i = 0; i < ' . ini_get('max_file_uploads') . '; i++) {
            list.items.add(files[i]);
        }
        document.getElementById("upfile").files = list.files;
    }
}
</script>';

echo deliverMiddle(
    "Upload",
    "",
    '<form action="upload.php" method="post" enctype="multipart/form-data" class="upload-container">
    <h2>Upload Files</h2>
    <input type="hidden" name="fsubmitted" value="true">
    <div class="file-input-wrapper">
        <label for="upfile">Choose file(s) to upload:</label>
        <input
            type="file"
            name="upfile[]"
            id="upfile"
            multiple
            onChange="checkLimit(this.files)"
        >
    </div>
    <button type="submit">
        <i class="fa fa-upload" style="margin-right: 6px;"></i>Upload
    </button>
</form>
'
);

echo deliverBottom();

?>
