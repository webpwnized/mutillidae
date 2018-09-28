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
 * A flexible layout configurable with pattern string.
 * 
 * <p>The goal of this class is to {@link format()} a {@link LoggerLoggingEvent} and return the results as a string.
 * The results depend on the conversion pattern. 
 * The conversion pattern is closely related to the conversion pattern of the printf function in C.
 * A conversion pattern is composed of literal text and format control expressions called conversion specifiers.
 * You are free to insert any literal text within the conversion pattern.</p> 
 *
 * <p>Each conversion specifier starts with a percent sign (%) and is followed by optional 
 * format modifiers and a conversion character.</p>
 * 
 * <p>The conversion character specifies the type of data, e.g. category, priority, date, thread name. 
 * The format modifiers control such things as field width, padding, left and right justification.</p>
 * 
 * The following is a simple example.
 * 
 * <p>Let the conversion pattern be "%-5p [%t]: %m%n" and assume that the log4php environment 
 * was set to use a LoggerPatternLayout.</p> 
 * 
 * Then the statements
 * <code> 
 *  $root = Logger::getRoot();
 *  $root->debug("Message 1");
 *  $root->warn("Message 2");
 * </code>
 * would yield the output 
 * <pre>
 *  DEBUG [main]: Message 1
 *  WARN  [main]: Message 2
 * </pre>
 * 
 * <p>Note that there is no explicit separator between text and conversion specifiers.</p>
 * 
 * <p>The pattern parser knows when it has reached the end of a conversion specifier when it reads a conversion character. 
 * In the example above the conversion specifier %-5p means the priority of the logging event should be 
 * left justified to a width of five characters.</p> 
 *
 * Not all log4j conversion characters are implemented. The recognized conversion characters are:
 * - <b>c</b> Used to output the category of the logging event. The category conversion specifier can be optionally followed by precision specifier, that is a decimal constant in brackets. 
 *         If a precision specifier is given, then only the corresponding number of right most components of the category name will be printed. 
 *         By default the category name is printed in full. 
 *         For example, for the category name "a.b.c" the pattern %c{2} will output "b.c". 
 * - <b>C</b> Used to output the fully qualified class name of the caller issuing the logging request. 
 *         This conversion specifier can be optionally followed by precision specifier, that is a decimal constant in brackets. 
 *         If a precision specifier is given, then only the corresponding number of right most components of the class name will be printed. 
 *         By default the class name is output in fully qualified form. 
 *         For example, for the class name "org.apache.xyz.SomeClass", the pattern %C{1} will output "SomeClass". 
 * - <b>d</b> Used to output the date of the logging event. 
 *         The date conversion specifier may be followed by a date format specifier enclosed between braces.
 *         The format specifier follows the {@link PHP_MANUAL#date} function.
 *         Note that the special character <b>u</b> is used to as microseconds replacement (to avoid replacement,
 *         use <b>\u</b>).  
 *         For example, %d{H:i:s,u} or %d{d M Y H:i:s,u}. If no date format specifier is given then ISO8601 format is assumed. 
 *         The date format specifier admits the same syntax as the time pattern string of the SimpleDateFormat. 
 *         It is recommended to use the predefined log4php date formatters. 
 *         These can be specified using one of the strings "ABSOLUTE", "DATE" and "ISO8601" for specifying 
 *         AbsoluteTimeDateFormat, DateTimeDateFormat and respectively ISO8601DateFormat. 
 *         For example, %d{ISO8601} or %d{ABSOLUTE}. 
 * - <b>F</b> Used to output the file name where the logging request was issued. 
 * - <b>l</b> Used to output location information of the caller which generated the logging event. 
 * - <b>L</b> Used to output the line number from where the logging request was issued.
 * - <b>m</b> Used to output the application supplied message associated with the logging event.
 * - <b>M</b> Used to output the method name where the logging request was issued.  
 * - <b>p</b> Used to output the priority of the logging event.
 * - <b>r</b> Used to output the number of milliseconds elapsed since the start of 
 *            the application until the creation of the logging event. 
 * - <b>t</b> Used to output the name of the thread that generated the logging event.
 * - <b>x</b> Used to output the NDC (nested diagnostic context) associated with 
 *            the thread that generated the logging event.  
 * - <b>X</b> Used to output the MDC (mapped diagnostic context) associated with 
 *            the thread that generated the logging event. 
 *            The X conversion character must be followed by the key for the map placed between braces, 
 *            as in <i>%X{clientNumber}</i> where clientNumber is the key.
 *            The value in the MDC corresponding to the key will be output.
 *            See {@link LoggerMDC} class for more details. 
 * - <b>%</b> The sequence %% outputs a single percent sign.  
 *
 * <p>By default the relevant information is output as is. 
 *  However, with the aid of format modifiers it is possible to change the minimum field width, 
 *  the maximum field width and justification.</p> 
 *
 * <p>The optional format modifier is placed between the percent sign and the conversion character.</p>
 * <p>The first optional format modifier is the left justification flag which is just the minus (-) character. 
 *  Then comes the optional minimum field width modifier. 
 *  This is a decimal constant that represents the minimum number of characters to output. 
 *  If the data item requires fewer characters, it is padded on either the left or the right until the minimum width is reached. The default is to pad on the left (right justify) but you can specify right padding with the left justification flag. The padding character is space. If the data item is larger than the minimum field width, the field is expanded to accommodate the data. 
 *  The value is never truncated.</p>
 * 
 * <p>This behavior can be changed using the maximum field width modifier which is designated by a period 
 *  followed by a decimal constant. 
 *  If the data item is longer than the maximum field, 
 *  then the extra characters are removed from the beginning of the data item and not from the end. 
 *  For example, it the maximum field width is eight and the data item is ten characters long, 
 *  then the first two characters of the data item are dropped. 
 *  This behavior deviates from the printf function in C where truncation is done from the end.</p> 
 *
 * <p>Below are various format modifier examples for the category conversion specifier.</p> 
 * <pre>
 *   Format modifier  left justify  minimum width  maximum width  comment
 *   %20c             false         20             none           Left pad with spaces if the category name 
 *                                                                is less than 20 characters long.
 *   %-20c            true          20             none           Right pad with spaces if the category name 
 *                                                                is less than 20 characters long.  
 *   %.30c            NA            none           30             Truncate from the beginning if the category name 
 *                                                                is longer than 30 characters.  
 *   %20.30c          false         20             30             Left pad with spaces if the category name 
 *                                                                is shorter than 20 characters. 
 *                                                                However, if category name is longer than 30 chars, 
 *                                                                then truncate from the beginning.  
 *   %-20.30c         true          20             30             Right pad with spaces if the category name is 
 *                                                                shorter than 20 chars. 
 *                                                                However, if category name is longer than 30 chars, 
 *                                                                then truncate from the beginning.  
 * </pre>
 *
 * @version $Revision: 800094 $
 * @package log4php
 * @subpackage layouts
 * @since 0.3 
 */
class LoggerLayoutPattern extends LoggerLayout {
	/** Default conversion Pattern */
	const DEFAULT_CONVERSION_PATTERN = '%m%n';

	/** Default conversion TTCC Pattern */
	const TTCC_CONVERSION_PATTERN = '%r [%t] %p %c %x - %m%n';

	/** @var string */
	private $pattern;

	/** @var LoggerPatternConverter head chain */
	private $head;

	private $timezone;

	/**
	 * Constructs a PatternLayout using the 
	 * {@link DEFAULT_LAYOUT_PATTERN}.
	 * The default pattern just produces the application supplied message.
	 */
	public function __construct($pattern = null) {
		if ($pattern === null) {
			$this->pattern = self :: DEFAULT_CONVERSION_PATTERN;
		} else {
			$this->pattern = $pattern;
		}
	}

	/**
	 * Set the <b>ConversionPattern</b> option. This is the string which
	 * controls formatting and consists of a mix of literal content and
	 * conversion specifiers.
	 */
	public function setConversionPattern($conversionPattern) {
		$this->pattern = $conversionPattern;
		$patternParser = new LoggerPatternParser($this->pattern);
		$this->head = $patternParser->parse();
	}

	/**
	 * Produces a formatted string as specified by the conversion pattern.
	 *
	 * @param LoggerLoggingEvent $event
	 * @return string
	 */
	public function format(LoggerLoggingEvent $event) {
		$sbuf = '';
		$c = $this->head;
		while ($c !== null) {
			$c->format($sbuf, $event);
			$c = $c->next;
		}
		return $sbuf;
	}
}