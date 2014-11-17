<?php
/**
 * @author Francis Amankrah <frank@frnk.ca>
 * @license http://www.gnu.org/licenses/gpl.html GNU General Public License
 */
defined('_AD_INC') or die;


class ViewStatic extends View
{
	public $title	= 'Di Nkomo: the book of Native tongues.';
	
	public function load()
	{
		// Get page HTML
		$page	= Request::get('page');
		$html	= $this->getContents('header');
		
		switch ($page)
		{
			case 'stats':
				$this->title	= 'Di Nkomo: in numbers.';
				$html	.= $this->getStatsContent($html);
				break;
			
			default:
				$html	.= $this->getContents('static-pages:'. $page);
		}
		
		// Footer
		$html	.= $this->getContents('footer');
		
		$this->body	= $html;
	}
	
	private function getStatsContent($html)
	{
		$stats		= $this->model->getTempStats();
		$content	= $this->getContents('static-pages:stats');
		
		$content	= str_replace('[stats:def]', $stats['def'], $content);
		$content	= str_replace('[stats:lang]', $stats['lang'], $content);
		
		return $content;
	}
}

