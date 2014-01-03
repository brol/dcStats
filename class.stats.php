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
# 03-01-2014

if (!defined('DC_RC_PATH')) {return;}
/**
 * manage statistics
 */
class dcStats {

	/**
	 * class variables
	 */
	protected $core;
	protected $installed=true;
	public $enabled;
	public $synchronize;
	protected $postCount_enabled=false;

	/**
	 * computed counters (for quick re-use)
	 */
	public $c_posts=-1;
	public $c_comments=-1;
	public $c_trackbacks=-1;
	public $c_categories=-1;
	public $c_tags=-1;
	public $c_authors=-1;
	public $t_reads=null;
	public $t_top=null;
	public $t_commented=null;
	public $t_trackbacked=null;

	/**
	 * class constructor
	 */
	public function __construct($core) {
		$this->core	=& $core;
		$this->core->blog->settings->addNamespace('dcStats');
		$this->readSettings();
	}

	/**
	 * read settings
	 */
	protected function readSettings() {
		if ($this->installed) {
			$this->enabled = (boolean) $this->core->blog->settings->dcStats->enabled;
			$this->synchronize = (boolean) $this->core->blog->settings->dcStats->synchronize;
			if ($this->core->plugins->moduleExists('postCount')) {
				$this->postCount_enabled = $this->core->blog->settings->postCount->enabled;
			}
		}
	}

	/**
	 * set default settings
	 */
	public function initSettings() {
		$this->defaultSettings();
		$this->saveSettings();
	}

	/**
	 * set default settings
	 */
	public function defaultSettings() {
		$this->enabled = (boolean) false;
		$this->synchronize = (boolean) true;
	}

	/**
	 * save all settings
	 */
	public function saveSettings() {
		$this->core->blog->settings->dcStats->put('enabled',$this->enabled,'boolean',__('Enable plugin'));
		$this->core->blog->settings->dcStats->put('synchronize',$this->synchronize,'boolean',__('Synchronize blog'));
		if ($this->synchronize) {
			$this->core->blog->triggerBlog();
		}
	}

	/**
	 * proceed to post counts/comments/trackbacks
	 */
    protected function counts($status=1,$tables=null,$where=null,$limit=null) {
		if (!$this->enabled)
			return -1;
		else {
			try	{
				$params = array();
				$params['no_content'] = true;
				$params['post_status'] = $status;
				if ($limit != null) $params['limit'] = $limit;

				if (isset($tables) && !empty($tables) && $tables != null)
					$params['from'] = ", $tables";

				if (isset($where) && !empty($where) && $where != null)
					$params['sql'] = $where;

				$rs = $this->core->blog->getPosts($params, true);
				if ($rs->isEmpty())
					return (integer)0;
				else
					return (integer)$rs->f(0);
			}
			catch (Exception $ex) { $this->core->error->add($ex->getMessage()); }
		}
    }

	/**
	 * proceed to comments or trackbacks counts
	 */
    protected function comments_trackbacks($status=1,$trackbacks=false) {
		if ($trackbacks)
			$req = 'Co.comment_trackback=1';
		else
			$req = 'Co.comment_status=1';
		return $this->counts($status,$this->core->prefix.'comment Co', 'AND Co.post_id=P.post_id AND '.$req);
	}

	/**
	 * proceed to post counts
	 */
    public function posts($status=1,$force=false) {
		if ($this->c_posts != -1 && !$force)
			return $this->c_posts;
		else {
			$this->c_posts = (integer)$this->counts();
			return $this->c_posts;
		}
	}

	/**
	 * proceed to comment counts
	 */
    public function comments($status=1,$force=false) {
		if ($this->c_comments != -1 && !$force)
			return $this->c_comments;
		else {
			$this->c_comments = (integer)$this->comments_trackbacks($status);
			return $this->c_comments;
		}
	}

	/**
	 * proceed to trackbacks counts
	 */
    public function trackbacks($status=1,$force=false) {
		if ($this->c_trackbacks != -1 && !$force)
			return $this->c_trackbacks;
		else {
			$this->c_trackbacks = (integer)$this->comments_trackbacks($status,true);
			return $this->c_trackbacks;
		}
	}

