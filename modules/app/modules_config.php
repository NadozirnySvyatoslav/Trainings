<?php
/**
 $pages = array( module_name => module_class)
 module_name - 1st element in REQUEST_URI http://server.org/module_name/
 module_class -  Page,NotAutorizedPage, AuthorizedPage  extended class
 */
$pages = array (
		'403' => 'error403', // display when not enough right to see page
		'404' => 'error404', // display when page not found
		'500' => 'error500', // display when page found, but class not loaded
		'noaccessyet' => 'NoAccessYet', // displays when user not approved yet by Admin
		'setlanguage' => 'SetLanguagePage',
		'signin' => 'SigninPage',
		'register_user' => 'RegisterUserPage',
		'login' => 'LoginPage',
		'logout' => 'LogoutPage',
		'trainings' => 'TrainingsPage',
		'profile' => 'ProfilePage',
		'courses' => 'CoursesPage',
		'course' => 'CoursePage',
		'enroll' => 'EnrollPage',
		'mycourses' => 'MyCoursesPage',
		'learn' => 'LearnPage',
		'exam' => 'ExamPage',
		'result' => 'ResultPage',
		'search' => 'SearchPage',
		'help' => 'HelpPage',
		'admin_trainings' => 'AdminPlansPage',
		'admin_training' => 'AdminPlanPage',
		'admin_manager' => 'AdminManagerPage',
		'admin_editor' => 'AdminEditorPage',
		'admin_users' => 'AdminUsersPage',
		'admin_user' => 'AdminUserPage',
		'admin_trainers' => 'AdminTrainersPage',
		'admin_trainer' => 'AdminTrainerPage',
		'admin_categories' => 'AdminCategoriesPage',
		'admin_courses' => 'AdminCoursesPage',
		'admin_course' => 'AdminCoursePage',
		'admin_questions' => 'AdminQuestionsPage',
		'admin_requests' => 'AdminRequestsPage' 
);
