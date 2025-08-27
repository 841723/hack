<?php

require_once('layout.php');
require_once('functions.global.php');

function deliverMiddle_manage(string $pageTitle, string $contentHtml): string {
    return <<<HTML
    <style>
        .center-buttons {
            display: flex;
            justify-content: center;
            margin: 1.5rem 0;
        }
        .center-buttons button {
            padding: 0.6rem 1.5rem;
            font-size: 1rem;
            border-radius: 6px;
            border: none;
            cursor: pointer;
            background-color: #007bff;
            color: white;
            font-weight: 600;
            transition: background-color 0.3s ease;
        }
        .center-buttons button:hover {
            background-color: #0056b3;
        }
    </style>

    <main role="main" aria-labelledby="page-title" style="
        max-width: 900px;
        margin: 2rem auto;
        font-family: Arial, sans-serif;
        display: block;
    ">
        <h1 id="page-title" style="
            font-weight: 700;
            font-size: 2rem;
            margin-bottom: 1.5rem;
            text-align: center;
        ">$pageTitle</h1>
        <section style="
            width: 100%;
            display: block;
        ">
            $contentHtml
        </section>
    </main>
HTML;
}

if ($_SESSION['eravalid'] != true) {
    header('location: login.php');
    die();
}

$currentUser = $_SESSION['erauser'];

/* Obtain list of current user's files */
$myFilesId = contactDB("SELECT * FROM files WHERE fileowner=$currentUser;", 0);
$myFilesName = contactDB("SELECT * FROM files where fileowner=$currentUser;", 1);

$nFiles = count($myFilesId);

$i = 0;

/* If the user elected to delete some files, delete those files & then re-load the list */
if ($_POST['msubmitted'] == true) {

    while ($i < $nFiles) {
        if ($_POST["file$myFilesId[$i]"] == "marked") {
            unlink($myFilesName[$i]); // Delete selected file

            $dbChange = contactDB("DELETE FROM files WHERE fileid='$myFilesId[$i]';", 0); // Update database
        }
        $i = $i + 1;
    }
    $i = 0; // Reset iteration for next use
    $noticeText = "<div align='center'><h1>Files successfully deleted</h1></div><br>" . PHP_EOL;

    unset($myFilesId);
    unset($myFilesName); // Re-loading list after file deletion
    unset($nFiles);

    $myFilesId = contactDB("SELECT * FROM files WHERE fileowner=$currentUser;", 0);
    $myFilesName = contactDB("SELECT * FROM files where fileowner=$currentUser;", 1);
}

/* If the user elected to change her settings, make the change now */
if ($_POST['settings-changed'] == true) {

    $input_auto_delete_enabled = ($_POST['auto_delete_enabled'] == true);

    $final_auto_delete_length = -1; // -1 for 'disabled'

    if ($input_auto_delete_enabled) {
        $input_auto_delete_length = $_POST['auto_delete_length'];
        $input_auto_delete_unit = $_POST['auto_delete_unit'];

        if (is_numeric($input_auto_delete_length)) {
            // Convert chosen unit to seconds:
            if ($input_auto_delete_unit == "minutes") {
                $final_auto_delete_length = ($input_auto_delete_length * 60);
            } else if ($input_auto_delete_unit == "hours") {
                $final_auto_delete_length = ($input_auto_delete_length * 60 * 60);
            } else if ($input_auto_delete_unit == "days") {
                $final_auto_delete_length = ($input_auto_delete_length * 24 * 60 * 60);
            } else if ($input_auto_delete_unit == "weeks") {
                $final_auto_delete_length = ($input_auto_delete_length * 7 * 24 * 60 * 60);
            }
        }
    }

    $update_settings = contactDB("UPDATE users SET auto_delete_files_after=$final_auto_delete_length WHERE user_id=$currentUser", 0);
    $noticeText = "<div align='center'><h1>Settings successfully changed</h1></div><br>" . PHP_EOL;

    if ($input_auto_delete_enabled && !is_numeric($input_auto_delete_length)) {
        $notice = "<div align='center'><h1>Error: Non-numeric input for auto-deletion time</h1></div><br>" . PHP_EOL;
    }
}

