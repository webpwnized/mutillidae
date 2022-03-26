<?php
    if(isset($_SESSION["showhints"])){
        $lShowhint = $_SESSION["showhints"];
    }else{
        $lShowhint = 0;
    }

    if ($lShowhint > 0) {
		$lDisplayHintStyle = "block";
	}else{
		$lDisplayHintStyle = "none";
	}//end if
?>
<div class="col d-flex justify-content-end">
<div  	class="dropdown"
		id="idHintWrapperHeader"
		title="Click to open this section"
		style="display: <?php echo $lDisplayHintStyle; ?>";
>

	<button class="btn btn-info dropdown-toggle" type="button" id="dropdownMenuButton1" data-bs-toggle="dropdown" aria-expanded="false">
	Hints and Videos
  	</button>

<ul  class="dropdown-menu" aria-labelledby="dropdownMenuButton1">
	<?php
	try{
	    if ($lShowhint > 0) {

		    if(array_key_exists("page", $_GET)){
		        $lPageName = $_GET["page"];
		    }else{
		        $lPageName = "home.php";
		    }//end if

			$lIncludeFileRecords = $SQLQueryHandler->getPageLevelOneHelpIncludeFiles($lPageName);

			if ($SQLQueryHandler->affected_rows()>0) {
				while($lRecord = $lIncludeFileRecords->fetch_object()){
					echo
						
						'<li>' .
						'<a class="dropdown-item my-2" href="hints-page-wrapper.php?level1HintIncludeFile='.
							$lRecord->level_1_help_include_file_key.
						'" title="Click to open '.$lRecord->level_1_help_include_file_description.' hint in new tab" target="_blank" />'.
							'<span class="material-icons align-middle mx-2">link</span>' .
							$lRecord->level_1_help_include_file_description.
						'</a>'.
						'</li>'
						;
						
				}// end while
			}else{
				include_once (__ROOT__.'/includes/hints/hints-not-found.inc');
			}// end if
		}//end if $_SESSION["showhints"] > 0

   	} catch (Exception $e) {
		echo $CustomErrorHandler->FormatError($e, $lQueryString);
   	}// end try;
	?>
</ul>


</div>

</div>