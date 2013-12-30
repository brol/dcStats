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
if (!defined('DC_CONTEXT_ADMIN')) exit;

/**
 * get previous and actual module version
 */
$v_new = $core->plugins->moduleInfo('dcStats', 'version');
$v_old = $core->getVersion('dcStats');

try {
	if (version_compare($v_old, $v_new, '>=')) {
		/**
		 * module is up to date
		 */
		return;
	} else {

		/**
		 * module is to be installed or updates
		 */
	
		// setup default settings and new plugin version
		$core->blog->dcStats->initSettings();
		$core->setVersion('dcStats', $v_new);
			
		unset($v_new, $v_old);
		return true;		
	}
} catch (Exception $e) {
	$core->error->add(__('Unable to install or update the plugin dcStats'));
	$core->error->add($e->getMessage());
	return false;
}
