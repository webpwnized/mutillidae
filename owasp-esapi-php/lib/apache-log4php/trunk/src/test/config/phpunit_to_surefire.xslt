<?xml version="1.0" encoding="UTF-8"?>
<!-- 
/**
 * Licensed to the Apache Software Foundation (ASF) under one or more
 * contributor license agreements.  See the NOTICE file distributed with
 * this work for additional information regarding copyright ownership.
 * The ASF licenses this file to You under the Apache License, Version 2.0
 * (the "License"); you may not use this file except in compliance with
 * the License.  You may obtain a copy of the License at
 * 
 *      http://www.apache.org/licenses/LICENSE-2.0
 * 
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS,
 * WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 * See the License for the specific language governing permissions and
 * limitations under the License.
 */
 -->

<xsl:stylesheet version="2.0"
	xmlns:xsl="http://www.w3.org/1999/XSL/Transform" xmlns:xs="http://www.w3.org/2001/XMLSchema"
	xmlns:fn="http://www.w3.org/2005/xpath-functions">
	<xsl:output method="xml" version="1.0" encoding="UTF-8"
		indent="yes" />
	<xsl:param name="outputDir">.</xsl:param>

	<xsl:template match="testsuites">
		<xsl:apply-templates select="testsuite" />
	</xsl:template>

	<xsl:template match="testsuite">
		<xsl:if test="testcase">
			<xsl:variable name="outputName" select="./@name" />
			<xsl:result-document href="{$outputDir}/{$outputName}.xml" method="xml">
				<xsl:copy-of select="." />
			</xsl:result-document>
		</xsl:if>

		<xsl:apply-templates select="testsuite" />
	</xsl:template>
</xsl:stylesheet>
