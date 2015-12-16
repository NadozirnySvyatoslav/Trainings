<?php
include LC_PATH . '/menu.php';

$admin_menu = array (
		MENU_PLANS => '/admin_trainings',
		MENU_REQUESTS => '/admin_requests',
		MENU_TRAINERS => '/admin_trainers',
		MENU_USERS => '/admin_users',
		MENU_REPORTS => '/admin_reports'
);

$editor_menu = array (
		MENU_COURSES => '/admin_courses',
		MENU_CATEGORIES => '/admin_categories',
		MENU_NEWS => '/admin_news'
);

$user_menu = array (
		MENU_TRAININGS => '/trainings',
		MENU_COURSES => '/courses',
		MENU_MYCOURSES => '/mycourses',
		MENU_PROFILE => '/profile' 
);
