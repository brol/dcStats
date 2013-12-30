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

/**
 * manage statistics for admin part
 */
class dcStatsAdmin {

	/**
	 * class variables
	 */
	protected $core;
	public $limit=5;
	public $count=10;
	public $nb_letter=50;
	public $nb_days=0;
	public $mask='<li class="%1$s">%2$s</li>';
	public $dtmask = '%Y-%m-%d %H:%M';

	/**
	 * class constructor
	 */
	public function __construct($core) {
		$this->core	=& $core;
	}

	/**
	 * get blog statistics
	 */
	public function Statistics() {
		$s = array();
		$s[] = true;
		$s[] = true;
		$s[] = true;
		$s[] = true;
		$s[] = true;
		$s[] = true;
		return $this->core->blog->dcStats->formated_pctr(true,1,$s);
	}

	/**
	 * get blog top reads
	 */
	public function TopReads() {
		$res = $this->core->blog->dcStats->formated_top($this->limit,$this->count,$this->nb_letter,1,$this->nb_days);
		if ($res == '') $res = __('No content');
		return $res;
	}

	/**
	 * get blog top commented
	 */
	public function TopCommented() {
		$res = $this->core->blog->dcStats->formated_commented($this->limit,$this->count,$this->nb_letter);
		if ($res == '') $res = __('No content');
		return $res;
	}

	/**
	 * get blog top backtracked
	 */
	public function TopTrackbacked() {
		$res = $this->core->blog->dcStats->formated_trackbacked($this->limit,$this->count,$this->nb_letter);
		if ($res == '') $res = __('No content');
		return $res;
	}

	/**
	 * get blog selected entries
	 */
	public function SelectedEntries() {
		$res = '';
		$post_selected_count = $this->core->blog->getPosts(array('post_selected' => 1),true)->f(0);
		$str_entries_selected = ($post_selected_count > 1) ? __('selected entries') : __('selected entry');
		$res .= sprintf($this->mask,'entries-number',$post_selected_count.' '.$str_entries_selected);
		return $res;
	}

	/**
	 * get blog entries waiting
	 */
	public function EntriesWaiting() {
		$res = '';
		$strReq = "SELECT count(P.post_id) FROM ".$this->core->prefix.
		"post P INNER JOIN ".$this->core->prefix.
		"user U ON U.user_id = P.user_id LEFT OUTER JOIN ".$this->core->prefix.
		"category C ON P.cat_id = C.cat_id WHERE P.blog_id = '".$this->core->blog->id.
		"' AND post_type = 'post' AND post_status = -2";
		$post_waiting_count = $this->core->con->select($strReq)->f(0);
		$str_entries_waiting = ($post_waiting_count > 1) ? __('entries waiting') : __('entry waiting');
		$res .= sprintf($this->mask,'entries-waiting-number',$post_waiting_count.' '.$str_entries_waiting);
		return $res;
	}

	/**
	 * get blog scheduled entries
	 */
	public function ScheduledEntries() {
		$res = '';
		$strReq = "SELECT count(P.post_id) FROM ".$this->core->prefix.
		"post P INNER JOIN ".$this->core->prefix.
		"user U ON U.user_id = P.user_id LEFT OUTER JOIN ".$this->core->prefix.
		"category C ON P.cat_id = C.cat_id WHERE P.blog_id = '".$this->core->blog->id.
		"' AND post_type = 'post' AND post_status = -1";
		$post_scheduled_count = $this->core->con->select($strReq)->f(0);
		$str_entries_scheduled = ($post_scheduled_count > 1) ? __('scheduled entries') : __('scheduled entry');
		$res .= sprintf($this->mask,'entries-scheduled-number',$post_scheduled_count.' '.$str_entries_scheduled);
		return $res;
	}

	/**
	 * get blog offline entries
	 */
	public function OfflineEntries() {
		$res = '';
		$strReq = "SELECT count(P.post_id) FROM ".$this->core->prefix.
		"post P INNER JOIN ".$this->core->prefix.
		"user U ON U.user_id = P.user_id LEFT OUTER JOIN ".$this->core->prefix.
		"category C ON P.cat_id = C.cat_id WHERE P.blog_id = '".$this->core->blog->id.
		"' AND post_type = 'post' AND post_status = -0";
		$post_offline_count = $this->core->con->select($strReq)->f(0);
		$str_entries_offline = ($post_offline_count > 1) ? __('offline entries') : __('offline entry');
		$res .= sprintf($this->mask,'entries-offline-number',$post_offline_count.' '.$str_entries_offline);
		return $res;
	}

