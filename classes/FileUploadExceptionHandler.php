<?php
class FileUploadExceptionHandler extends Exception{
	
	/*
	UPLOAD_ERR_OK:	Value: 0; There is no error, the file uploaded with success.
	UPLOAD_ERR_INI_SIZE:    Value: 1; The uploaded file exceeds the upload_max_filesize directive in php.ini.
	UPLOAD_ERR_FORM_SIZE:    Value: 2; The uploaded file exceeds the MAX_FILE_SIZE directive that was specified in the HTML form.
	UPLOAD_ERR_PARTIAL:    Value: 3; The uploaded file was only partially uploaded.
	UPLOAD_ERR_NO_FILE:    Value: 4; No file was uploaded.
	UPLOAD_ERR_NO_TMP_DIR:    Value: 6; Missing a temporary folder. Introduced in PHP 4.3.10 and PHP 5.0.3.
	UPLOAD_ERR_CANT_WRITE:    Value: 7; Failed to write file to disk. Introduced in PHP 5.1.0.
	UPLOAD_ERR_EXTENSION:    Value: 8; A PHP extension stopped the file upload. PHP does not provide a way to ascertain which extension caused the file upload to stop; examining the list of loaded extensions with phpinfo() may help. Introduced in PHP 5.2.0.
	*/

	const UPLOAD_ERR_TYPE = 1009;
	const UPLOAD_ERR_SIZE = 1010;
	
    public function __construct($pUploadErrorCode) {
        $lMessage = $this->codeToMessage($pUploadErrorCode);
        parent::__construct($lMessage, $pUploadErrorCode);
    }// end function __construct()

    private function codeToMessage($pUploadErrorCode){
        switch ($pUploadErrorCode) {
            case UPLOAD_ERR_INI_SIZE:
                $lMessage = "The uploaded file exceeds the upload_max_filesize directive in php.ini";
                break;
            case UPLOAD_ERR_FORM_SIZE:
                $lMessage = "The uploaded file exceeds the MAX_FILE_SIZE directive that was specified in the HTML form";
                break;
            case UPLOAD_ERR_PARTIAL:
                $lMessage = "The uploaded file was only partially uploaded";
                break;
            case UPLOAD_ERR_NO_FILE:
                $lMessage = "No file was uploaded";
                break;
            case UPLOAD_ERR_NO_TMP_DIR:
                $lMessage = "Missing a temporary folder";
                break;
            case UPLOAD_ERR_CANT_WRITE:
                $lMessage = "Failed to write file to disk";
                break;
            case UPLOAD_ERR_EXTENSION:
                $lMessage = "File upload stopped by extension";
                break;
            case UPLOAD_ERR_TYPE:
                $lMessage = "File upload stopped by extension";
                break;
			case UPLOAD_ERR_TYPE:
                $lMessage = "File upload stopped by extension";
                break;
			case UPLOAD_ERR_SIZE:
                $lMessage = "File upload stopped by extension";
                break;
            default:
                $lMessage = "Unknown upload error";
                break;
        }
        return $lMessage;
    }// end function codeToMessage()
    
}// end class FileUploadExceptionHandler
?>