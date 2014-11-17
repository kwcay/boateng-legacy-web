/*
 *
 * @author		Francis Amankrah <frank@frnk.ca>
 * @copyright	Copyright 2014 Francis Amankrah
 * @license		http://www.gnu.org/licenses/gpl.html GNU General Public License version 3 or later (see LICENSE.txt)
 */

/*
	Languages table
	Full list of ISO 639-3 codes: http://en.wikipedia.org/wiki/List_of_ISO_639-3_codes
 */
CREATE TABLE IF NOT EXISTS `languages` (
	`code` char(3) not null default '',
	`name` varchar(300) not null default '',
	`countries` varchar(60) not null default '',
	`params` text not null default '',
	primary key (`code`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


/*
	Definitions table
 */
CREATE TABLE IF NOT EXISTS `definitions` (
	`word` varchar(200) not null default '',
	`language` varchar(30) not null default '',
	`translation` text not null default '',
	`meaning` text not null default '',
	`state` tinyint(1) unsigned not null default 0,
	`date` date not null default '1000-01-01',
	`params` text not null default '',
	`id` char(32) not null default '',
	primary key (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

