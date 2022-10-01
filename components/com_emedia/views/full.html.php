<?php 
/**
* @version		$Id: full.html.php 1788 2016-02-16 17:50:05Z sannosi $
* @package		Elxis
* @subpackage	Component eMedia
* @copyright	Copyright (c) 2006-2016 Elxis CMS (http://www.elxis.org). All rights reserved.
* @license		Elxis Public License ( http://www.elxis.org/elxis-public-license.html )
* @author		Elxis Team ( http://www.elxis.org )
* @description 	Elxis CMS is free software. Read the license for copyright notices and details
*/

defined('_ELXIS_') or die ('Direct access to this location is not allowed');


class fullMediaView extends emediaView {


	/*********************/
	/* MAGIC CONSTRUCTOR */
	/*********************/
	public function __construct() {
		parent::__construct();
	}


	/*************************************/
	/* MEDIA MANAGER FULL USER INTERFACE */
	/*************************************/
	public function fullUI() {
?>
		<div id="loading-wrap"><!-- loading wrapper / removed when loaded --></div>
		<div id="elx_emedia_filemanager">
		<form id="uploader" method="post">
			<h1></h1>
			<div id="uploadresponse"></div>
			<button id="level-up" name="level-up" type="button" value="LevelUp">&#160;</button>
			<button id="home" name="home" type="button" value="Home">&#160;</button>
			<input id="mode" name="mode" type="hidden" value="add" /> 
			<input id="currentpath" name="currentpath" type="hidden" />
			<div id="file-input-container">
				<div id="alt-fileinput">
					<input id="filepath" name="filepath" type="text" /><button id="browse" name="browse" type="button" value="Browse"></button>
				</div>
				<input type="file" id="newfile" name="newfile" />
			</div>
			<button id="upload" name="upload" type="submit" value="Upload" class="em"></button>
			<button id="newfolder" name="newfolder" type="button" value="New Folder" class="em"></button>
			<button id="grid" class="ON" type="button">&#160;</button>
			<button id="list" type="button">&#160;</button>
		</form>
		<div id="splitter">
			<div id="filetree"></div>
			<div id="fileinfo">
				<h1></h1>
			</div>
		</div>
		<div id="footer">
			<form name="search" id="search" method="get">
			<div>
				<input type="text" value="" name="q" id="q" />
				<a id="reset" href="#" class="q-reset"></a>
				<span class="q-inactive"></span>
			</div> 
			</form>
			<a href="" id="link-to-project"></a>
			<div id="folder-info"></div>
		</div>
		<ul id="itemOptions" class="contextMenu">
			<li class="select"><a href="#select"></a></li>
			<li class="download"><a href="#download"></a></li>
			<li class="rename"><a href="#rename"></a></li>
			<li class="resize"><a href="#resize"></a></li>
			<li class="move"><a href="#move"></a></li>
			<li class="replace"><a href="#replace"></a></li>
			<li class="delete separator"><a href="#delete"></a></li>
		</ul>
	</div>

<?php 
	}

}

?>