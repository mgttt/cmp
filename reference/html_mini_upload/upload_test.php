<?php

#require_once "../saas_ace/inc.app.php";
require_once "inc.app.php";

/**
 * This is just an example of how a file could be processed from the
 * upload script. It should be tailored to your own requirements.
 */

// Only accept files with these extensions
$whitelist = array('jpg', 'jpeg', 'png', 'gif');
$name      = null;
$error     = 'No file uploaded.';

if (isset($_FILES)) {
	if (isset($_FILES['file'])) {
		$tmp_name = $_FILES['file']['tmp_name'];
		$name     = basename($_FILES['file']['name']);
		$error    = $_FILES['file']['error'];
		
		if ($error === UPLOAD_ERR_OK) {
			$extension = pathinfo($name, PATHINFO_EXTENSION);

			if (!in_array($extension, $whitelist)) {
				$error = 'Invalid file type uploaded.';
			} else {
				$uploadDir = __DIR__ .'/uploads/';//TODO
				if(!is_dir($uploadDir)){
					mkdir($uploadDir);
				}
				
				$move_rst = move_uploaded_file($tmp_name, $uploadDir."/".$name);
				$error=$move_rst;
			}
		}
	}
}

echo json_encode(array(
	'name'  => $name,
	'error' => $error,
));
die();
