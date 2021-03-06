<?php
/**
 * load.php
 * Alex Aylwin
 * 01/08/2013
 * 
 * This script is used by the client to load block content onto
 * a front-end page. It is not used by the admin panel. It can
 * either be called directly (by setting $block and $bucket) before
 * the include, or called via AJAX (as with loader.js)
 */
include_once __DIR__ . '/src/framework/classloader.php';
function load_container($bucketid, $blockid) {
	$elements[$bucketid] = $blockid;
	$controller = new FrameworkController();
	$content = $controller->getContent($elements);
	return $content;
}

if (isset($_GET['bucket']) && isset($_GET['block'])) {
	echo load_container($_GET['bucket'], $_GET['block']);
} else if (isset($bucket) && isset($block)) {
	echo load_container($bucket, $block);
} else {
	echo "";
}
?>