<?php

	if ($vars['size'] == 'large') {
		$ext = '_lrg';
	} else {
		$ext = '';
	}
	echo "<img src=\"{$CONFIG->wwwroot}mod/community_plugins/graphics/icons/openoffice{$ext}.gif\" border=\"0\" />";

?>