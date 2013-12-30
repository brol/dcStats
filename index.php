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
dcPage::check('usage');

$page_title = __('dcStats');

/**
 * auto-load working class as needed
 */
global $__autoload, $core;
$GLOBALS['__autoload']['dcStatsAdmin'] = dirname(__FILE__).'/class.stats.admin.php';
$core->blog->dcStatsAdmin = new dcStatsAdmin($core);

/* get plugin operation */
$p_op = (!empty($_POST['op']))?(string)$_POST['op']:'none';
$p_tab='tab_about';

/* get message to display */
if (!empty($_GET['msg'])) $msg = (string) rawurldecode($_GET['msg']);
else if (!empty($_POST['msg'])) $msg = (string) rawurldecode($_POST['msg']);

if (!empty($_GET['m'])) {
	switch ($_GET['m']) {
		case '1':
			$m = __('Settings saved.');
			$p_tab='tab_settings';
			break;
		case '2':
			$m = __('Settings reseted.');
			$p_tab='tab_settings';
			break;
		default:
			break;
	}
	if (empty($msg)) $msg = $m;
	else $msg.=' - '.$m;
}

/* what operation to do */
switch ($p_op) {
	case 'settings': {
		try {
			$m=1;
			$plugin_defaults = (empty($_POST['plugin_defaults']))?false:true;
			$core->blog->dcStats->enabled = (boolean) (empty($_POST['plugin_enabled']))?false:true;
			$core->blog->dcStats->synchronize = (boolean) (empty($_POST['plugin_synchronize']))?false:true;			
			if ($plugin_defaults) {
				$m=2;
				$core->blog->dcStats->defaultSettings();
			}
			$core->blog->dcStats->saveSettings();			
			if (empty($msg)) {
				http::redirect('plugin.php?p=dcStats&m='.$m);
			}
		}
		catch (Exception $ex) { $this->core->error->add($ex->getMessage()); }
	}
	break;	
	
	case 'none':
	default:
		break;
}

?>
<html>
<head>
<title><?php echo $page_title; ?></title>
<link rel="stylesheet" type="text/css" href="index.php?pf=dcStats/style.css" />
  <?php echo
  dcPage::jsDatePicker().
  dcPage::jsToolBar().
  dcPage::jsModal().
  /*dcPage::jsLoad('js/_post.js').*/
  /*dcPage::jsConfirmClose('entry-form','comment-form').*/
  # --BEHAVIOR-- adminPageHeaders
  $core->callBehavior('adminPageHeaders');/*.*/
  /*dcPage::jsPageTabs($default_tab).*/
  /*$next_headlink."\n".$prev_headlink;*/
  ?>
<?php
	echo dcPage::jsPageTabs($p_tab);
?>
</head>
<body>

<?php

	echo dcPage::breadcrumb(
		array(
			html::escapeHTML($core->blog->name) => '',
			'<span class="page-title">'.$page_title.'</span>' => ''
		));
if (!empty($msg)) {
  dcPage::success($msg);
}
?>

<div class='multi-part' id='tab_settings' title='<?php echo __('Settings') ?>'>
	<div class="fieldset">
		<h4><?php echo __('Settings'); ?></h4>
			<form action="plugin.php" method="post" id="state">	
				<p class="field">
					<label for="plugin_defaults" class="classic"><?php echo __('Reset to default settings') ?></label>
						<?php echo form::checkbox('plugin_defaults', 1, (boolean) false) ?>
				</p>
				<p class="field">
					<label for="plugin_enabled" class="classic"><?php echo __('Plugin activation') ?></label>
						<?php echo form::checkbox('plugin_enabled', 1, (boolean) $core->blog->settings->dcStats->enabled) ?>
				</p>
				<p class="field">
					<label for="plugin_synchronize" class="classic"><?php echo __('Synchronize blog') ?></label>
						<?php echo form::checkbox('plugin_synchronize', 1, (boolean) $core->blog->settings->dcStats->synchronize) ?>
				</p>
				<p>
					<input type="submit" value="<?php echo __('Save') ?>" />
				<?php
					echo form::hidden(array('p'),'dcStats');
					echo form::hidden(array('op'),'settings');
					echo $core->formNonce();
				?>
				</p>
			</form>
	</div>
</div>

<?php if ($core->auth->check('usage', $core->blog->id) && $core->blog->settings->dcStats->enabled) { ?>
<div class='multi-part' id='tab_infos' title='<?php echo __('Informations') ?>'>
	<div class="fieldset">
		<h4><?php echo __('Blog'); ?></h4>
			<ul>
				<?php echo $core->blog->dcStatsAdmin->StartBlogDate(); ?>
			</ul>
	</div>
	<div class="fieldset">
		<h4><?php echo __('Last updates'); ?></h4>
			<ul>
				<?php echo $core->blog->dcStatsAdmin->LastBlogUpdate(); ?>
				<?php echo $core->blog->dcStatsAdmin->LastPostUpdate(); ?>
				<?php echo $core->blog->dcStatsAdmin->LastCommentUpdate(); ?>
				<?php echo $core->blog->dcStatsAdmin->LastMediaUpdate(); ?>
			</ul>
	</div>
	<div class="fieldset">
		<h4><?php echo __('Statistics'); ?></h4>
			<?php echo $core->blog->dcStatsAdmin->Statistics(); ?>
	</div>
	<div class="fieldset">
		<h4><?php echo __('Entries'); ?></h4>
			<ul>
				<?php echo $core->blog->dcStatsAdmin->SelectedEntries(); ?>
				<?php echo $core->blog->dcStatsAdmin->EntriesWaiting(); ?>
				<?php echo $core->blog->dcStatsAdmin->ScheduledEntries(); ?>
				<?php echo $core->blog->dcStatsAdmin->OfflineEntries(); ?>
			</ul>
	</div>
	<div class="fieldset">
		<h4><?php echo __('Top reads'); ?></h4>
			<?php echo $core->blog->dcStatsAdmin->TopReads(); ?>
	</div>
	<div class="fieldset">
		<h4><?php echo __('Top comments'); ?></h4>
			<?php echo $core->blog->dcStatsAdmin->TopCommented(); ?>
	</div>
	<div class="fieldset">
		<h4><?php echo __('Top trackbacks'); ?></h4>
			<?php echo $core->blog->dcStatsAdmin->TopTrackbacked(); ?>
	</div>
</div>
<?php } ?>

<?php dcPage::helpBlock('dcStats'); ?>

</body>
</html>