	/**
	 * proceed to post read counts
	 */
    protected function reads($status=1,$force=false) {
		if (!$this->enabled)
			return -1;
		else {
			if (!$this->postCount_enabled) { // if postcount module is not installed, return 0 for all counters
				$this->t_reads = array('min'=>0, 'max'=>0, 'sum'=>0, 'avg'=>0);
				return $this->t_reads;
			}
			else {
				if ($this->t_reads != null && !$force) {
					return $this->t_reads;
				}
				else {
					try {
					if ($this->core->con->driver() == 'mysql' || $this->core->con->driver() == 'mysqli') {
            $cast_type = 'UNSIGNED';
          } else {
            $cast_type='INTEGER';
          }
						$req =
						'SELECT MIN(CAST(M.meta_id AS '.$cast_type.')) as min,MAX(CAST(M.meta_id AS '.$cast_type.')) as max,SUM(CAST(M.meta_id AS '.$cast_type.')) as sum,AVG(CAST(M.meta_id AS DECIMAL)) as avg '.
						'FROM '.$this->core->prefix.'meta M '.
						'LEFT JOIN '.$this->core->prefix.'post P ON M.post_id=P.post_id WHERE '.
						'P.post_status='.$status." AND P.post_password IS NULL AND M.meta_type = 'count|".$this->core->blog->settings->lang."' ".
						"AND P.blog_id='".$this->core->con->escape($this->core->blog->id)."' ";

						$rs = $this->core->con->select($req);
						if ($rs->isEmpty()) {
							$this->t_reads = array('min'=>0, 'max'=>0, 'sum'=>0, 'avg'=>0);
							return $this->t_reads;
						}
						else {
							$rs = $rs->toStatic();
							$this->t_reads = array('min'=>(integer)$rs->f('min'), 'max'=>(integer)$rs->f('max'), 'sum'=>(float)$rs->f('sum'), 'avg'=>(float)$rs->f('avg'));
							return $this->t_reads;
						}
					}
					catch (Exception $ex) { $this->core->error->add($ex->getMessage()); }
				}
			}
		}
    }

	/**
 	 * get post min read counter
	 * min: minimal value for read counters regarding all posts (less read post)
	 */
    public function reads_min($force=false) {
		if ($this->t_reads != null && !$force)
			return $this->t_reads['min'];
		else {
			$v = $this->reads($force);
			return (integer)$v['min'];
		}
	}

	/**
	 * get post max read counter
	 * max: maximal value for read counters regarding all posts (most read post)
	 */
    public function reads_max($force=false) {
		if ($this->t_reads != null && !$force)
			return $this->t_reads['max'];
		else {
			$v = $this->reads($force);
			return (integer)$v['max'];
		}
	}

	/**
	 * get post max read counter
	 * sum: total of all read counters (sum all reads)
	 */
    public function reads_sum($force=false) {
		if ($this->t_reads != null && !$force)
			return $this->t_reads['sum'];
		else {
			$v = $this->reads($force);
			return (integer)$v['sum'];
		}
	}

	/**
	 * get post average read counter
	 * avg: average read counter regarding all posts (average reads per post)
	 */
    public function reads_avg($force=false) {
		if ($this->t_reads != null && !$force)
			return $this->t_reads['avg'];
		else {
			$v = $this->reads($force);
			return (float)$v['avg'];
		}
	}

	/**
	 * get post top 'x' read counter
	 */
    public function reads_top($status=1,$limit=5,$force=false,$days=0) {
		if (!$this->enabled)
			return -1;
		else {
			if (!$this->postCount_enabled) { // if postcount module is not installed, return 0 for all counters
				$this->t_top = array();
				return $this->t_top;
			}
			else {
				if ($this->t_top != null && !$force)
					return $this->t_top;
				else {
					try {
					if ($this->core->con->driver() == 'mysql' || $this->core->con->driver() == 'mysqli') {
            $cast_type = 'UNSIGNED';
          } else {
            $cast_type='INTEGER';
          }
						$req =
						'SELECT P.post_id,CAST(M.meta_id AS '.$cast_type.') as count,P.post_title,P.post_url '.
						'FROM '.$this->core->prefix.'post P '.
						'LEFT JOIN '.$this->core->prefix.'meta M ON P.post_id=M.post_id '.
						'WHERE ';

						$req .= 'P.post_status='.$status." AND P.post_password IS NULL AND M.meta_type = 'count|".$this->core->blog->settings->lang."' ";
						$req .= "AND P.blog_id='".$this->core->con->escape($this->core->blog->id)."' ";

						if ($days > 0) {
							$req .= 'AND P.post_dt > (SELECT DATE_SUB(CURDATE(), INTERVAL '.(integer)$days.' DAY)) ';
						}						
						
						$req .= 'ORDER BY CAST(M.meta_id AS '.$cast_type.') DESC ';
						$req .= $this->core->con->limit($limit);

						$rs = $this->core->con->select($req);
						if ($rs->isEmpty())
							return null;
						else {
							$rs = $rs->toStatic();
							$this->t_top = $rs;
							return $this->t_top;
						}
					}
					catch (Exception $ex) { $this->core->error->add($ex->getMessage()); }
				}
			}
		}
	}

