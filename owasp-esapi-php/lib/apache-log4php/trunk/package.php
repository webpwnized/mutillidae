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

@include 'PEAR/PackageFileManager2.php';
if(!class_exists('PEAR_PackageFileManager2')) {
	echo "\nYou need to install PEAR_PackageFileManager2 in order to run this script\n\n";
	echo "Installation tips:\n\n";
	echo "  $ sudo pear upgrade PEAR\n";
	echo "  $ sudo pear install XML_Serializer-0.19.2\n";
	echo "  $ sudo pear install --alldeps PEAR_PackageFileManager2\n\n";
	exit(0);
}

include dirname(__FILE__).'/package-config.php';

$package = new PEAR_PackageFileManager2();
$result = $package->setOptions($options);
if(PEAR::isError($result)) {
    echo $result->getMessage();
    die( __LINE__ . "\n" );
}

$package->setPackage($name);
$package->setSummary($summary);
$package->setDescription($description);

$package->setChannel($channel);
$package->setAPIVersion($apiVersion);
$package->setReleaseVersion($version);
$package->setReleaseStability($state);
$package->setAPIStability($apiStability);
$package->setNotes($notes);
$package->setPackageType('php'); // this is a PEAR-style php script package
$package->setLicense($license['name'], $license['url']);

foreach($maintainer as $m) {
	$package->addMaintainer($m['role'], $m['handle'], $m['name'], $m['email'], $m['active']);
}

foreach($dependency as $d) {
    $package->addPackageDepWithChannel($d['type'], $d['package'], $d['channel'], $d['version']);
}

$package->setPhpDep( $require['php'] );
$package->setPearinstallerDep($require['pear_installer']);

$package->generateContents();

$package->debugPackageFile();

$result = $package->writePackageFile();
if(PEAR::isError($result)) {
    echo $result->getMessage();
    die();
}
exit(0);
