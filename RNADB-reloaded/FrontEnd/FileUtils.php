<?php
//// FileUtils.php : File-related utility functions;
//// Author: Maxie D. Schmidt (maxieds@gmail.com) from modified previous code
//// Created: 2019.06.15
?>

<?php

class FileUtils {

     public static function CreateZipFile($filenames = array(), $fileTypeSuffix = "DefaultSequences") {
	     $uniqueSessionID = substr(session_create_id(rand()), 0, 6);
	     $destination = FrontendConfig::DOWNLOADS_URL . "/gtDMMB-RNADB-$fileTypeSuffix-" . 
	                    strftime("%Y-%m-%d-%H%M%S") . "-$uniqueSessionID.zip";
	     $valid_files = array();
	     if(is_array($filenames)) {
		     foreach($filenames as $file) {
			     if(file_exists($file))
				     array_push($valid_files, $file);
		     }
	     }
	     if(count($valid_files)) {
		     $zipFile = new ZipArchive;
		     if(!$zipFile->open(filter_input(INPUT_SERVER, 'DOCUMENT_ROOT') . "/" . $destination, ZIPARCHIVE::CREATE)) {
			     return "NULL (1)";
		     }
		     foreach($valid_files as $file) {
			     $zipFile->addFile($file, basename($file));
		     }
		     $zipFile->close();
		     if(file_exists(filter_input(INPUT_SERVER, 'DOCUMENT_ROOT') . "/" . $destination))
			     return "../" . $destination;
			 return "NULL (2)";
	     }
	     else {
	          return "NULL (3)";
	     }
     }


};

?>
