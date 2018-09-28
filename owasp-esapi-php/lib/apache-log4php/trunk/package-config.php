<?php
/**
 * Licensed to the Apache Software Foundation (ASF) under one or more
 * contributor license agreements. See the NOTICE file distributed with
 * this work for additional information regarding copyright ownership.
 * The ASF licenses this file to You under the Apache License, Version 2.0
 * (the "License"); you may not use this file except in compliance with
 * the License. You may obtain a copy of the License at
 *
 *     http://www.apache.org/licenses/LICENSE-2.0
 *
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS,
 * WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 * See the License for the specific language governing permissions and
 * limitations under the License.
 */

$name = 'log4php';
$summary = 'log4Php is a PHP port of log4j framework';
$version = '2.0.0';
$versionBuild = 'b1';
$apiVersion = '2.0.0';
$state = 'beta';
$apiStability = 'stable';

$description = <<<EOT
log4Php is a PHP port of log4j framework. It supports XML configuration, 
logging to files, stdout/err, syslog, socket, configurable output layouts 
and logging levels.
EOT;

$notes = <<<EOT
Changes since 0.9:
 - 
EOT;

$options = array(
	'license' => 'Apache License 2.0',
	//'filelistgenerator' => 'svn',
	'ignore' => array('package.php', 'package-config.php'),
	'simpleoutput' => true,
	'baseinstalldir' => '/',
	'packagedirectory' => '.',
	'dir_roles' => array(
		'examples' => 'doc',
	),
	'exceptions' => array(
		'CHANGELOG' => 'doc',
		'LICENSE' => 'doc',
		'README' => 'doc',
	),
);

$license = array(
	'name' => 'Apache License 2.0',
	'url' => 'http://www.apache.org/licenses/LICENSE-2.0'
);

$maintainer = array();
$maintainer[]  =   array(
	'role' => 'lead',
	'handle' => 'kurdalen',
	'name' => 'Knut Urdalen',
	'email' => 'kurdalen@apache.org',
	'active' => 'yes'
);
$maintainer[]   =   array(
	'role' => 'lead',
	'handle' => 'grobmeier',
	'name' => 'Christian Grobmeier',
	'email' => 'grobmeier@gmail.com',
	'active' => 'yes'
);

$dependency = array();

$channel = 'pear.php.net';
$require = array(
	'php' => '5.2.0',
	'pear_installer' => '1.8.0',
);
