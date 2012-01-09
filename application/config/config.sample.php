<?php

$config = array();

// the sites base url
$config['base_url'] = 'http://example.com/okapi/';

// sets the default controller
$config['default_controller'] = 'welcome';

// Theme to use... a.k.a the folder name in themes folder for the theme stuffs...
$config['theme'] = 'default';

// database settings
$config['db']['prefix'] = 'okapi_';
$config['db']['host'] = 'localhost';
$config['db']['user'] = 'user';
$config['db']['password'] = 'password';
$config['db']['database'] = 'okapi';

// controller subfolders
// this is used to make okapi able to handle controllers in subfolders in the controllers folder... f.ex. if you have a complex app amongst other controllers you could add a folder complex_app in the applications/controllers folder, add all your controllers related to that complex app in that folder and then all you have to do is att the folder name (case sensitive) in this array... this list is comma separated. This also makes it easy to enable and disable functionality.
// example config for two subfolders complex_app, todo and cms
// $config['controller_subfolders'] ='complex_app,todo,cms';
$config['controller_subfolders'] ='';
