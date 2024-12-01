<?php
    try {
        switch ($_SESSION["security-level"]) {
            default:
            case "0":
                $lEnableJavaScriptValidation = false;
                $lEnableHTMLControls = false;
                $lProtectAgainstXSS = false;
                break;

            case "1":
                $lEnableJavaScriptValidation = true;
                $lEnableHTMLControls = true;
                $lProtectAgainstXSS = false;
                break;

            case "2":
            case "3":
            case "4":
            case "5":
                $lEnableHTMLControls = true;
                $lEnableJavaScriptValidation = true;
                $lProtectAgainstXSS = true;
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

<style>
    #idMessageOutput {
        white-space: pre-wrap; /* Preserve whitespace and wrap long lines */
        font-family: monospace; /* Use a monospace font for better readability */
        background: #fdfdfd; /* Light, soft background color */
        color: #000000; /* Dark, neutral text color */
        padding: 10px; /* Add padding for aesthetics */
        border-radius: 5px; /* Round corners for a modern look */
        border: 1px solid #d8dee9; /* Subtle border to frame the content */
        box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1); /* Optional: Add a slight shadow for depth */
    }
    #idMessageOutput pre {
        margin: 0; /* Remove extra margin for <pre> when used */
    }
</style>

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

                if (this.status === 0) {
                    // Status 0 usually indicates a CORS-related issue
                    document.getElementById("idMessageOutput").innerText = 
                        "Error: The browser blocked the response. This typically happens because the 'Access-Control-Allow-Origin' header is missing or does not match the request's origin. Try enabling the ACAO header.";
                    return;
                }

                if (document.querySelector('input[name="method"]:checked').value === "OPTIONS") {
                    // OPTIONS requests do not include a response body by design
                    document.getElementById("idMessageOutput").innerText = 
                        "The OPTIONS request was sent successfully. Note that according to the CORS standard, OPTIONS responses typically do not include a response body. This is normal.";
                    return;
                }

                try {
                    // Attempt to parse and pretty print JSON
                    const jsonResponse = JSON.parse(lXMLHTTP.responseText);
                    const prettyJson = JSON.stringify(jsonResponse, null, 4);

                    <?php if ($lProtectAgainstXSS) { ?>
                        // Securely display the response (XSS protection enabled)
                        document.getElementById("idMessageOutput").innerText = prettyJson;
                    <?php } else { ?>
                        // Allow potential XSS for educational purposes (XSS protection disabled)
                        document.getElementById("idMessageOutput").innerHTML = "<pre>" + prettyJson + "</pre>";
                    <?php } ?>
                } catch (e) {
                    // Handle non-JSON responses
                    const rawResponse = lXMLHTTP.responseText;

                    <?php if ($lProtectAgainstXSS) { ?>
                        document.getElementById("idMessageOutput").innerText = rawResponse;
                    <?php } else { ?>
                        document.getElementById("idMessageOutput").innerHTML = "<pre>" + rawResponse + "</pre>";
                    <?php } ?>
                }
            }
        };

        lXMLHTTP.onerror = function() {
            document.getElementById("idMessageOutput").innerText =
                "Error: An error occurred during the request. This may be due to the lack of a proper 'Access-Control-Allow-Origin' header.";
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
