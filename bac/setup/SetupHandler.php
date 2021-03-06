<?php
require_once __DIR__. '/../src/framework/classloader.php';
class SetupHandler extends RequestHandler
{
	protected function handleGet()
	{
		$ret = array();		
		if($this->adminExists())
		{
			$ret['redirectToLogin'] = true;
		}
		if($this->contentExists())
		{
			$ret['message'] = <<<EOM
			Warning: There was content found in this BAC installation. Using this form
			may erase some or all of your existing data, or break the BAC installation
			completely. 
			<br />
			Please log in to the administrator account, and use the settings
			page to change BAC settings.
EOM;
		}
		return $ret;
	}
		
	protected function handlePost()
	{
		if($this->adminExists())
		{
			$ret['redirectToLogin'] = true;
			return;
		}
		
		/**
		 * This section updates the administration password to the user supplied value
		 */
		$ret['message'] = 'Settings saved';
		$ret['settingsSaved'] = false;
		$passwordDefined = true;
		
//HANDLE ADMIN ACCOUNT
		if (isset($this->post['adminPassword']) && !empty($this->post['adminPassword'])) {
			if (isset($this->post['adminPasswordConfirm']) && ($this->post['adminPassword'] == $this->post['adminPasswordConfirm'])) {
				$newpass = sha1($this->post['adminPassword']);
				$newuser = 'admin';
				$usertype = UserTypes::Admin;
				
				//Create default permissions
				$pagePermissions = array();
				$bucketsPermissions = new PagePermissions(array(PagePermissions::c_pagename => 'buckets', 
					PagePermissions::c_actionPermissions => array(
						'all' => ActionPermissions::Allowed
				)));
				$pagePermissions['buckets'] = $bucketsPermissions;
				
				$setupPermissions = new PagePermissions(array(PagePermissions::c_pagename => 'settings', 
					PagePermissions::c_actionPermissions => array(
						'all' => ActionPermissions::Allowed
				)));
				$pagePermissions['settings'] = $setupPermissions;

				//create new user
				$userCreated = $this->createUser($newuser, $newpass, $usertype, $pagePermissions);
				
				if (!$userCreated) {
					$ret['message'] = "Settings could not be saved";
					$ret['settingsSaved'] = 'false';
					$ret['redirectToLogin'] = 'false';
					$passwordDefined = false;
				}
			} else {
				//return 'error, passwords don't match' message
				$ret['message'] = "Admin passwords do not match";
				$ret['settingsSaved'] = 'false';
				$ret['redirectToLogin'] = 'false';
				$passwordDefined = false;
			}
		} else {
			//Error, user didn't define a password
			$ret['message'] = "A password must be specified";
			$ret['settingsSaved'] = false;
			$ret['redirectToLogin'] = false;
			$passwordDefined = false;
		}
		
//HANDLE AUTHOR ACCOUNT
		if (isset($this->post['authorPassword']) && !empty($this->post['authorPassword'])) {
			if (isset($this->post['authorPasswordConfirm']) && ($this->post['authorPasswordConfirm'] == $this->post['authorPasswordConfirm'])) {
				$newpass = sha1($this->post['authorPassword']);
				$newuser = 'author';
				$usertype = UserTypes::Author;
				
				//Create default permissions
				//TODO: Why are we resetting permissions on a password change?
				//This should only happen if it is a new user (i.e. the user doesn't
				//exist). Change this when permissions are modifiable.
				$pagePermissions = array();
				$bucketsPermissions = new PagePermissions(array(PagePermissions::c_pagename => 'buckets', 
					PagePermissions::c_actionPermissions => array(
						'createBucket' => ActionPermissions::Denied,
						'deleteBucket' => ActionPermissions::Denied,
						'createBlock' => ActionPermissions::Denied,
						'deleteBlock' => ActionPermissions::Denied,
						'createBlogBlock' => ActionPermissions::Allowed,
						'deleteBlogBlock' => ActionPermissions::Allowed,
						'editBlock' => ActionPermissions::Allowed
					)));
				$pagePermissions['buckets'] = $bucketsPermissions;
				
				$setupPermissions = new PagePermissions(array(PagePermissions::c_pagename => 'settings', 
					PagePermissions::c_actionPermissions => array(
						'createBucket' => ActionPermissions::Denied,
						'deleteBucket' => ActionPermissions::Denied,
						'createBlock' => ActionPermissions::Denied,
						'deleteBlock' => ActionPermissions::Denied,
						'changeAdminPassword' => ActionPermissions::Denied,
						'changeAuthorPassword' => ActionPermissions::Allowed
					)));
				
				$pagePermissions['settings'] = $setupPermissions;
				
				//Create new user
				$userCreated = $this->createUser($newuser, $newpass, $usertype, $pagePermissions);
				
				if (!$userCreated) {
					$ret['message'] = "Settings could not be saved";
					$ret['settingsSaved'] = 'false';
					$ret['redirectToLogin'] = 'false';
					$passwordDefined = false;
				}
			} else {
				//return 'error, passwords don't match' message
				$ret['message'] = "Author passwords do not match";
				$ret['settingsSaved'] = false;
				$ret['redirectToLogin'] = false;
				$passwordDefined = false;
			}
		}

		/*
		 * This section creates the directories and files for the buckets
		 */
		if (isset($this->post['sitemap']) && $passwordDefined) {
	
			//Get the existing site for comparison
			$framework = new FrameworkController();
			$site = $framework->getSite();
			$bucketlist = $site -> getAllBuckets();
			$blockarray = array();
			$bucketarray = array();
			
			//This loop marks every block and bucket for deletion
			foreach ($bucketlist as $bucket) {
				if ($bucket -> hasBlocks()) {
					$blocklist = $bucket -> getAllBlocks();
					foreach ($blocklist as $block) {
						$blockarray[$bucket -> getBucketId()][$block -> getBlockId()] = false;
					}
				}
				$bucketarray[$bucket -> getBucketId()] = false;
			}
	
			//The string looks like:
			//page1:container1,container2,container3|page2:container1,container2,container3
			$sitemap_string = $this->post['sitemap'];
	
		
			$bucketsArray = explode("|", $sitemap_string);
			$bucketsdir = Constants::GET_PAGES_DIRECTORY();

			for ($i = 0; $i < count($bucketsArray); $i++) {
				if (strlen($bucketsArray[$i]) <= 1) {
					continue;
				}
				//Get the bucket and block list from the bucket string
				$bucketstring = explode(":", $bucketsArray[$i]);
				$bucketname = $bucketstring[0];
				$blockstring = $bucketstring[1];
				$blockNameArray = explode(",", $blockstring);
	
				//If we have no bucket name, then there's nothing to
				//do here (no bucket page names)
				if ($bucketname == null || $bucketname == "") {
					continue;
				}
	
				//If the directory exists, we don't want to overwrite it
				//This adds the page
				$bucketarray[$bucketname] = true;
				$result = $site->addBucket($bucketname);
	
				$blocktext = "";
	
				for ($j = 0; $j < count($blockNameArray); $j++) {
					$blockname = $blockNameArray[$j];
	
					//If the block name is blank do nothing (no blank bucket names)
					if ($blockname == null || $blockname == "") {
						continue;
					}
					//Don't delete this block!
					if (isset($blockarray[$bucketname][$blockname])) {
						$blockarray[$bucketname][$blockname] = true;
					}
	
					//TODO: this needs to be refactored, we aren't creating a new block every time, only loading
					//blocks that exist.
					$bucket = $site->getBucket($bucketname);
					if(!$bucket->hasBlock($blockname))
					{
						$newblockConfig = Array(
							"type" => BlockTypes::Text,
							"blockid" => $blockname,
							"bucketid" =>$bucketname
						);
						$factory = new BlockFactory();
						$newblock = $factory->build($newblockConfig);
						$bucket->addBlock($newblock);
					}
					
				}
			}
	
			//Now delete all the blocks that are still in the array as false
			foreach ($blockarray as $bucketname => $blocklist) {
				foreach ($blocklist as $blockname => $exists) {
					if (!$exists) {
						$currentbucket = $site->getBucket($bucketname);
						$result = $currentbucket->removeBlock($blockname);
					}
				}
			}
	
			//Now delete the bucket
			foreach ($bucketarray as $bucketname => $exists) {
				$path = $bucketsdir . "/" . $bucketname;
				if (!$exists) {
					$deleted = $site->removeBucket($bucketname);
				}
			}
	
		} else {
			//No site map
		}
		return $ret;
	}
	
	protected function handleAjax()
	{
	}
		
	/**
	 * This function parses a sitemap string into an array of bucket and block ids
	 */
	private function parse_sitemap_string($sitemap)
	{
		
	}
	
	private function adminExists()
	{
		$io = new FileIO();
		$path = Constants::GET_USERS_DIRECTORY() . '/' . 'admin.usr';
		if ($io->fileExists($path)) {
			return true; 
		} else {
			return false;
		}
	}
	
	private function contentExists()
	{
		$framework = new FrameworkController();
		$site = $framework->getSite();
		$buckets = $site->getAllBuckets();
		if(!empty($buckets))
		{
			return true;
		} else {
			return false;
		}
	}
	
	private function createUser($username, $password, $usertype, $pagePermissions)
	{
		$io = new FileIO();
		$newuser = new User($username, $usertype);
		$newuser->setPassword($password);
		if(!empty($pagePermissions)) 
		{
			foreach($pagePermissions as $page => $perm)
			{
				$newuser->addPagePermission($page, $perm);
			}
		}
		$filename = Constants::GET_USERS_DIRECTORY() . '/' . $username . '.usr';
		$serialized = serialize($newuser);
		
		return $io->writeFile($filename, $serialized);
		
	}
	
}

?>