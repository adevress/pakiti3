<?php
# Copyright (c) 2011, CESNET. All rights reserved.
# 
# Redistribution and use in source and binary forms, with or
# without modification, are permitted provided that the following
# conditions are met:
# 
#   o Redistributions of source code must retain the above
#     copyright notice, this list of conditions and the following
#     disclaimer.
#   o Redistributions in binary form must reproduce the above
#     copyright notice, this list of conditions and the following
#     disclaimer in the documentation and/or other materials
#     provided with the distribution.
# 
# THIS SOFTWARE IS PROVIDED BY THE COPYRIGHT HOLDERS AND
# CONTRIBUTORS "AS IS" AND ANY EXPRESS OR IMPLIED WARRANTIES,
# INCLUDING, BUT NOT LIMITED TO, THE IMPLIED WARRANTIES OF
# MERCHANTABILITY AND FITNESS FOR A PARTICULAR PURPOSE ARE
# DISCLAIMED. IN NO EVENT SHALL THE COPYRIGHT OWNER OR CONTRIBUTORS
# BE LIABLE FOR ANY DIRECT, INDIRECT, INCIDENTAL, SPECIAL,
# EXEMPLARY, OR CONSEQUENTIAL DAMAGES (INCLUDING, BUT NOT LIMITED
# TO, PROCUREMENT OF SUBSTITUTE GOODS OR SERVICES; LOSS OF USE,
# DATA, OR PROFITS; OR BUSINESS INTERRUPTION) HOWEVER CAUSED AND ON
# ANY THEORY OF LIABILITY, WHETHER IN CONTRACT, STRICT LIABILITY,
# OR TORT (INCLUDING NEGLIGENCE OR OTHERWISE) ARISING IN ANY WAY
# OUT OF THE USE OF THIS SOFTWARE, EVEN IF ADVISED OF THE
# POSSIBILITY OF SUCH DAMAGE. 

require(realpath(dirname(__FILE__)) . '/../../../common/Loader.php');
require(realpath(dirname(__FILE__)) . '/../Html.php');

// Instantiate the HTML module
$html = new HtmlModule($pakiti);

$hostId = $html->getHttpGetVar("hostId");
$hostname = $html->getHttpGetVar("hostname");
$pageNum = $html->getHttpGetVar("pageNum", 0);
$pageSize = $html->getHttpGetVar("pageSize", HtmlModule::$DEFAULTPAGESIZE);
$sort = $html->getHttpGetVar("sortBy", "name");

if ($hostId != null) {
  $host =& $pakiti->getManager("HostsManager")->getHostById($hostId);
} else if ($hostname != null) {
  $host =& $pakiti->getManager("HostsManager")->getHostByHostname($hostname);
} else {
  $html->setError("HostId nor Hostname was supplied");
}

$html->addHtmlAttribute("title", "Host: " . $host->getHostname());

$pkgs =& $pakiti->getManager("PkgsManager")->getInstalledPkgs($host, $sort, $pageSize, $pageNum);
$pkgsCount = $pakiti->getManager("PkgsManager")->getInstalledPkgsCount($host);
$reportsCount = $pakiti->getManager("ReportsManager")->getHostReportsCount($host);

$report = $pakiti->getManager("ReportsManager")->getReportById($host->getLastReportId());

//---- Output HTML

$html->printHeader();

?>
<table class="tableDetail">
  <tr>
    <td class="header">Operating system</td>
    <td><?php print $host->getOs()->getName()?></td> 
  </tr>
  <tr>
    <td class="header">Architecture</td>
    <td><?php print $host->getArch()->getName()?></td> 
  </tr>
  <tr>
    <td class="header">Kernel</td>
    <td><?php print $host->getKernel()?></td> 
  </tr>
   <tr>
    <td class="header">Domain</td>
    <td><?php print $host->getDomain()->getName()?></td> 
  </tr>  
 <tr>
    <td class="header">Reporter Hostname/IP</td>
    <td><?php print $host->getReporterHostname()?>/<?php print $host->getReporterIp()?></td> 
  </tr>
  <tr>
    <td class="header">Installed packages</td>
    <td><?php print $pkgsCount?></td> 
  </tr>
  <tr>
    <td class="header">Last report received on</td>
    <td><?php print $report->getReceivedOn() ?></td>
  </tr>
  <tr>
    <td class="header">Reports</td>
    <td><a href="reports.php?hostId=<?php print $host->getId() ?>"><?php print $reportsCount ?></a></td>
  </tr>
</table>

<div class="paging">
<?php print $html->paging($pkgsCount, $pageSize, $pageNum) ?>
</div>

<table class="tableList">
	<tr>
		<th width="300"><a href="<?php print $html->getQueryString(array("sortBy" => "name")); ?>">Name</a></th>
		<th width="300"><a href="<?php print $html->getQueryString(array("sortBy" => "version")); ?>">Installed version</a></th>
		<th><a href="<?php print $html->getQueryString(array("sortBy" => "arch")); ?>">Architecture</a></th>
	</tr>
<?php 
  $i = 0;
  foreach ($pkgs as $pkg) { 
    $i++;
?>
	<tr class="a<?php print ($i & 1) ?>">
		<td><?php print$pkg->getPkg()->getName()?></td>
		<td><?php print$pkg->getVersionRelease()?></td>
		<td><?php print$pkg->getArch()->getName()?></td>
	</tr>
<?php } ?>
</table>

<div class="paging">
<?php print $html->paging($pkgsCount, $pageSize, $pageNum) ?>
</div>

<?php $html->printFooter(); ?>
