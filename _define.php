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
# 20-12-2014

if (!defined('DC_RC_PATH')) {
  return null;
}

// décommentez la ligne ci-après afin d'activer la console de debug :
// require_once dirname(__FILE__).'/_debug.php';

/**
 * register this module for Dotclear
 */
$this->registerModule(
	/* Name */			"dcStats",
	/* Description*/	"Dotclear Statistics / Statistiques pour Dotclear",
	/* Author */		"Olivier Le Bris, Pierre Van Glabeke",
	/* Version */		"0.2.7",
	/* Properties */
	array(
		'permissions' => 'usage,contentadmin,admin',
		'type' => 'plugin',
		'dc_min' => '2.7',
		'support' => 'http://forum.dotclear.org/viewtopic.php?pid=326229#p326229',
		'details' => 'http://plugins.dotaddict.org/dc2/details/dcStats'
		)
);
