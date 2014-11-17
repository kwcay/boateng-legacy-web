<?php
/**
 * @author Francis Amankrah <frank@frnk.ca>
 * @license http://www.gnu.org/licenses/gpl.html GNU General Public License
 */
defined('_AD_INC') or die;


Library::import('formats:abstract.renderer');

class RendererHTML extends Renderer
{
	/*
	 *
	 */
	public function __construct()
	{
		parent::__construct();
		
		// Define base URL for localhost
		$this->addTag('<base href="'. URI::toString('scheme', 'host') .'/" />');
		
		// Define charset
		$this->addTag('<meta charset="utf-8">');
		
		// Canonical
		$this->addLink(URI::toString('scheme', 'host', 'path', 'query', 'fragment'), array('rel' => 'canonical'));
	}
	
	public function addTag($name, $content = null, $attr = 'name')
	{
		// If no content is provided, assume full tag HTML was provided
		if (is_null($content)) {
			$tag	= $name;
		}
		
		// Save tag and attribute name
		// "name" for regular meta tags, "property" for OpenGraph tags
		else {
			$tag	= '<meta '. $attr .'="'. $name .'" content="'. $content .'" />';
		}
		
		$this->_tags[$attr . $name]	= $tag;
	}
	
	public function addLink($href, $attr = array())
	{
		$link	= '<link';
		
		foreach ($attr as $name => $value) {
			$link	.= ' '. $name .'="'. $value .'"';
		}
		
		$link	.= ' href="'. $href .'" />';
		
		$this->addTag($link);
	}
	
	public function addScript($src) {
		$this->addTag('<script src="'. $src .'" type="text/javascript"></script>');
	}
	public function addScriptDeclaration($js) {
		$this->_js	.= $js;
	}
	public function addStyleDeclaration($css) {
		$this->_css	.= $css;
	}
	
	/*
	 * Methods for rendering
	 * 
	 */
	
	public function render()
	{
		Log::mark('Running HTML renderer...');
		
		// Render HTML
		$html	= $this->getContents('html');
		$html	= str_replace('[i:head]', $this->getHeader(), $html);
		$html	= str_replace('[i:body]', $this->body, $html);
		$html	= str_replace('[i:log]', Log::dump(), $html);
		
		// General formatting
		return parent::format($html);
	}
	
	private function getHeader()
	{
		$head	= '';
		
		// Header tags
		foreach ($this->_tags as $tag) {
			$head	.= "\t". $tag ."\n";
		}
		
		// Style declarations
		if (strlen($this->_css)) {
			$head	.= '<style>'. $this->_css .'</style>';
		}
		
		// Javascript declarations
		if (strlen($this->_js)) {
			$head	.= '<script type="text/javascript">'. $this->_js .'</script>';
		}
		
		return $head;
	}
	
	// Retrieves contents of a file
	public function getContents($file)
	{
		// Include library
		$filename	= PATH_HTML.DS.str_replace(':', DS, $file) .'.html';
		if (file_exists( $filename )) {
			$output	= @file_get_contents( $filename );
		}
		
		// Raise error if file doesn't exist
		else {
			Log::mark('Missing HTML file "'. $file .'"');
			Error::raiseError(EC_ERROR_CODE, 'Missing HTML file');
		}
		
		// Check for errors
		if ($output === false) {
			Log::mark('Error loading HTML file "'. $file .'"');
			Error::raiseError(EC_ERROR_CODE, 'Error loading HTML file');
		}
		
		return $output;
	}
}
