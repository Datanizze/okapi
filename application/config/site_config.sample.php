<?php

$site_config = array();

$site_config['title'] = 'Okapi CMS'; // for <title> tag in <head>
$site_config['header'] = '<h1>Header above main menu</h1>'; // html formatted

$site_config['copyright_holder'] = '';
$site_config['copyright_notice'] = 'All rights reserved';

$site_config['meta'] = array(); // for meta descriptions, tags, keywords and so on

// example meta entry for <meta name="keywords" content="wikipedia,encyclopedia" >
// $site_config['meta']['keywords'] = 'wikipedia,encyclopedia';

// ergo the format is $site_config['meta']['x'] = 'y'; for <meta name="x" content="y" >
$site_config['meta']['keywords'] = 'CMS, content, management, framework, MVC, model, view, controller, github, okapi, datanizze';
$site_config['meta']['description'] = 'CMS built on Okapi MVC Framework';

$site_config['css'] = array(); // just add css src:es to this array e.q $site_config['css'][] = 'path/to/some/style.css';
//$site_config['css'][] = '';

$site_config['js'] = array(); // just add js src:es to this array e.q $site_config['js'][] = 'path/to/some/script.js';
//$site_config['js'][] = '';
