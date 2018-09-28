<?php
/**
 * Licensed to the Apache Software Foundation (ASF) under one or more
 * contributor license agreements.  See the NOTICE file distributed with
 * this work for additional information regarding copyright ownership.
 * The ASF licenses this file to You under the Apache License, Version 2.0
 * (the "License"); you may not use this file except in compliance with
 * the License.  You may obtain a copy of the License at
 *
 *     http://www.apache.org/licenses/LICENSE-2.0
 *
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS,
 * WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 * See the License for the specific language governing permissions and
 * limitations under the License.
 *
 */

/**
 * This layout outputs events in a HTML table.
 *
 * Parameters are: {@link $title}, {@link $locationInfo}.
 *
 * @version $Revision: 795643 $
 * @package log4php
 * @subpackage layouts
 */
class LoggerLayoutHtml extends LoggerLayout {

    /**
     * The <b>LocationInfo</b> option takes a boolean value. By
     * default, it is set to false which means there will be no location
     * information output by this layout. If the the option is set to
     * true, then the file name and line number of the statement
     * at the origin of the log statement will be output.
     *
     * <p>If you are embedding this layout within a {@link LoggerAppenderMail}
     * or a {@link LoggerAppenderMailEvent} then make sure to set the
     * <b>LocationInfo</b> option of that appender as well.
     * @var boolean
     */
    private $locationInfo = false;
    
    /**
     * The <b>Title</b> option takes a String value. This option sets the
     * document title of the generated HTML document.
     * Defaults to 'Log4php Log Messages'.
     * @var string
     */
    private $title = "Log4php Log Messages";
    
    /**
     * Constructor
     */
    public function __construct() {
    }
    
    /**
     * The <b>LocationInfo</b> option takes a boolean value. By
     * default, it is set to false which means there will be no location
     * information output by this layout. If the the option is set to
     * true, then the file name and line number of the statement
     * at the origin of the log statement will be output.
     *
     * <p>If you are embedding this layout within a {@link LoggerAppenderMail}
     * or a {@link LoggerAppenderMailEvent} then make sure to set the
     * <b>LocationInfo</b> option of that appender as well.
     */
    public function setLocationInfo($flag) {
        if (is_bool($flag)) {
            $this->locationInfo = $flag;
        } else {
            $this->locationInfo = (bool)(strtolower($flag) == 'true');
        }
    }

    /**
     * Returns the current value of the <b>LocationInfo</b> option.
     */
    public function getLocationInfo() {
        return $this->locationInfo;
    }
    
    /**
     * The <b>Title</b> option takes a String value. This option sets the
     * document title of the generated HTML document.
     * Defaults to 'Log4php Log Messages'.
     */
    public function setTitle($title) {
        $this->title = $title;
    }

    /**
     * @return string Returns the current value of the <b>Title</b> option.
     */
    public function getTitle() {
        return $this->title;
    }
    
    /**
     * @return string Returns the content type output by this layout, i.e "text/html".
     */
    public function getContentType() {
        return "text/html";
    }
    
