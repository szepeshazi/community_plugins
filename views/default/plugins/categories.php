<?php
?>
<div class="plugins_sidebar_box">
<h2><?php echo elgg_echo('plugins:categories'); ?></h2>
<ul>
<?php
// your plugins
if (isloggedin()) {
	$params = array(
		'types' => 'object',
		'subtypes' => 'plugin_project',
		'owner_guid' => get_loggedin_userid(),
		'count' => TRUE,
	);
	$count_user_plugins = (int)elgg_get_entities($params);
?>
	<li>
		<a class="plugins_highlight" href="<?php echo $vars['url']; ?>pg/plugins/developer/<?php echo $vars['user']->username; ?>"><?php echo elgg_echo('plugins:myplugins'); ?></a>
		(<?php echo $count_user_plugins; ?>)
	</li>
<?php
}

// all plugins
$params = array(
	'types' => 'object',
	'subtypes' => 'plugin_project',
	'count' => TRUE,
);
$all_plugins_count = (int)elgg_get_entities($params);
$url = $vars['url'] . "pg/plugins/category/all";
?>
	<li>
		<a class="plugins_highlight" href="<?php echo $url; ?>"><?php echo elgg_echo('plugins:cat:all'); ?></a>
		(<?php echo $all_plugins_count; ?>)
	</li>
<?php

// categories
$params = array(
	'types' => 'object',
	'subtypes' => 'plugin_project',
	'count' => TRUE,
);
foreach ($vars['config']->plugincats as $value => $option) {
	$params['metadata_name'] = 'plugincat';
	$params['metadata_value'] = $value;
	$counter = (int)elgg_get_entities_from_metadata($params);
	echo "<li><a href=\"{$vars['url']}pg/plugins/category/{$value}\">".$option."</a> ({$counter})</li>";
}
?>
</ul>
</div>