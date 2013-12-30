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

if (!defined('DC_RC_PATH')) { return; }

/**
 * auto-load working class as needed
 */
global $__autoload, $core;
$GLOBALS['__autoload']['dcStats'] = dirname(__FILE__).'/class.stats.php';
$core->blog->dcStats = new dcStats($core);