$auto_delete_after_length = contactDB("SELECT auto_delete_files_after FROM users WHERE user_id=$currentUser;", 0);
$auto_delete_after_length = $auto_delete_after_length[0];

$auto_delete_enabled = ($auto_delete_after_length > 0);

// Determine pre-set "placeholder" value for auto_delete_length
$preset_length = 10;
$preset_unit = "minutes";

if ($auto_delete_enabled) {
    $one_minute = 60;
    $one_hour = $one_minute * 60;
    $one_day = $one_hour * 24;
    $one_week = $one_day * 7;

    // Is it evenly divisible by weeks, days, or hours?
    if ($auto_delete_after_length % $one_week == 0) {
        $preset_length = ($auto_delete_after_length / $one_week);
        $preset_unit = "weeks";
    } else if ($auto_delete_after_length % $one_day == 0) {
        $preset_length = ($auto_delete_after_length / $one_day);
        $preset_unit = "days";
    } else if ($auto_delete_after_length % $one_hour == 0) {
        $preset_length = ($auto_delete_after_length / $one_hour);
        $preset_unit = "hours";
    } else {
        $preset_length = (floor($auto_delete_after_length / $one_minute));
        $preset_unit = "minutes";
    }
}

function set_preset_unit($unit)
{
    global $preset_unit;
    if ($unit == $preset_unit) {
        return ' selected="selected"';
    }
    return "";
}

$noticeHTML = '';
if (isset($noticeText)) {
    $noticeHTML = '
    <div class="notice-message" role="alert" aria-live="polite">
        ' . $noticeText . '
    </div>';
}

$settingsFormStyled = '
<style>
    /* Container for settings and files */
    .manage-container {
        max-width: 700px;
        margin: 2rem auto;
        background: #fff;
        padding: 2rem;
        border-radius: 10px;
        box-shadow: 0 8px 20px rgba(0,0,0,0.05);
        font-family: "Segoe UI", Tahoma, Geneva, Verdana, sans-serif;
    }

    /* Notice message styling */
    .notice-message {
        background-color: #d4edda;
        border: 1px solid #c3e6cb;
        color: #155724;
        padding: 1rem 1.5rem;
        border-radius: 6px;
        font-weight: 600;
        margin-bottom: 1.5rem;
        text-align: center;
    }

    /* Settings form */
    #settings {
        margin-bottom: 2rem;
    }

    #settings label {
        font-weight: 600;
        display: inline-block;
        margin-left: 0.5rem;
        font-size: 1.1rem;
        vertical-align: middle;
    }

    #settings input[type="checkbox"] {
        width: 18px;
        height: 18px;
        vertical-align: middle;
    }

    #settingsForm {
        margin-top: 0.75rem;
        display: flex;
        gap: 1rem;
        align-items: center;
    }

    #settingsForm input[type="number"] {
        width: 100px;
        padding: 0.5rem;
        font-size: 1rem;
        border: 1px solid #ccc;
        border-radius: 6px;
    }

    #settingsForm select {
        flex-grow: 1;
        padding: 0.5rem;
        font-size: 1rem;
        border: 1px solid #ccc;
        border-radius: 6px;
    }

    #settings button {
        margin-top: 1rem;
        background-color: #007bff;
        color: white;
        font-weight: 600;
        border: none;
        border-radius: 6px;
        padding: 0.75rem 1.25rem;
        cursor: pointer;
        transition: background-color 0.3s ease;
        display: inline-flex;
        align-items: center;
        gap: 0.5rem;
        font-size: 1rem;
    }

    #settings button:hover {
        background-color: #0056b3;
    }

    /* Files list styling */
    form.files-form {
        margin-top: 1rem;
    }

    form.files-form .file-item {
        display: flex;
        align-items: center;
        margin-bottom: 0.8rem;
        font-size: 1rem;
    }

    form.files-form .file-item input[type="checkbox"] {
        margin-right: 0.75rem;
        width: 18px;
        height: 18px;
    }

    form.files-form .file-item label a {
        text-decoration: none;
        color: #007bff;
        transition: color 0.3s ease;
    }

    form.files-form .file-item label a:hover {
        color: #0056b3;
        text-decoration: underline;
    }

    /* Delete button */
    form.files-form button.delete-btn {
        margin-top: 1rem;
        background-color: #dc3545;
        color: white;
        font-weight: 600;
        border: none;
        border-radius: 6px;
        padding: 0.75rem 1.25rem;
        cursor: pointer;
        transition: background-color 0.3s ease;
        display: inline-flex;
        align-items: center;
        gap: 0.5rem;
        font-size: 1rem;
    }

    form.files-form button.delete-btn:hover {
        background-color: #a71d2a;
    }

    /* Footer buttons */
    .footer-buttons {
        margin-top: 2rem;
        display: flex;
        gap: 1rem;
        justify-content: center;
    }

    .footer-buttons form button {
        background-color: #6c757d;
        color: white;
        font-weight: 600;
        border: none;
        border-radius: 6px;
        padding: 0.75rem 1.5rem;
        cursor: pointer;
        transition: background-color 0.3s ease;
        font-size: 1rem;
        display: inline-flex;
        align-items: center;
        gap: 0.5rem;
    }

    .footer-buttons form button:hover {
        background-color: #5a6268;
    }
