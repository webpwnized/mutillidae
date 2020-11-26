<?php
	switch ($_SESSION["security-level"]){
   		case "0": // This code is insecure
   			// DO NOTHING: This is insecure
   			$lEnableHTMLControls = FALSE;
   			$lEncodeOutput = FALSE;
   			$lTokenizeAllowedMarkup = FALSE;
   			$lProtectAgainstMethodTampering = FALSE;
   		break;
   		
   		case "1": // This code is insecure
   			// DO NOTHING: This is insecure		
			$lEnableHTMLControls = TRUE;
   			$lEncodeOutput = FALSE;
			$lTokenizeAllowedMarkup = FALSE;
			$lProtectAgainstMethodTampering = FALSE;
		break;
	    		
		case "2":
		case "3":
		case "4":
		case "5": // This code is fairly secure
			$lEnableHTMLControls = TRUE;
				
  			/* 
  			 * NOTE: Input validation is excellent but not enough. The output must be
  			 * encoded per context. For example, if output is placed in HTML,
  			 * then HTML encode it. Blacklisting is a losing proposition. You 
  			 * cannot blacklist everything. The business requirements will usually
  			 * require allowing dangerous charaters. In the example here, we can 
  			 * validate username but we have to allow special characters in passwords
  			 * least we force weak passwords. We cannot validate the signature hardly 
  			 * at all. The business requirements for text fields will demand most
  			 * characters. Output encoding is the answer. Validate what you can, encode it
  			 * all.
  			 */
   			// encode the output following OWASP standards
   			// this will be HTML encoding because we are outputting data into HTML
			$lEncodeOutput = TRUE;
			
			/* Business Problem: Sometimes the business requirements define that users
			 * should be allowed to use some HTML  markup. If unneccesary, this is a
			 * bad idea. Output encoding will naturally kill any users attempt to use HTML
			 * in their input, which is exactly why we use output encoding. 
			 * 
			 * If the business process allows some HTML, then those HTML items are elevated
			 * from "mallicious input" to "direct object refernces" (a resource to be enjoyed).
			 * When we want to restrict a user to using to "direct object refernces" (a 
			 * resource to be enjoyed) responsibly, we use mapping. Mapping allows the user
			 * to chose from a "system generated" (that's us programmers) set of tokens
			 * to pick from. We need to assure that the user either chooses one of the tokens
			 * we offer, or our system rejects the request. To put it bluntly, either the user
			 * follows the rules, or their output is encoded. Period.
			 */
			$lTokenizeAllowedMarkup = TRUE;
			
			/* If we are in secure mode, we need to protect against SQLi */
			$lProtectAgainstMethodTampering = TRUE;
   		break;
   	}// end switch

   	if ($lEnableHTMLControls) {
   		$lHTMLControlAttributes='required="required"';
   	}else{
   		$lHTMLControlAttributes="";
   	}// end if
?>

<div class="page-title">View Blogs</div>

<?php include_once (__ROOT__.'/includes/back-button.inc');?>
<?php include_once (__ROOT__.'/includes/hints/hints-menu-wrapper.inc'); ?>

<fieldset>
	<legend>View Blog Entries</legend>
	<span>
		<a href="./index.php?page=add-to-your-blog.php" style=" text-decoration: none;">
		<img style="vertical-align: middle;" src="./images/add-icon-32-32.png" />
		<span style="font-weight:bold;">&nbsp;Add To Your Blog</span>
		</a>
	</span>
	<form action="index.php?page=view-someones-blog.php" method="post" enctype="application/x-www-form-urlencoded">
		<table>
			<tr id="id-bad-blog-entry-tr" style="display: none;">
				<td class="error-message">
					Validation Error: Please choose blog entries to view
				</td>
			</tr>
			<tr><td></td></tr>
			<tr>
				<td id="id-blog-form-header-td" class="form-header">Select Author and Click to View Blog</td>
			</tr>
			<tr><td></td></tr>
			<tr>
				<td>
					<select name="author" id="id_author_select" autofocus="autofocus" <?php echo $lHTMLControlAttributes ?>>
						<option value="53241E83-76EC-4920-AD6D-503DD2A6BA68">&nbsp;&nbsp;&nbsp;Please Choose Author&nbsp;&nbsp;&nbsp;</option>
						<option value="6C57C4B5-B341-4539-977B-7ACB9D42985A">Show All</option>
						<?php
							try {
								$lQueryResult = $SQLQueryHandler->getUsernames();
								
							    while($row = $lQueryResult->fetch_object()){

									if(!$lEncodeOutput){
										$lUsername = $row->username;
									}else{
										$lUsername = $Encoder->encodeForHTML($row->username);
									}// end if
									
								    echo '<option value="' . $lUsername . '">' . $lUsername . '</option>\n';
									
								}// end while
							} catch (Exception $e) {
								echo $CustomErrorHandler->FormatError($e, $lQueryString);
							}// end try		
						?>
					</select>
					<input name="view-someones-blog-php-submit-button" class="button" type="submit" value="View Blog Entries" />
				</td>
			</tr>
			<tr><td></td></tr>
		</table>
	</form>
