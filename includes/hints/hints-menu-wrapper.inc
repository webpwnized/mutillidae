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

<div  	class="hint-wrapper-header"
		id="idHintWrapperHeader"
		title="Click to open this section"
		style="display: <?php echo $lDisplayHintStyle; ?>";
>
	<img id="idHintWrapperHeaderImage" align="left" style="padding-right: 5px;" alt="Expand Hints" src="./images/down_arrow_16_16.png" />
	Hints and Videos
</div>
<div id="idHintWrapperBody" class="hint-wrapper-body" style="display: none;">
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
						'<div class="hint-header">'.
						'<img src="images/link-icon-16-16.png" style="margin-right: 5px;" />'.
						'<a class="hint-header" href="hints-page-wrapper.php?level1HintIncludeFile='.
							$lRecord->level_1_help_include_file_key.
						'" title="Click to open '.$lRecord->level_1_help_include_file_description.' hint in new tab" target="_blank" />'.
							$lRecord->level_1_help_include_file_description.
						'</a>'.
						'</div>';
				}// end while
			}else{
				include_once (__ROOT__.'/includes/hints/hints-not-found.inc');
			}// end if
		}//end if $_SESSION["showhints"] > 0

   	} catch (Exception $e) {
		echo $CustomErrorHandler->FormatError($e, $lQueryString);
   	}// end try;
	?>
</div>