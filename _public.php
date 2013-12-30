<?php
# -- BEGIN LICENSE BLOCK ----------------------------------
# This file is part of dcStats, a plugin for Dotclear 2.
#
# Copyright (c) 2007-2010 Olivier Le Bris
# http://phoenix.cybride.net/
# Contributor : Pierre Van Glabeke
#
# Licensed under the Creative Commons by-nc-sa license.
# See LICENSE file or
# http://creativecommons.org/licenses/by-nc-sa/3.0/deed.fr_CA
# -- END LICENSE BLOCK ------------------------------------
#
# 2013-12-29

if (!defined('DC_RC_PATH')) {
        return;
}

require_once dirname(__FILE__).'/_widgets.php';

/**
 * define template helper codes
 */
$core->tpl->addValue('getStats', array('tpl_dcStats', 'getStats'));
$core->tpl->addValue('getStatsTopReads', array('tpl_dcStats', 'getStatsTopReads'));
$core->tpl->addValue('getStatsTopCommented', array('tpl_dcStats', 'getStatsTopCommented'));
$core->tpl->addValue('getStatsTopTrackbacked', array('tpl_dcStats', 'getStatsTopTrackbacked'));

/**
 * dcStats template helper
 */
class tpl_dcStats {

	/**
	 * template helper to get statistics
	 */
    static public function getStats() {
		echo
		'<?php
			if (isset($core->blog->dcStats) && $core->blog->settings->dcStats->enabled) {
				$stats = $core->blog->dcStats->formated_pctr();
			}
		?>';
	}

	/**
	 * template helper to get top posts reads
	 */
    static public function getStatsTopReads() {
		echo
		'<?php
			if (isset($core->blog->dcStats) && $core->blog->settings->dcStats->enabled) {
				$stats = $core->blog->dcStats->formated_top();
			}
		?>';
	}

	/**
	 * template helper to get top posts commented
	 */
    static public function getStatsTopCommented() {
		echo
		'<?php
			if (isset($core->blog->dcStats) && $core->blog->settings->dcStats->enabled) {
				$stats = $core->blog->dcStats->formated_commented();
			}
		?>';
	}

	/**
	 * template helper to get top posts trackbacked
	 */
    static public function getStatsTopTrackbacked() {
		echo
		'<?php
			if (isset($core->blog->dcStats) && $core->blog->settings->dcStats->enabled) {
				$stats = $core->blog->dcStats->formated_trackbacked();
			}
		?>';
	}
}

/**
 * dcStats widgets helper
 */
class widgets_dcStats {

	/**
	* print 'Statistics' widget
	*/
	static public function Widget_dcStats($w) {
		global $core;

		if (($w->homeonly == 1 && $core->url->type != 'default') ||
			($w->homeonly == 2 && $core->url->type == 'default'))
			return;

		if (!$core->plugins->moduleExists('dcStats') || !$core->blog->settings->dcStats->enabled)
			return;

		$title = $w->title ? html::escapeHTML($w->title) : __('Statistics');
		$postcount = ($w->postcount) ? true : false;

		$s = array();
		$s[] = ($w->posts) ? true : false;
		$s[] = ($w->comments) ? true : false;
		$s[] = ($w->trackbacks) ? true : false;
		$s[] = ($w->categories) ? true : false;
		$s[] = ($w->tags) ? true : false;
		$s[] = ($w->authors) ? true : false;

		return
		$res = ($w->content_only ? '' : '<div class="dcStats'.($w->class ? ' '.html::escapeHTML($w->class) : '').'">').
		($w->title ? '<h2>'.html::escapeHTML($w->title).'</h2>' : '').
			$core->blog->dcStats->formated_pctr($postcount,1,$s).
		($w->content_only ? '' : '</div>');
	}

	/**
	 * print 'Top x post reads' widget
	 */
	static public function Widget_TopReads($w) {
		global $core;

		if (($w->homeonly == 1 && $core->url->type != 'default') ||
			($w->homeonly == 2 && $core->url->type == 'default'))
			return;

		if (!$core->plugins->moduleExists('dcStats') || !$core->blog->settings->dcStats->enabled)
			return;

		$title = $w->title ? html::escapeHTML($w->title) : __('Top reads');
		$limit = abs((integer)$w->limit);
		$count = (boolean)$w->count;
		$nb_letter = abs((integer)$w->nb_letter);
		$nb_days = $w->nb_days ? abs((integer)$w->nb_days) : 0;

		return
		$res = ($w->content_only ? '' : '<div class="dcStatsTopRead'.($w->class ? ' '.html::escapeHTML($w->class) : '').'">').
		($w->title ? '<h2>'.html::escapeHTML($w->title).'</h2>' : '').
			$core->blog->dcStats->formated_top($limit,$count,$nb_letter,1,$nb_days).
		($w->content_only ? '' : '</div>');
	}

	/**
	 * print 'Top x post commented' widget
	 */
	static public function Widget_TopCommented($w) {
		global $core;

		if (($w->homeonly == 1 && $core->url->type != 'default') ||
			($w->homeonly == 2 && $core->url->type == 'default'))
			return;

		if (!$core->plugins->moduleExists('dcStats') || !$core->blog->settings->dcStats->enabled)
			return;

		$title = $w->title ? html::escapeHTML($w->title) : __('Top comments');
		$limit = abs((integer)$w->limit);
		$count = (boolean)$w->count;
		$nb_letter = abs((integer)$w->nb_letter);

		return
		$res = ($w->content_only ? '' : '<div class="dcStatsTopCommendted'.($w->class ? ' '.html::escapeHTML($w->class) : '').'">').
		($w->title ? '<h2>'.html::escapeHTML($w->title).'</h2>' : '').
			$core->blog->dcStats->formated_commented($limit,$count,$nb_letter).
		($w->content_only ? '' : '</div>');
	}

	/**
	 * print 'Top x post trackbacked' widget
	 */
	static public function Widget_TopTrackbacked($w) {
		global $core;

		if (($w->homeonly == 1 && $core->url->type != 'default') ||
			($w->homeonly == 2 && $core->url->type == 'default'))
			return;

		if (!$core->plugins->moduleExists('dcStats') || !$core->blog->settings->dcStats->enabled)
			return;

		$title = $w->title ? html::escapeHTML($w->title) : __('Top trackbacks');
		$limit = abs((integer)$w->limit);
		$count = (boolean)$w->count;
		$nb_letter = abs((integer)$w->nb_letter);

		return
		$res = ($w->content_only ? '' : '<div class="dcStatsTopTrackbacked'.($w->class ? ' '.html::escapeHTML($w->class) : '').'">').
		($w->title ? '<h2>'.html::escapeHTML($w->title).'</h2>' : '').
			$core->blog->dcStats->formated_trackbacked($limit,$count,$nb_letter).
		($w->content_only ? '' : '</div>');
	}
}
