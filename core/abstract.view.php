<?php
namespace Nkomo;
defined('_NKOMO_INC') or die;
/**
 * @author		Francis Amankrah <frank@frnk.ca>
 * @copyright	Copyright 2014 Francis Amankrah
 * @license		http://www.gnu.org/licenses/gpl.html GNU General Public License version 3 or later (see LICENSE.txt)
 */


abstract class View
{
	public $title		= 'Di Nkomo: The book of Native tongues.';
	public $desc		= 'The dictionary of Native tongues.';
	public $keywords	= 'dictionary, bilingual, multilingual, translation, twi, ewe, ga, wa, dagbani, igbo';
	public $body		= '';
	
	abstract public function load();
	
	public function __construct() {}
	
	/*
	 * Renders page in requested format
	 */
	public function render()
	{
		Log::mark('Rendering view...');
		
		// Render
		$format	= Request::get('format', 'html');
		switch ($format)
		{
			case 'html':
			default:
				return $this->_renderHTML();
		}
	}
	
	protected function _renderHTML()
	{
		// HTML renderer
		Library::import('formats:html');
		$html	= new RendererHTML();
		
		// Meta data
		$html->addTag('<title>'. $this->title .'</title>');
		$html->addTag('viewport', 'width=device-width, initial-scale=1, maximum-scale=1');
		$html->addTag('author', 'Francis Amankrah');
		$html->addTag('description', $this->desc);
		$html->addTag('keywords', $this->keywords);
		$html->addTag('<meta property="og:title" content="'. $this->title .'" />');
		$html->addTag('<meta property="og:desc" content="'. $this->desc .'" />');
		$html->addTag('<meta property="og:type" content="website" />');
		
		// Scripts
		$html->addScript('//ajax.googleapis.com/ajax/libs/jquery/2.1.1/jquery.min.js');
		$html->addScript('assets/var.app.js?'. INC_VER);
		$html->addScript('assets/var.form.js?'. INC_VER);
		$html->addScript('assets/var.remote.js?'. INC_VER);
		$html->addTag(
		'<!--[if lt IE 9]>'."\n".
			'<script src="http://html5shim.googlecode.com/svn/trunk/html5.js"></script>'."\n".
		'<![endif]-->');
		
		// Stylesheets
		$attr	= array('rel' => 'stylesheet', 'type' => 'text/css');
		$html->addLink('assets/style.sinanova.css', $attr);
		$html->addLink('http://fonts.googleapis.com/css?family=Gentium+Basic', $attr);
		$html->addLink('assets/style.app.css?'. INC_VER, $attr);
		$html->addLink('//maxcdn.bootstrapcdn.com/font-awesome/4.2.0/css/font-awesome.min.css', $attr);
		
		// Notice
		$msg	= Session::get('app.notice', '', true);
		$msg	= strlen($msg) ? '<div class="notice">'. $msg .'</div>' : '';
		
		// HTML body
		$html->body	= $this->body;
		$html->body	= str_replace('[i:token]', Session::getToken(), $html->body);
		$html->body	= str_replace('[i:msg]', $msg, $html->body);
		
		return $html->render();
	}
	
	/*
	 * Retrieves contents of a file
	 */
	protected function getContents($file, $ext = 'html')
	{
		// Lookup file and import contents
		$filename	= PATH_HTML.DS.str_replace(':', DS, $file) .'.'. $ext;
		if (file_exists( $filename ))
		{
			if ($ext == 'xml') {
				$output	= @simplexml_load_file( $filename ); 
			} else {
				$output	= @file_get_contents( $filename );
			}
		}
		
		// Raise error if file doesn't exist
		else {
			Log::mark('Missing file "'. $file .'" ('. $ext .')');
			Error::raiseError(EC_ERROR_CODE, 'Couldn\'t load content');
		}
		
		// Check for errors
		if ($output === false) {
			Log::mark('Error loading "'. $file .'" ('. $ext .')');
			Error::raiseError(EC_ERROR_CODE, 'Couldn\'t load content');
		}
		
		return $output;
	}
}