    /**
     * @param LoggerLoggingEvent $event
     * @return string
     */
    public function format(LoggerLoggingEvent $event) {
        $sbuf = PHP_EOL . "<tr>" . PHP_EOL;
    
        $sbuf .= "<td>";
        $sbuf .= $event->getTime();
        $sbuf .= "</td>" . PHP_EOL;
    
        $sbuf .= "<td title=\"" . $event->getThreadName() . " thread\">";
        $sbuf .= $event->getThreadName();
        $sbuf .= "</td>" . PHP_EOL;
    
        $sbuf .= "<td title=\"Level\">";
        
        $level = $event->getLevel();
        
        if ($level->equals(LoggerLevel::getLevelDebug())) {
          $sbuf .= "<font color=\"#339933\">";
          $sbuf .= $level->toString();
          $sbuf .= "</font>";
        } else if ($level->equals(LoggerLevel::getLevelWarn())) {
          $sbuf .= "<font color=\"#993300\"><strong>";
          $sbuf .= $level->toString();
          $sbuf .= "</strong></font>";
        } else {
          $sbuf .= $level->toString();
        }
        $sbuf .= "</td>" . PHP_EOL;
    
        $sbuf .= "<td title=\"" . htmlentities($event->getLoggerName(), ENT_QUOTES) . " category\">";
        $sbuf .= htmlentities($event->getLoggerName(), ENT_QUOTES);
        $sbuf .= "</td>" . PHP_EOL;
    
        if ($this->locationInfo) {
            $locInfo = $event->getLocationInformation();
            $sbuf .= "<td>";
            $sbuf .= htmlentities($locInfo->getFileName(), ENT_QUOTES). ':' . $locInfo->getLineNumber();
            $sbuf .= "</td>" . PHP_EOL;
        }

        $sbuf .= "<td title=\"Message\">";
        $sbuf .= htmlentities($event->getRenderedMessage(), ENT_QUOTES);
        $sbuf .= "</td>" . PHP_EOL;

        $sbuf .= "</tr>" . PHP_EOL;
        
        if ($event->getNDC() != null) {
            $sbuf .= "<tr><td bgcolor=\"#EEEEEE\" style=\"font-size : xx-small;\" colspan=\"6\" title=\"Nested Diagnostic Context\">";
            $sbuf .= "NDC: " . htmlentities($event->getNDC(), ENT_QUOTES);
            $sbuf .= "</td></tr>" . PHP_EOL;
        }
        return $sbuf;
    }

    /**
     * @return string Returns appropriate HTML headers.
     */
    public function getHeader() {
        $sbuf = "<!DOCTYPE HTML PUBLIC \"-//W3C//DTD HTML 4.01 Transitional//EN\" \"http://www.w3.org/TR/html4/loose.dtd\">" . PHP_EOL;
        $sbuf .= "<html>" . PHP_EOL;
        $sbuf .= "<head>" . PHP_EOL;
        $sbuf .= "<title>" . $this->title . "</title>" . PHP_EOL;
        $sbuf .= "<style type=\"text/css\">" . PHP_EOL;
        $sbuf .= "<!--" . PHP_EOL;
        $sbuf .= "body, table {font-family: arial,sans-serif; font-size: x-small;}" . PHP_EOL;
        $sbuf .= "th {background: #336699; color: #FFFFFF; text-align: left;}" . PHP_EOL;
        $sbuf .= "-->" . PHP_EOL;
        $sbuf .= "</style>" . PHP_EOL;
        $sbuf .= "</head>" . PHP_EOL;
        $sbuf .= "<body bgcolor=\"#FFFFFF\" topmargin=\"6\" leftmargin=\"6\">" . PHP_EOL;
        $sbuf .= "<hr size=\"1\" noshade>" . PHP_EOL;
        $sbuf .= "Log session start time " . strftime('%c', time()) . "<br>" . PHP_EOL;
        $sbuf .= "<br>" . PHP_EOL;
        $sbuf .= "<table cellspacing=\"0\" cellpadding=\"4\" border=\"1\" bordercolor=\"#224466\" width=\"100%\">" . PHP_EOL;
        $sbuf .= "<tr>" . PHP_EOL;
        $sbuf .= "<th>Time</th>" . PHP_EOL;
        $sbuf .= "<th>Thread</th>" . PHP_EOL;
        $sbuf .= "<th>Level</th>" . PHP_EOL;
        $sbuf .= "<th>Category</th>" . PHP_EOL;
        if ($this->locationInfo)
            $sbuf .= "<th>File:Line</th>" . PHP_EOL;
        $sbuf .= "<th>Message</th>" . PHP_EOL;
        $sbuf .= "</tr>" . PHP_EOL;

        return $sbuf;
    }

    /**
     * @return string Returns the appropriate HTML footers.
     */
    public function getFooter() {
        $sbuf = "</table>" . PHP_EOL;
        $sbuf .= "<br>" . PHP_EOL;
        $sbuf .= "</body></html>";

        return $sbuf;
    }
}
