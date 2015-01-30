<?php defined( 'SYSPATH' ) or die( 'No direct script access.' );

$plugin = Plugin::factory('skeleton_widget_registration', array(
	'title' => 'Skeleton Registration Widget',
	'version' => '1.0.0',
	'description' => 'Заготовка виджета регистрации пользователя',
	'author' => 'KodiCMS',
))->register();