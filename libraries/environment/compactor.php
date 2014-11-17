<?php
defined('_NYANSA_INC') or die;

/**
 * @author Oliver Lillie (aka buggedcom) <publicmail@buggedcom.co.uk>
 *
 * @license BSD
 * @copyright Copyright (c) 2008 Oliver Lillie <http://www.buggedcom.co.uk>
 * Permission is hereby granted, free of charge, to any person obtaining a copy of this software and associated documentation
 * files (the "Software"), to deal in the Software without restriction, including without limitation the rights to use, copy,
 * modify, merge, publish, distribute, sublicense, and/or sell copies of the Software, and to permit persons to whom the Software
 * is furnished to do so, subject to the following conditions:  The above copyright notice and this permission notice shall be
 * included in all copies or substantial portions of the Software.
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR IMPLIED, INCLUDING BUT NOT LIMITED TO THE
 * WARRANTIES OF MERCHANTABILITY, FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE AUTHORS OR
 * COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE,
 * ARISING FROM, OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE SOFTWARE.
 *
 * @name Compactor
 * @version 0.5.1
 * @abstract This class can be used in speeding up delivery of webpages front the server to the client browser, by compacting
 * the whitespace. There are multiple options for compacting, including both horizontal and vertical whitespace removal and
 * css/javascript compacting also. The class can also compact the output of a php script using automatic output buffering. 
 */

// Oliver Lillie's compactor class
//***********************
class Compactor
{
	private $_options	= array(
		'line_break'						=> "\n",
		'preserved_tags'					=> array('textarea', 'pre', 'script', 'style', 'code'),
		'preserved_boundry'					=> '@@PRESERVEDTAG@@',
		'use_buffer' 						=> true,
		'buffer_echo'						=> true,
		'compact_on_shutdown'				=> true,
		'strip_comments' 					=> false,
		'keep_conditional_comments'			=> true,
		'conditional_boundries'				=> array('@@IECOND-OPEN@@', '@@IECOND-CLOSE@@'),
		'compress_horizontal'				=> true,
		'compress_vertical'					=> true,
		'compress_scripts'					=> true,
		'script_compression_callback' 		=> array('JSMin', 'minify'),
		'script_compression_callback_args' 	=> array(),
		'compress_css'						=> true
	);
	
	private $_preserved_blocks  = false;
	
	function __construct( $options = array() )
	{
		$this->setOption( $options );
		if ($this->_options['compact_on_shutdown']) {
			$this->setOption(array(
				'use_buffer' => true,
				'buffer_echo' => true
			));
		}
		if ($this->_options['use_buffer']) {
			ob_start();
		}
		if ($this->_options['compact_on_shutdown']) {
			register_shutdown_function(array(&$this, 'squeeze'));
		}
	}
	
	public function setOption($varname, $varvalue = null)
	{
		$keys	= array_keys( $this->_options );
		if (is_array( $varname )) {
			foreach ($varname as $name => $value) {
				if (in_array( $name, $keys )) {
					$this->_options[$name]	= $value;
				}
			}
		} else {
			if (in_array( $varname, $keys )) {
				$this->_options[$varname]	= $varvalue;
			}
		}
	}
	
	public function squeeze($html = null)
	{
		if ($this->_options['use_buffer']) {
			$html	= ob_get_clean();
		}
		$html	= $this->_unifyLineBreaks( $html );
		if ($this->_options['compress_scripts'] || $this->_options['compress_css']) {
			$html	= $this->_compressScriptAndStyleTags( $html );
		}
		if ($this->_options['strip_comments']) {
			$html	= $this->_stripHTMLComments( $html );
		}
		if ($this->_options['compress_horizontal']) {
			$html = $this->_compressHorizontally( $html );
		}
		if ($this->_options['compress_vertical']) {
			$html = $this->_compressVertically( $html );
		}
		$html	= $this->_reinstatePreservedBlocks( $html );
		if ($this->_options['buffer_echo']) {
			echo $html;
		}
		return $html;
	}
	