</fieldset>

<?php
	/* Known Vulnerabilities: 
		SQL injection, Cross Site Scripting, Cross Site Request Forgery
		Known Vulnerable Output: Name, Comment
	*/

	if(isSet($_POST["view-someones-blog-php-submit-button"])){
		try {

			/* Note that $MySQLHandler->escapeDangerousCharacters is ok but not the best defense. Stored
			 * Procedures are a much more powerful defense, run much faster, can be
			 * trapped in a schema, can run on the database, and can be called from
			 * any number of web applications. Stored procs are the true anti-pwn.
			 * There are 3 ways that stored procs can be made vulenrable by developers,
			 * but they are safe by default. Queries are vulnerable by default.
			 */
			if($lProtectAgainstMethodTampering){
				$lAuthor = $_POST["author"];
			}else{
				$lAuthor = $_REQUEST["author"];
			}// end if

			if ($lAuthor == "53241E83-76EC-4920-AD6D-503DD2A6BA68" || strlen($lAuthor) == 0){
				echo '<script>document.getElementById("id-bad-blog-entry-tr").style.display="";</script>';
			}else{
				if ($lAuthor == "6C57C4B5-B341-4539-977B-7ACB9D42985A"){
					$lAuthor = "%";
				}// end if

				$lQueryResult = $SQLQueryHandler->getBlogRecord($lAuthor);
				$LogHandler->writeToLog("User viewed blog for {$lAuthor}");
				
				/* Report Header */
				echo '<div>&nbsp;</div>';
				echo '<table border="1px" width="90%" class="results-table">';
			    echo '
			    	<tr class="report-header">
			    		<td colspan="4">'.$lQueryResult->num_rows.' Current Blog Entries</td>
			    	</tr>
			    	<tr class="report-header">
			    		<td>&nbsp;</td>
					    <td>Name</td>
					    <td>Date</td>
					    <td>Comment</td>
				    </tr>';

			    $lRowNumber = 0;
			    while($row = $lQueryResult->fetch_object()){
			    	
			    	$lRowNumber++;
			    			    	
			    	/* Simple but effective security against XSS. Encode output per context if
					 * we are in secure-mode.
					 */
					if(!$lEncodeOutput){
						$lBloggerName = $row->blogger_name;
						$lDate = $row->date;
						$lComment = $row->comment;
					}else{
						$lBloggerName = $Encoder->encodeForHTML($row->blogger_name);
						$lDate = $Encoder->encodeForHTML($row->date);
						$lComment = $Encoder->encodeForHTML($row->comment);
					}// end if
			    	
					/* Some dangerous markup allowed. Here we restore the tokenized output. 
					 * Note that using GUIDs as tokens works well because they are 
					 * fairly unique plus they encode to the same value. 
					 * Encoding wont hurt them.
					 * 
					 * Note: Mutillidae is weird. It has to be broken and unbroken at the same time.
					 * Here we un-tokenize our output no matter if we are in secure mode or not.
					 */
					$lComment = str_ireplace(BOLD_STARTING_TAG, '<span style="font-weight:bold;">', $lComment);
					$lComment = str_ireplace(BOLD_ENDING_TAG, '</span>', $lComment);
					$lComment = str_ireplace(ITALIC_STARTING_TAG, '<span style="font-style: italic;">', $lComment);
					$lComment = str_ireplace(ITALIC_ENDING_TAG, '</span>', $lComment);
					$lComment = str_ireplace(UNDERLINE_STARTING_TAG, '<span style="border-bottom: 1px solid #000000;">', $lComment);
					$lComment = str_ireplace(UNDERLINE_ENDING_TAG, '</span>', $lComment);
										
					echo "<tr>
						<td>{$lRowNumber}</td>
						<td>{$lBloggerName}</td>
						<td>{$lDate}</td>
						<td>{$lComment}</td>
					</tr>\n";
				}//end while $row
				echo "</table>";		
			    		
			}// end if ($lAuthor == "53241E83-76EC-4920-AD6D-503DD2A6BA68" || strlen($lAuthor) == 0)		

		} catch (Exception $e) {
			echo $CustomErrorHandler->FormatError($e, $lQueryString);
		}// end try		
	}// end if isSet($_POST["view-someones-blog-php-submit-button"])
?>