</style>

<div class="manage-container">

    ' . $noticeHTML . '

    <!-- Settings Form -->
    <form action="manage.php" method="post" id="settings">
        <input type="hidden" name="settings-changed" value="true">

        <input type="checkbox" name="auto_delete_enabled" id="auto_delete_enabled"' . ($auto_delete_enabled ? ' checked' : '') . '>
        <label for="auto_delete_enabled">Automatically delete my files after:</label>

        <div id="settingsForm">
            <input type="number" name="auto_delete_length" value="' . htmlspecialchars($preset_length) . '" min="1" required>
            <select name="auto_delete_unit" id="auto_delete_unit" required>
                <option value="minutes"' . set_preset_unit("minutes") . '>Minutes</option>
                <option value="hours"' . set_preset_unit("hours") . '>Hours</option>
                <option value="days"' . set_preset_unit("days") . '>Days</option>
                <option value="weeks"' . set_preset_unit("weeks") . '>Weeks</option>
            </select>
        </div>

        <button type="submit"><i class="fa fa-sync-alt"></i> Update Settings</button>
    </form>

    <hr>

    <!-- Files Form -->
    <form action="manage.php" method="post" class="files-form">
        <input type="hidden" name="msubmitted" value="true">

        ';

if ($nFiles === 0) {
    $settingsFormStyled .= '<p>You haven\'t uploaded any files yet.</p>';
} else {
    for ($i = 0; $i < $nFiles; $i++) {
        $fileNameClean = htmlspecialchars(str_replace("files/", "", $myFilesName[$i]));
        $fileId = htmlspecialchars($myFilesId[$i]);
        $settingsFormStyled .= '
            <div class="file-item">
                <input type="checkbox" name="file' . $fileId . '" id="file' . $fileId . '" value="marked">
                <label for="file' . $fileId . '">
                    <a href="download.php?id=' . $fileId . '">' . $fileNameClean . '</a>
                </label>
            </div>';
    }
}

$settingsFormStyled .= '

        <button type="submit" class="delete-btn"><i class="fa fa-trash-alt"></i> Delete Selected Files</button>
    </form>

    <div class="footer-buttons">
        <form action="index.php" method="get" style="margin:0;">
            <button type="submit"><i class="fa fa-home"></i> Return Home</button>
        </form>
        <form action="reset.php" method="get" style="margin:0;">
            <button type="submit"><i class="fa fa-key"></i> Reset Security Questions</button>
        </form>
    </div>

</div>
';

echo deliverTop("Era - Manage");

echo deliverMiddle_manage("Manage Your Files & Settings", $settingsFormStyled);

echo deliverBottom();
