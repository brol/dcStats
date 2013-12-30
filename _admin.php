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
* rights management
*/
if (!defined('DC_CONTEXT_ADMIN')) { return; }

/**
* admin menu integration
*/
$_menu['Plugins']->addItem('dcStats',
	'plugin.php?p=dcStats',
	'index.php?pf=dcStats/icon.png',
	preg_match('/plugin.php\?p='.'dcStats'.'(&.*)?$/', $_SERVER['REQUEST_URI']),
	$core->auth->check('usage,contentadmin,admin', $core->blog->id)
	);
	
require_once dirname(__FILE__).'/_widgets.php';