	/**
	 * get most commented/trackbacked posts
	 */
    protected function post_nb($status=1,$limit=5,$field='nb_comment',$force=false) {
		if ((($field == 'nb_comment') ? ($this->t_commented != null) : ($this->t_trackbacked != null)) && !$force)
			return ($field == 'nb_comment') ? $this->t_commented : $this->t_trackbacked;
		else {
			if (!$this->enabled)
				return -1;
			else {
				try	{
					$req =
					"SELECT P.post_id,P.post_title,P.post_url,P.$field as count ".
					'FROM '.$this->core->prefix.'post P '.
					"WHERE P.$field > 0 AND ".
					"P.blog_id='".$this->core->con->escape($this->core->blog->id)."' AND ";

					$req .=
					'P.post_status='.$status.' AND P.post_password IS NULL '.
					"ORDER BY P.$field DESC ".
					$this->core->con->limit($limit);

					$rs = $this->core->con->select($req);
					if ($rs->isEmpty())
						return null;
					else {
						$rs = $rs->toStatic();
						if ($field == 'nb_comment') {
							$this->t_commented = $rs;
							return $this->t_commented;
						}
						else {
							$this->t_trackbacked = $rs;
							return $this->t_trackbacked;
						}
					}
				}
				catch (Exception $ex) { $this->core->error->add($ex->getMessage()); }
			}
		}
    }

	/**
	 * get most commented posts
	 */
    public function commented($status=1,$limit=5,$force=false) {
		return $this->post_nb($status,$limit,'nb_comment',$force);
    }

	/**
	 * get most trackbacked posts
	 */
    public function trackbacked($status=1,$limit=5,$force=false) {
		return $this->post_nb($status,$limit,'nb_trackback',$force);
    }


	/**
	* format posts/comments/trackbacks
	*/
    public function formated_pctr($postcount,$status=1,$entries) {
		if ($this->enabled) {
			$this->posts($status,($this->c_posts == -1) ? true : false);
			$this->comments($status,($this->c_comments == -1) ? true : false);
			$this->trackbacks($status,($this->c_trackbacks == -1) ? true : false);

			try	{
				if ($this->c_categories == -1) {
					$rs = $this->core->blog->getCategories();
					$this->c_categories = (integer)count($rs->rows());
				}

				if ($this->c_tags == -1) {
					if ($this->core->plugins->moduleExists('tags')) {
						$rs = $this->core->meta->getMetadata(array(),true);
						$this->c_tags = (integer)$rs->f(0);
					}
				}

				if ($this->c_authors == -1) {
					$rs = $this->core->blog->getPostsUsers(array(),true);
					$this->c_authors = (integer)$rs->count();
				}
			}
			catch (Exception $ex) { $this->core->error->add($ex->getMessage()); }

			$total = (integer)$this->c_posts + (integer)$this->c_comments + (integer)$this->c_trackbacks;
			$string =
			'<ul>'.
				(!$entries[0] ? '' :  (($this->c_posts > 0) ? ('<li>'.html::escapeHTML($this->c_posts).' '.(($this->c_posts > 1) ? __("Posts") : __("Post")).'</li>') : (''))).
				(!$entries[1] ? '' :  (($this->c_comments > 0) ? ('<li>'.html::escapeHTML($this->c_comments).' '.(($this->c_comments > 1) ? __("Comments") : __("Comment")).'</li>') : (''))).
				(!$entries[2] ? '' :  (($this->c_trackbacks > 0) ? ('<li>'.html::escapeHTML($this->c_trackbacks).' '.(($this->c_trackbacks > 1) ? __("Trackbacks") : __("Trackback")).'</li>') : (''))).
				(!$entries[3] ? '' :  (($this->c_categories > 0) ? ('<li>'.html::escapeHTML($this->c_categories).' '.(($this->c_categories > 1) ? __("Categories") : __("Category")).'</li>') : (''))).
				(!$entries[4] ? '' :  (($this->c_tags > 0) ? ('<li>'.html::escapeHTML($this->c_tags).' '.(($this->c_tags > 1) ? __("Tags") : __("Tag")).'</li>') : (''))).
				(!$entries[5] ? '' :  (($this->c_authors > 0) ? ('<li>'.html::escapeHTML($this->c_authors).' '.(($this->c_authors > 1) ? __("Authors") : __("Author")).'</li>') : (''))).
				'';

			if ($postcount) {
				if ($this->postCount_enabled) { // if postcount module is installed, print read counters
					$this->reads($status,($this->t_reads == -1) ? true : false);
					$string .=
						(($this->t_reads['sum'] > 0) ? ('<li>'.html::escapeHTML($this->t_reads['sum']).' '.(($this->t_reads['sum'] > 1)  ? __("Total posts reading") : __("Total post reading")).'</li>') : ('')).
						(($this->t_reads['max'] > 0) ? ('<li>'.html::escapeHTML($this->t_reads['max']).' '.(($this->t_reads['max'] > 1)  ? __("Reads for most read post") : __("Read for most read post")).'</li>') : ('')).
						(($this->t_reads['avg'] > 0) ? ('<li>'.html::escapeHTML(sprintf("%3.1f",$this->t_reads['avg'])).' '.(($this->t_reads['avg'] > 1)  ? __("Average posts read") : __("Average post read")).'</li>') : (''));

					$total += (integer)$this->t_reads['sum'] + (integer)$this->t_reads['max'] + (integer)$this->t_reads['avg'];
				}
			}

			$string .=
			'</ul>';

			if ($total > 0)
				return $string;
			else
				return '<ul><li>'.__('No content').'</li></ul>';
		}
		else
			return '<ul><li>'.__('No content').'</li></ul>';
	}

