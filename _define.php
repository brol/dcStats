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
  return null;
}
//require_once dirname(__FILE__).'/_debug.php';

/**
 * register this module for Dotclear
 */
$this->registerModule(
	/* Name */			"dcStats",
	/* Description*/	__("Dotclear 2 Statistics"),
	/* Author */		"Olivier Le Bris, Pierre Van Glabeke",
	/* Version */		"0.2.3",
	/* Properties */
	array(
		'permissions' => 'usage,contentadmin,admin',
		'type' => 'plugin',
		'dc_min' => '2.6',
		'support' => 'http://forum.dotclear.org/viewforum.php?id=16',
		'details' => 'https://github.com/brol/dcStats'
		)
);
