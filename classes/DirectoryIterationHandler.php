<?php
	class DirectoryIterationHandler extends DirectoryIterator
	{
	    public function GetExtension()
	    {
	        $Filename = $this->GetFilename();
	        $FileExtension = strrpos($Filename, ".", 1) + 1;
	        if ($FileExtension != false)
	            return strtolower(substr($Filename, $FileExtension, strlen($Filename) - $FileExtension));
	        else
	            return "";
	    }
	}
?>