	private function _stripHTMLComments( $html )
	{
		if ($this->_options['keep_conditional_comments'])
		{
			$rep	= array( '<!--[if', '<![endif]-->' );
			$with	= $this->_options['conditional_boundries'];
			$html	= str_ireplace($rep, $with, $html);
		}
		
		// Keep CSS tags that are in <!-- -->
		$html	= preg_replace( '/(<style[^>]+?>)[\s]+?<!--([^>]+)-->/i', '$1$2', $html );
		$html	= preg_replace( '/<!--(.|\s)*?-->/', '', $html );
		
		if ($this->_options['keep_conditional_comments'])
		{
			$rep	= $this->_options['conditional_boundries'];
			$with	= array( '<!--[if', '<![endif]-->' );
			$html	= str_ireplace($rep, $with, $html);
		}
		return $html;
	}
	
	private function _extractPreservedBlocks( $html )
	{
		if ($this->_preserved_blocks !== false) {
			return $html;
		}
		$tag_string	= implode('|', $this->_options['preserved_tags']);
		$tag_match	= "!<(". $tag_string .")[^>]*>.*?</(". $tag_string .")>!is";
		preg_match_all($tag_match, $html, $preserved_area_match);
		$this->_preserved_blocks	= $preserved_area_match[0];
		$replaced	= preg_replace($tag_match, $this->_options['preserved_boundry'], $html);
		return $replaced;
	}
	
	private function _reinstatePreservedBlocks( $html )
	{
		if ($this->_preserved_blocks === false) {
			return $html;
		}
		foreach ($this->_preserved_blocks as $curr_block) {
			$html	= preg_replace("!". $this->_options['preserved_boundry'] ."!", $curr_block, $html, 1);
		}
		return $html;
	}
	
	private function _compressHorizontally( $html )
	{
		$html	= $this->_extractPreservedBlocks( $html );
		$html	= preg_replace('/((?<!\?>)'. $this->_options['line_break'] .')[\s]+/m', '\1', $html);
		$html	= preg_replace('/\t+/', '', $html);
		return $html;
	}

	private function _compressVertically( $html )
	{
		$html	= $this->_extractPreservedBlocks( $html );
		$html	= str_ireplace($this->_options['line_break'], '', $html);
		return $html;
	}
	
	private function _unifyLineBreaks( $html ) {
		return preg_replace("/\015\012|\015|\012/", $this->_options['line_break'], $html);
	}
	
	private function _compressScriptAndStyleTags( $html )
	{
		$compress_scripts		= $this->_options['compress_scripts'];
		$compress_css			= $this->_options['compress_css'];
		$use_script_callback	= ($this->_options['script_compression_callback'] != false);
		$scripts				= preg_match_all("!(<(style|script)[^>]*>(?:\\s*<\\!--)?)(.*?)((?://-->\\s*)?</(style|script)>)!is", $html, $scriptparts);
		$compressed		= array();
		$parts			= array();
		
		for ($i = 0; $i < count( $scriptparts[0] ); $i++)
		{
			$code		= trim( $scriptparts[3][$i] );
			$not_empty	= (!empty( $code ));
			$is_script	= ($compress_scripts && $scriptparts[2][$i] == 'script');
			$is_style	= ($compress_css && $scriptparts[2][$i] == 'style');
			
			if ($not_empty && ($is_script || $is_style))
			{
				if ($is_script && $use_script_callback)
				{
					$callback_args	= $this->_options['script_compression_callback_args'];
					if (!is_array($callback_args)) {
						$callback_args	= array( $callback_args );
					}
					array_unshift( $callback_args, $code );
					$minified	= call_user_func_array($this->_options['script_compression_callback'], $callback_args);
				}
				else {
					$minified	= $this->_simpleCodeCompress( $code );
				}
				
				array_push( $parts, $scriptparts[0][$i] );
				array_push( $compressed, trim( $scriptparts[1][$i] ) . $minified . trim( $scriptparts[4][$i] ) );
			}
		}
		$html	= str_ireplace($parts, $compressed, $html);
		return $html;
	}
	
	private function _simpleCodeCompress( $code )
	{
		$code	= preg_replace('/\/\*(?!-)[\x00-\xff]*?\*\//', '', $code);
		$code 	= preg_replace('/\\/\\/[^\\n\\r]*[\\n\\r]/', '', $code);
		$code	= preg_replace('/\\/\\*[^*]*\\*+([^\\/][^*]*\\*+)*\\//', '', $code);
		$code	= preg_replace('/\s+/', ' ', $code);
		$code	= preg_replace('/\s?([\{\};\=\(\)\/\+\*-])\s?/', "\\1", $code);
		return $code;
	}
}
