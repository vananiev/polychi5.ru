<?php require_once($_SERVER['DOCUMENT_ROOT']."/../../before.php"); ?>
<?php require_once(MOBILE_ROOT."/head.php"); ?>
<?php require(MOBILE_ROOT.'/menu.php') ?>	

<div class="mt-content-auto mt-indent"><div id="main">

<?php require(SCRIPT_ROOT.'/'.$INCLUDE_MODULES[$URL['MODULE']]['PATH'].'/'.$URL['FILE'].'.php'); ?>

</div></div>

<?php require_once(MOBILE_ROOT."/footer.php"); ?>