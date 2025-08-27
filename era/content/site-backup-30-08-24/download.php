<?php

require_once('functions.global.php');
require_once('layout.php');

function deliverMiddle_download($title, $subtitle, $content) {
    return '
    <main style="
        display: flex; 
        flex-direction: column; 
        align-items: center; 
        justify-content: center; 
        height: 80vh; 
        text-align: center;
        padding: 2rem;
    ">
        <h1>' . htmlspecialchars($title) . '</h1>
        <p>' . htmlspecialchars($subtitle) . '</p>
        <div>' . $content . '</div>
    </main>
    ';
}


if (!isset($_GET['id'])) {
	header('location: index.php'); // user loaded without requesting file by id
	die();
}

if (!is_numeric($_GET['id'])) {
	header('location: index.php'); // user requested non-numeric (invalid) file id
	die();
}

$reqFile = $_GET['id'];

$fetched = contactDB("SELECT * FROM files WHERE fileid='$reqFile';", 1);

$realFile = (count($fetched) != 0); // Set realFile to true if we found the file id, false if we didn't find it

if (!$realFile) {
	echo deliverTop("Era - Download");

	echo deliverMiddle("File Not Found", "The file you requested doesn't exist on this server", "");

	echo deliverBottom();
} else {
	$fileName = str_replace("files/", "", $fetched[0]);


	// Allow immediate file download
	if ($_GET['dl'] === "true") {

		header('Content-Type: application/octet-stream');
		header("Content-Transfer-Encoding: Binary");
		header("Content-disposition: attachment; filename=\"" .$fileName. "\"");
		readfile($fetched[0]);
	// BETA (Currently only available to the admin) - Showcase file instead of downloading it
	} elseif ($_GET['show'] === "true" && $_SESSION['erauser'] === 1) {
    		$format = isset($_GET['format']) ? $_GET['format'] : '';
    		$file = $fetched[0];

		if (strpos($format, '://') !== false) {
        		$wrapper = $format;
        		header('Content-Type: application/octet-stream');
    		} else {
        		$wrapper = '';
        		header('Content-Type: text/html');
    		}

    		try {
        		$file_content = fopen($wrapper ? $wrapper . $file : $file, 'r');
			$full_path = $wrapper ? $wrapper . $file : $file;
			// Debug Output
			echo "Opening: " . $full_path . "\n";
        		echo $file_content;
    		} catch (Exception $e) {
        		echo "Error reading file: " . $e->getMessage();
    		}


	// Allow simple download
	} else {
		echo deliverTop("Era - Download");
		echo deliverMiddle_download("Your Download Is Ready!", $fileName, '<a href="download.php?id='.$_GET['id'].'&dl=true"><i class="fa fa-download fa-5x"></i></a>');

	}

}


?>
