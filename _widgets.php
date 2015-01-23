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
# 23-01-2015

if (!defined('DC_RC_PATH')) { return; }

/**
 * define behavior helper codes
 */
$core->addBehavior('initWidgets',array('bhv_dcStats','initWidgets'));

/**
 * dcStats behavior helper
 */
class bhv_dcStats {
	public static function initWidgets($widgets) {
		$widgets->create('dcStats',__('DcStats: statistics'),array('widgets_dcStats','Widget_dcStats'),
			null,
			__('Complete statistics'));
		$widgets->dcStats->setting('title',__('Title:'),__('Statistics'));
		$widgets->dcStats->setting('posts', __('Display posts count'), 1, 'check');
		$widgets->dcStats->setting('comments', __('Display comments count'), 1, 'check');
		$widgets->dcStats->setting('trackbacks', __('Display trackbacks count'), 1, 'check');
		$widgets->dcStats->setting('categories', __('Display categories count'), 1, 'check');
		$widgets->dcStats->setting('tags', __('Display tags count'), 1, 'check');
		$widgets->dcStats->setting('authors', __('Display authors count'), 1, 'check');
		$widgets->dcStats->setting('postcount', __('Display postCount stats'), 0, 'check');
		$widgets->dcStats->setting('homeonly',__('Display on:'),0,'combo',
			array(
				__('All pages') => 0,
				__('Home page only') => 1,
				__('Except on home page') => 2
				)
		);
		$widgets->dcStats->setting('content_only',__('Content only'),0,'check');
		$widgets->dcStats->setting('class',__('CSS class:'),'');
		$widgets->dcStats->setting('offline',__('Offline'),0,'check');

		$widgets->create('dcStatsTopReads',__('DcStats: top reads'),array('widgets_dcStats','Widget_TopReads'),
			null,
			__('List of best reads'));
		$widgets->dcStatsTopReads->setting('title',__('Title:'),__('Top reads'));
		$widgets->dcStatsTopReads->setting('count', __('Show counts'), 1, 'check');
		$widgets->dcStatsTopReads->setting('limit',__('Posts limit (empty means 5 posts):'),'5');
		$widgets->dcStatsTopReads->setting('nb_letter',__('Maximal post title length:'),'80');
		$widgets->dcStatsTopReads->setting('nb_days',__('Do not count below X days:'),'0');
		$widgets->dcStatsTopReads->setting('homeonly',__('Display on:'),0,'combo',
			array(
				__('All pages') => 0,
				__('Home page only') => 1,
				__('Except on home page') => 2
				)
		);
		$widgets->dcStatsTopReads->setting('content_only',__('Content only'),0,'check');
		$widgets->dcStatsTopReads->setting('class',__('CSS class:'),'');
		$widgets->dcStatsTopReads->setting('offline',__('Offline'),0,'check');

		$widgets->create('dcStatsTopCommented',__('DcStats: top comments'),array('widgets_dcStats','Widget_TopCommented'),
			null,
			__('Post list with the most comments'));
		$widgets->dcStatsTopCommented->setting('title',__('Title:'),__('Top comments'));
		$widgets->dcStatsTopCommented->setting('count', __('Show counts'), 1, 'check');
		$widgets->dcStatsTopCommented->setting('limit',__('Posts limit (empty means 5 posts):'),'5');
		$widgets->dcStatsTopCommented->setting('nb_letter',__('Maximal post title length:'),'80');
		$widgets->dcStatsTopCommented->setting('homeonly',__('Display on:'),0,'combo',
			array(
				__('All pages') => 0,
				__('Home page only') => 1,
				__('Except on home page') => 2
				)
		);
		$widgets->dcStatsTopCommented->setting('content_only',__('Content only'),0,'check');
		$widgets->dcStatsTopCommented->setting('class',__('CSS class:'),'');
		$widgets->dcStatsTopCommented->setting('offline',__('Offline'),0,'check');

		$widgets->create('dcStatsTopTrackbacked',__('DcStats: top trackbacks'),array('widgets_dcStats','Widget_TopTrackbacked'),
			null,
			__('List of tickets with the most trackbacks'));
		$widgets->dcStatsTopTrackbacked->setting('title',__('Title:'),__('Top trackbacks'));
		$widgets->dcStatsTopTrackbacked->setting('count', __('Show counts'), 1, 'check');
		$widgets->dcStatsTopTrackbacked->setting('limit',__('Posts limit (empty means 5 posts):'),'5');
		$widgets->dcStatsTopTrackbacked->setting('nb_letter',__('Maximal post title length:'),'80');
		$widgets->dcStatsTopTrackbacked->setting('homeonly',__('Display on:'),0,'combo',
			array(
				__('All pages') => 0,
				__('Home page only') => 1,
				__('Except on home page') => 2
				)
		);
		$widgets->dcStatsTopTrackbacked->setting('content_only',__('Content only'),0,'check');
		$widgets->dcStatsTopTrackbacked->setting('class',__('CSS class:'),'');
		$widgets->dcStatsTopTrackbacked->setting('offline',__('Offline'),0,'check');
	}
}
