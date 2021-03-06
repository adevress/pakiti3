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

$hostGroupId = $html->getHttpGetVar("hostGroupId", 0);
$hostGroupName = $html->getHttpGetVar("hostGroupName", 0);
$pageNum = $html->getHttpGetVar("pageNum", 0);
$pageSize = $html->getHttpGetVar("pageSize", HtmlModule::$DEFAULTPAGESIZE);
$sort = $html->getHttpGetVar("sortBy", "name");

if ($hostGroupId != 0) {
  $hostGroup =& $pakiti->getManager("HostGroupsManager")->getHostGroupById($hostGroupId);
} else if ($hostGroupName != null) {
  $hostGroup =& $pakiti->getManager("HostGroupsManager")->getHostGroupByName($hostGroupName);
} else {
  $html->setError("HostGroupId nor HostGroupName was supplied");
}

$html->addHtmlAttribute("title", "Host Group: " . $hostGroup->getName());

$hostsCount = $pakiti->getManager("HostGroupsManager")->getHostsCount($hostGroup);
$hosts = $pakiti->getManager("HostGroupsManager")->getHosts($hostGroup, $sort, $pageSize, $pageNum);

//---- Output HTML

$html->printHeader();

# Print table with hosts
?>

<table class="tableDetail">
  <tr>
    <td class="header">Name</td>
    <td><?php print $hostGroup->getName()?></td>
  </tr>
  <tr>
    <td class="header">Hosts Count</td>
    <td><?php print $hostsCount; ?></td>
  </tr>
</table>

<div class="paging">
<?php print $html->paging($hostsCount, $pageSize, $pageNum) ?>
</div>

<?php $html->printHosts($hosts); ?>

<div class="paging">
<?php print $html->paging($hostsCount, $pageSize, $pageNum) ?>
</div>

<?php $html->printFooter(); ?>