	/**
	 * format posts list
	 */
    public function formated($posts=array(),$count=false,$nb_letter=null) {
		$string = "";
		$sum = 0;
		if (!empty($posts)) {
			$posts->moveStart();
			while($posts->fetch()) {
				$sum++;
				// UTF8 bug correction
				$co_title = html::clean($posts->post_title);
				if ((integer) $nb_letter !== 0) {
					if (mb_strlen($co_title, 'UTF-8') > $nb_letter) {
						$co_title = trim(html::escapeHTML(mb_substr(html::decodeEntities($co_title), 0, $nb_letter, 'UTF-8'))).'…';
					}
				}

				if (!empty($co_title)) {
					$string .=
					'<li>'.
						'<a href="'.$this->core->blog->url.$this->core->url->getBase("post").'/'.$posts->post_url.'"'.
							' title="'.__("Go to the post").'">'.html::escapeHTML($co_title);
					if ($count)
						$string .= ' ('.html::escapeHTML($posts->count).')';
					$string .=
						'</a>'.
					'</li>';
				}
			}
			// set ul & /ul
			if (substr($string,0,4) == "<li>") {
				$string = '<ul>'.$string;
				$string = str_replace('</li><h3','</li></ul><h3',$string);
			}
			$string = str_replace('</h3><li>','</h3><ul><li>',$string);
			if (substr($string,strlen($string)-5,5) == "</li>") {
				$string = $string.'</ul>';
			}
		}

		if ($sum > 0)
			return $string;
		else
			return '<ul><li>'.__('No content').'</li></ul>';
	}

	/**
	 * format post top 'x' reads
	 */
    public function formated_top($limit=5,$count=false,$nb_letter=null,$status=1,$days=0) {
		if ($this->enabled) {
			$this->reads_top($status,$limit,($this->t_top == null) ? true : false,$days);
			return $this->formated($this->t_top,$count,$nb_letter);
		}
		else
			return "";
	}

	/**
	 * format post top 'x' commented posts
	 */
    public function formated_commented($limit=5,$count=false,$nb_letter=null,$status=1,$days=0) {
		if ($this->enabled) {
			$this->commented($status,$limit,($this->t_commented == null) ? true : false);
			return $this->formated($this->t_commented,$count,$nb_letter,$days);
		}
		else
			return "";
	}

	/**
	 * format post top 'x' trackbacked posts
	 */
    public function formated_trackbacked($limit=5,$count=false,$nb_letter=null,$status=1,$days=0) {
		if ($this->enabled) {
			$this->trackbacked($status,$limit,($this->t_trackbacked == null) ? true : false);
			return $this->formated($this->t_trackbacked,$count,$nb_letter,$days);
		}
		else
			return "";
	}
}
