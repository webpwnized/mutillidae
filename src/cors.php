<?php
    try {
        switch ($_SESSION["security-level"]) {
            default:
            case "0":
                $lEnableJavaScriptValidation = false;
                $lEnableHTMLControls = false;
                break;

            case "1":
                $lEnableJavaScriptValidation = true;
                $lEnableHTMLControls = true;
                break;

            case "2":
            case "3":
            case "4":
            case "5":
                $lEnableHTMLControls = true;
                $lEnableJavaScriptValidation = true;
                break;
        }
    } catch (Exception $e) {
        $lErrorMessage = "Error setting up configuration on page cors.php";
        echo $CustomErrorHandler->FormatError($e, $lErrorMessage);
    }
?>

<div class="page-title">Cross-origin Resource Sharing (CORS)</div>

<?php include_once __SITE_ROOT__.'/includes/back-button.inc'; ?>
<?php include_once __SITE_ROOT__.'/includes/hints/hints-menu-wrapper.inc'; ?>

<script type="text/javascript">
    var onSubmitOfForm = function(theForm) {
        var lText = theForm.idMessageInput.value;

        <?php if ($lEnableJavaScriptValidation) { ?>
            var lOSCommandInjectionPattern = /[;&|<>]/;
            var lCrossSiteScriptingPattern = /[<>=()]/;
        <?php } else { ?>
            var lOSCommandInjectionPattern = /[]/;
            var lCrossSiteScriptingPattern = /[]/;
        <?php } ?>

        if (lText.search(lOSCommandInjectionPattern) > -1) {
            alert("Malicious characters are not allowed.");
            return false;
        } else if (lText.search(lCrossSiteScriptingPattern) > -1) {
            alert("Characters used in cross-site scripting are not allowed.");
            return false;
        }

        var lXMLHTTP = new XMLHttpRequest();
        var lURL = "http://cors.<?php echo $_SERVER['SERVER_NAME']; ?>/webservices/rest/ws-cors-echo.php";
        var lAsynchronously = true;
        var lMessage = encodeURIComponent(lText);

        // Correct way to get the selected method
        var lMethodElement = document.querySelector('input[name="method"]:checked');
        if (!lMethodElement) {
            alert("Please select an HTTP method.");
            return false;
        }
        var lMethod = lMethodElement.value;

        var lSendACAOHeader = theForm.idACAO.checked ? "True" : "False";
        var lSendACAMHeader = theForm.idACAM.checked ? "True" : "False";
        var lSendACMAHeader = theForm.idACMA.checked ? "True" : "False";
        var lMaxAge = encodeURIComponent(theForm.idMaxAgeInput.value || 600); // Default to 600

        var lQueryParameters =
            "message=" + lMessage +
            "&method=" + lMethod +
            "&acao=" + lSendACAOHeader +
            "&acam=" + lSendACAMHeader +
            "&acma=" + lSendACMAHeader +
            "&max-age=" + lMaxAge;

        lXMLHTTP.onreadystatechange = function() {
            if (this.readyState == 4) {
                document.getElementById("idMessageOutput").innerHTML = lXMLHTTP.responseText;
            }
        };

        switch (lMethod) {
            case "GET":
                lXMLHTTP.open(lMethod, lURL + "?" + lQueryParameters, lAsynchronously);
                lXMLHTTP.send();
                break;
            case "POST":
            case "PUT":
            case "PATCH":
            case "DELETE":
            case "OPTIONS":
                lXMLHTTP.open(lMethod, lURL, lAsynchronously);
                lXMLHTTP.setRequestHeader('Content-type', 'application/x-www-form-urlencoded');
                lXMLHTTP.send(lQueryParameters);
                break;
        }
    };
</script>

<form>
    <table>
        <tr>
            <td colspan="2" class="form-header">Enter message to echo</td>
        </tr>
        <tr>
            <td class="label">Message</td>
            <td>
                <input
                    type="text"
                    id="idMessageInput"
                    name="message"
                    size="20"
                    value="Hello World!"
                    onclick="this.select();"
                    autofocus="autofocus"
                    <?php if ($lEnableHTMLControls) { echo 'minlength="1" maxlength="20" required="required"'; } ?>
                />
            </td>
        </tr>
        <tr>
            <td class="label">HTTP Method</td>
            <td>
                <label><input type="radio" name="method" value="GET" checked /> GET</label><br>
                <label><input type="radio" name="method" value="POST" /> POST</label><br>
                <label><input type="radio" name="method" value="PUT" /> PUT</label><br>
                <label><input type="radio" name="method" value="PATCH" /> PATCH</label><br>
                <label><input type="radio" name="method" value="DELETE" /> DELETE</label><br>
                <label><input type="radio" name="method" value="OPTIONS" /> OPTIONS</label><br>
            </td>
        </tr>
        <tr>
            <td class="label">Response Headers to Send</td>
            <td>
                <label>
                    <input type="checkbox" id="idACAO" name="acao" checked />
                    Access-Control-Allow-Origin
                </label><br>
                <label>
                    <input type="checkbox" id="idACAM" name="acam" checked />
                    Access-Control-Allow-Methods
                </label><br>
                <label>
                    <input type="checkbox" id="idACMA" name="acma" checked />
                    Access-Control-Max-Age
                </label><br>
            </td>
        </tr>
        <tr>
            <td class="label">Max-Age (in seconds)</td>
            <td>
                <input
                    type="number"
                    id="idMaxAgeInput"
                    name="max-age"
                    min="0"
                    max="86400"
                    value="600"
                />
            </td>
        </tr>
        <tr>
            <td colspan="2" style="text-align:center;">
                <input
                    onclick="onSubmitOfForm(this.form);"
                    name="echo-php-submit-button"
                    class="button"
                    type="button"
                    value="Echo Message"
                />
            </td>
        </tr>
    </table>
</form>

<div id="idMessageOutput"></div>
