<?php
/*
 * This script contains the logic and UI for initializing a new instance of
 * BarelyACMS. It will be deleted or disabled after the initialization is complete.
 * 
 * This is the setup script for the first install. As of 1.0, it only has two
 * user configurable options - admin password and creating the sitemap.
 *
 * To set up the BAC installation, run this file. It will first ask for an
 * admin password, then ask the user to enter each page and container
 * on that page that they want managed by BAC.
 *
 * The mechanism for the sitemap is currently manual. Ideally, this will
 * be replaced by an automatic funciton that can do one of two things:
 * 1)	Scan the web root and look at each file, determining if the file
 * 		has BAC managed containers
 * 2)	Be able to securely and reliably identify Ajax calls from the
 * 		installation domain, and automatically generate content files
 * 		at an ajax request, if the file does not exist.
 */
?>

<?php

//Handle a post here, returning data to the rest of the page in a $data array
include_once ('../src/util.php');
require 'SetupHandler.php';

$requestHandler = new SetupHandler();
$data = $requestHandler->handleRequest($_POST, $_GET);

if(isset($data['redirectToLogin']) && $data['redirectToLogin'] == true)
{
	header("Location: " . get_bac_uri("/admin/login.php"));
	die();
}
$displaymessage = "";
if(!isset($data['message'])){$data['message'] = "";}
if($data['message'])
{
	$displaymessage = "block";
} else {
	$displaymessage = "none";
}


$messageclass = 'alert-success';
if(isset($data['settingsSaved']))
{
	if(!$data['settingsSaved'])
	{
		$messageclass = "alert-error";
	}
}


?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html>
<head>
		<link href="../admin/bootstrap/css/bootstrap.min.css" rel="stylesheet" media="screen" />
		<script src="../admin/js/jquery.min.js" type="text/javascript"></script>
		<script src="../admin/bootstrap/js/bootstrap.min.js" type="text/javascript"></script>
		<link href="../admin/styles/styles.css" rel="stylesheet" media="screen" />
		<title>BarelyACMS - Setup</title>
</head>	
<body>
	<div class="container-fluid">
	   	<div class="navbar navbar-fixed-top navbar-inverse" id="header">
 			<div class="navbar-inner" id="menubar">
    			<a class="brand" href="#" id="titlebar">BAC</a>

   				<div id="logout" class="pull-right">
					<a href=""><i class="icon-off invisible"></i> Domain <br /></a>
					<a href="logout.php"><i class="icon-off icon-white"></i> Logout</a>
   			</div>
   			</div>
    	</div>
		<div class="row-fluid">
			<div class="span10 offset1">
				<div id="content">					
				
				<script type="text/javascript" src="setupform.js"></script>	
					<p>
						<h3>Welcome to BarelyACMS!</h3>
							BAC is a new way of thinking about content management systems.
						BAC believes in doing only one thing, <strong>managing content</strong>. <br /> <br />
						
						It doesn't help you create themes, layouts or even new pages. It won't do CSS for you, 
						it won't provide plugins to help you set up an email list or twitter feed. <br /> <br />
						
						What it will do is <strong>promise to not get in the way</strong>. It isn't designed to be
						a website building solution, but simply a way for users to manage the content on their website.
						BAC lets you, the web designer, focus on design your website hassle free and create the
						content later.						
					</p>
					<br />
					<p>
						<h4>Setup</h4>
						<div class="alert <?php echo $messageclass; ?>" style="display:<?php echo $displaymessage; ?>;
"><?php echo $data['message']; ?><button type="button" class="close" data-dismiss="alert">&times;</button></div>
						To set up BAC, set up an administrative password and then define your sitemap.
					</p>
					
					<form method="post" class="form-horizontal" id="setupform">
<!-- ADMIN PASSWORD -->
						<h6>Admin Account</h6>
						<p>
							Define the administrative password that you'll use to
							access the BAC admin panel.
						</p>
						<div class="control-group">
							<label class="control-label" for"username">Username</label>
							<div class="controls">
								<p style="display:inline-block; margin-bottom:0; margin-top: 1px;vertical-align:middle;font-size:14px;height:20px;line-height:20px;padding:4px 6px;"><strong>admin</strong></p>
							</div>
						</div>
						<div class="control-group">
							<label class="control-label" for="adminPassword">Password</label>
							<div class="controls">
								<input type="password" name="adminPassword" id="adminPassword">
							</div>
						</div>
						<div class="control-group">
							<label class="control-label" for="adminPasswordConfirm">Confirm password</label>
							<div class="controls">
								<input type="password" name="adminPasswordConfirm" id="adminPasswordConfirm">
							</div>
						</div>
											
<!-- AUTHOR PASSWORD -->						
						<h6>Author Account</h6>
						<p>
							The author account is a user who is able to alter content and add blog posts,
							but cannot add or delete buckets or blocks themselves.
						</p>
						<div class="control-group">
							<label class="control-label" for"username">Username</label>
							<div class="controls">
								<p style="display:inline-block; margin-bottom:0; margin-top: 1px;vertical-align:middle;font-size:14px;height:20px;line-height:20px;padding:4px 6px;"><strong>author</strong></p>
							</div>
						</div>
						<div class="control-group">
							<label class="control-label" for="authorPassword">Password</label>
							<div class="controls">
								<input type="password" name="authorPassword" id="authorPassword">
							</div>
						</div>
						<div class="control-group">
							<label class="control-label" for="authorPasswordConfirm">Confirm password</label>
							<div class="controls">
								<input type="password" name="authorPasswordConfirm" id="authorPasswordConfirm">
							</div>
						</div>
<!-- SITEMAP -->	
		
						<h6>Sitemap</h6>
						<p>
							BAC works by letting you include content containers dynamically within a page. This
							section lets you pre-define the pages and containers that will exist within your
							website. You can modify this later, but it is recommended that you set up a skeleton
							framework of the site (even if its just an index page!).
						</p>
						<ul id="controllist" class="unstyled"></ul>
						<input name="sitemap" id="sitemap" type="hidden" value=""/>
						<input name="submitted" type="hidden" value="1" />
						<br />


<button class="btn btn-custom" id="save" type="button" onclick="javascript:$('#savemodal').modal();">
Save Configuration
</button>
</form>

<div id="savemodal" class="modal hide fade">
	<div class="modal-header">
		<button type="button" class="close" data-dismiss="modal" aria-hidden="true">
			&times;
		</button>
		<h3>You're done!</h3>
	</div>
	<div class="modal-body">
		<p>
			That's it! When you click 'Continue', you will be taken to the BAC Administrative Panel login page, where you will be asked to log in with the following credentials: <br />
			Username: admin <br />
			Password: <i>User defined</i>
			<br /> <br />
			To change any of these settings, log into the admin panel and go to Setup
		</p>
	</div>
	<div class="modal-footer">
		<a href="#" class="btn" id="cancel" data-dismiss="modal" aria-hidden="true">Cancel</a>
		
		<? //This posts setupform ?>
		<a href="#" class="btn btn-primary" id="continue">Continue</a>
	</div>
</div>

<?php
include '../admin/footer.php';
?>