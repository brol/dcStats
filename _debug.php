<?php
# -- BEGIN LICENSE BLOCK ----------------------------------
# This file is part of plugin by Olivier Le Bris for Dotclear 2.
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
# 2013-12-30

// ajouter la ligne suivante (sans les //) dans le fichier _define.php afin d'activer la console de debug :
// require_once dirname(__FILE__).'/_debug.php';

/**
 * debug tweak
 */
error_reporting(E_ALL);
ini_set('error_reporting', E_ALL);
ini_set('display_errors', true);
ini_set('display_startup_errors', true);