	/**
	 * get start blog date
	 */
	public function StartBlogDate($title='') {
		$res = '';
		if ($title == '') $title = __('Start').': ';
		$start = $this->core->blog->getPosts(array('order' => 'post_dt ASC','limit' => '1'));
		$str_start = dt::dt2str($this->core->blog->settings->date_format,$start->post_dt);
		$str_date = $title.$str_start;//$str_date = sprintf('%s%s',$title,$str_start);
		if (!empty($str_date)) {
			$res .= sprintf($this->mask,'start-date',$str_date);
		}
		return $res;
	}

	/**
	 * get last blog update
	 */
	public function LastBlogUpdate($title='') {
		$res = '';
		if ($title == '') $title = __('Blog').': ';
		$str_update = dt::str($this->dtmask,$this->core->blog->upddt,$this->core->blog->settings->system->blog_timezone);
		$str_date = sprintf('%s%s',$title,$str_update);
		$res .= sprintf($this->mask,'lastblogupdate-date',$str_date);
		return $res;
	}

	/**
	 * get last post update
	 */
	public function LastPostUpdate($title='') {
		$res = '';
		if ($title == '') $title = __('Post').': ';
		$rs = $this->core->blog->getPosts(array('limit'=>1,'no_content'=>true,'order'=>'post_upddt DESC'));
		$title = html::escapeHTML($title).' ';
		if (!$rs->isEmpty())
		{
			$link = $rs->getURL();
			$timez = dt::str($this->dtmask,strtotime($rs->post_upddt),$this->core->blog->settings->system->blog_timezone);
			$text = $rs->post_title;
			$over = __('Go to content');
			$res .= sprintf('<li>%s<a href="%s" title="%s">%s</a> (%s)</li>',$title,$link,$over,$text,$timez);
		}
		else {
			$res .= '<li>'.$title.__('No content').'</li>';
		}
		return $res;
	}

	/**
	 * get last comment update
	 */
	public function LastCommentUpdate($title='') {
		$res = '';
		if ($title == '') $title = __('Comment').': ';
		$rs = $this->core->blog->getComments(array('limit'=>1,'no_content'=>true,'order'=>'comment_upddt DESC'));
		$title = html::escapeHTML($title);
		if (!$rs->isEmpty())
		{
			$link = $this->core->blog->url.$this->core->getPostPublicURL($rs->post_type,html::sanitizeURL($rs->post_url)).'#c'.$rs->comment_id;
			$timez = dt::str($this->dtmask,strtotime($rs->comment_upddt),$this->core->blog->settings->system->blog_timezone);
			$text = $rs->post_title;
			$over = __('Go to content');
			$res .= sprintf('<li>%s<a href="%s" title="%s">%s</a> (%s)</li>',$title,$link,$over,$text,$timez);
		}
		else {
			$res .= '<li>'.$title.__('No content').'</li>';
		}
		return $res;
	}

	/**
	 * get last media update
	 */
	public function LastMediaUpdate($title='') {
		$res = '';
		if ($title == '') $title = __('Media').': ';
		$rs = $this->core->con->select(
			'SELECT media_id FROM '.$this->core->prefix.'media '.
			"WHERE media_path='".$this->core->con->escape($this->core->blog->settings->system->public_path)."' ".
			'ORDER BY media_dt DESC '.$this->core->con->limit(1)
		);
		$title = html::escapeHTML($title).' ';
		if (!$rs->isEmpty())
		{
			$media = new dcMedia($this->core);
			$fi = $media->getFile($rs->f('media_id'));
			$link = 'media_item.php?id='.$fi->media_id.'&amp;popup=0&amp;post_id=';
			$timez = $fi->media_dtstr;
			$text = $fi->basename.' - '.$fi->type;
			$over = __('Go to content');
			$res .= sprintf('<li>%s<a href="%s" title="%s">%s</a> (%s)</li>',$title,$link,$over,$text,$timez);
			
			if ($fi->type_prefix == 'image') {
				$res .= '<br /><img src="'.$fi->media_icon.'" title="'.$text.'" alt="'.$text.'" />';
			}
		}
		else {
			$res .= '<li>'.$title.__('No content').'</li>';
		}
		return $res;
	}
}
