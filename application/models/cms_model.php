<?php

class Cms_model extends Model {

	private $prefix = '';

	public function __construct() {
		parent::__construct();
		$okapi = Okapi::singleton(); // get config
		$this->prefix = $okapi->config['db']['prefix'];
		unset($okapi); // done with okapi object, unset it!
	}

	public function get_site_info() {
		if (file_exists(APPLICATION_PATH . '/config/site_config.php')) {
			include(APPLICATION_PATH . '/config/site_config.php');
			return $site_config;
		} 
	}

	public function register($username, $password, $real_name, $email) {
		return $this->auth->register($username, $password, $real_name, $email);
	}

	public function check_login() {
		return $this->auth->is_authenticated();
	}

	public function do_login() {
		$retval = false;
		if(isset($_POST['submit'])) {
			if (!empty($_POST['username']) && !empty($_POST['password']))  {
				$retval = $this->auth->authenticate($_POST['username'], $_POST['password']);

				if (!$retval) 
					$retval = '<p><span class="error"> Wrong Username and/or Password, try again.</span></p>';
			}
		}
		return $retval;
	}

	public function do_logout() {
		$this->auth->logout();
		header('location: /');
	}

	public function get_menu($auth = 0) { // $auth: 0 = get menuitems for users not logged in, 1 = opposite of 0, 2 = both 0 & 1 -1 = let this method decide with help from auth helper
		$auth = $auth<2 ? ' AND `logged_in`="' . $auth . '"' : '';
		$query = "SELECT * FROM `{$this->prefix}main_menu` WHERE `alive`=1{$auth} ORDER BY `weight` DESC";
		$res = $this->db->query($query);

		return $this->get_array($res);
	}

	public function get_article($article_id = null) {
		$query = "SELECT * FROM {$this->prefix}articles";

		if ($article_id != NULL) {
			if (is_numeric(trim($article_id))) {// get article by id
				$query .= " WHERE `id`='{$article_id}' LIMIT 1";
			} else { // get article by key
				$key = $this->db->escape($article_id);
				$query .= " WHERE `key`='{$key}' LIMIT 1";
			}
		}
		$res = $this->db->query($query);
		return $this->get_array($res);
	}

	public function do_install($authed = false) {
		$installation_exists = false;
		$tables_ddl = array();
		$tables_ddl['articles'] = "CREATE TABLE `{$this->prefix}articles` (
					`id` int(11) not null auto_increment,
					`key` varchar(255) not null,
					`type` varchar(255) default 'article',
					`title` varchar(255) default 'Title',
					`content` text,
					`content_type` varchar(255) default 'html',
					`active` int(1) default '1',
					`created` timestamp not null default CURRENT_TIMESTAMP,
					`published` timestamp not null default '0000-00-00 00:00:00',
					`modified` timestamp not null default '0000-00-00 00:00:00',
					`author` int(11),
				PRIMARY KEY (`id`),
				UNIQUE KEY (`key`)
			) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1;";
		$tables_ddl['canonical_urls'] = "CREATE TABLE `{$this->prefix}canonical_urls` (
			`id` int(11) not null auto_increment,
			`canurl` varchar(255),
					`realurl` varchar(255),
					`created` timestamp not null default CURRENT_TIMESTAMP,
					`active` int(1) default '1',
					`external` int(1) default '0',
					PRIMARY KEY (`id`)
				) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1;";
		$tables_ddl['groups'] = "CREATE TABLE `{$this->prefix}groups` (
			`id` int(11) not null auto_increment,
			`name` varchar(255),
				PRIMARY KEY (`id`)
			) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1;";
		$tables_ddl['main_menu'] = "CREATE TABLE `{$this->prefix}main_menu` (
			`id` int(11) not null auto_increment,
			`text` varchar(255),
					`url` varchar(255),
					`title` varchar(255),
					`weight` int(11) default '1',
					`alive` int(1) default '1',
					`external` int(1) default '0',
					`added` timestamp not null default CURRENT_TIMESTAMP on update CURRENT_TIMESTAMP,
					`logged_in` int(1) default '0',
					PRIMARY KEY (`id`)
				) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1;";
		$tables_ddl['users'] = "CREATE TABLE `{$this->prefix}users` (
			`id` int(11) not null auto_increment,
			`username` varchar(255),
					`password` varchar(50),
					`real_name` varchar(255),
					`email` varchar(255),
					`created` timestamp not null default CURRENT_TIMESTAMP on update CURRENT_TIMESTAMP,
					`last_login` timestamp not null default '0000-00-00 00:00:00',
					`last_ip` varchar(255),
					`salt` varchar(255),
				PRIMARY KEY (`id`),
				UNIQUE KEY (`username`)
			) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1;";
		$tables_ddl['users_groups'] = "CREATE TABLE `{$this->prefix}users_groups` (
			`id` int(11) not null auto_increment,
			`user_id` int(11),
					`group_id` int(11),
				PRIMARY KEY (`id`)
			) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1;";

		$tables_dml = array();

		$tables_dml['articles'] = "INSERT INTO `{$this->prefix}articles` (`key`, `title`, `content`, `author`) VALUES 
			('bacon_ipsum', 'Bacon Ipsum', '<p>Laboris short ribs aliqua non sed ad.  Pig spare ribs proident chicken non nulla, officia jowl short loin pork loin sed commodo flank pariatur nostrud.  Meatloaf shankle sint cow.  Ham ribeye commodo in cow ut.  Proident venison shank tongue andouille ea.  Proident et bresaola irure non.  Ex beef short ribs incididunt brisket nostrud.</p><p>Drumstick pork chop in, dolore capicola tenderloin tail shankle esse.  Esse irure ham fatback mollit.  Irure pork chop bresaola enim dolore do.  Sed sunt in, eu salami elit kielbasa short loin et pork shank ham ad cillum t-bone.  Tongue cupidatat enim excepteur esse sirloin.  Voluptate dolore bacon, elit dolor shoulder fugiat sint pork pork loin drumstick ea commodo prosciutto.  Capicola pork chop consectetur, beef tenderloin mollit deserunt qui.</p><p>Labore short ribs occaecat tongue t-bone qui.  Consequat culpa laboris pastrami consectetur.  Ex capicola excepteur, beef ribs short loin corned beef eu tri-tip ea chicken reprehenderit short ribs laboris ut jowl.  Nulla kielbasa non, aliquip pig filet mignon beef pork belly excepteur in minim reprehenderit elit.  Spare ribs short loin t-bone, consectetur id filet mignon nisi sed.  Pig eu hamburger dolore ham, shoulder bresaola anim ad.  Pastrami tail deserunt, mollit proident ut pork loin non tenderloin jowl ea enim meatloaf.</p><p>Flank prosciutto consectetur, turkey bresaola cow short loin ex eu culpa hamburger ullamco.  Dolore consectetur reprehenderit deserunt, cillum mollit tri-tip.  Reprehenderit anim voluptate, filet mignon nulla bresaola excepteur esse prosciutto magna biltong quis.  Eu officia pork chop, pig laboris aliquip sunt.  Andouille t-bone qui, excepteur cow swine officia eu do.  Nulla strip steak capicola short loin, tempor quis et non biltong.  Officia fugiat nulla enim ex pork velit laboris eu.</p><p>Boudin deserunt biltong, enim laboris mollit spare ribs rump dolor chuck quis pork belly tongue.  Cillum dolore sirloin turducken.  Ground round do duis magna eu.  Leberkase consequat pancetta, fugiat jerky incididunt pariatur ea sirloin laborum culpa rump dolore velit.  Biltong pig beef magna, beef ribs shankle ex in dolore sunt fugiat tongue flank.  Ut dolore occaecat tenderloin, pork chop qui bacon ut ut eiusmod turkey.  Anim hamburger shank, nostrud jerky beef ribs chicken.</p>', '1');";
		$tables_dml['main_menu'] = "INSERT INTO `{$this->prefix}main_menu` ( `text`, `url`, `title`, `weight`) VALUES 
			('Home', '/', 'Go home!', '100'),
			('Admin', '/admin', 'Administrate this site!', '1');";
		$tables_dml['users'] = "INSERT INTO `{$this->prefix}users` (`username`, `password`, `salt`) VALUES 
			('okapi', '500eea3748d43c294d2f614d72d4b1516834c4b7', 'c9fc47de600ab808bc9fe0151abdafcd6ecd34c1');";

		$retval = array();
		$retval['status'] = 'fail';
		// Let's check if an installation already exsists
		echo '<pre>';
		if ($this->db->connect()) {
			$retval['connect'] = 'success';
		} else {
			$retval['connect'] = 'fail';
		}

		if ($retval['connect'] = 'success') {
			// connecting went fine, let's check for tables!
			// since we have an array over table ddls, lets loop over that autmatically.. manual labor is boring!
			foreach($tables_ddl as $key => $val) {
				$retval['ddl_'.$key] = $this->db->query($val);
				if ($retval['ddl_'.$key] != 1 && !$installation_exists) { // table exists
					$installation_exists = true;
				}
			}
		} else {
			$retval['error'] = "Could not connect to MySQL server with provided settings, please check your config and make sure your settings are correct, then try again!";
		}

		$retval['ddl_status'] = $installation_exists ? 'fail' : 'success';
		if ($installation_exists) {
			$retval['error'] = 'One ore more colliding tables found, aborted';
			$retval['status'] = 'fail';
		} else {
			// lets try and populate the tables
			$local_success = false;
			foreach($tables_dml as $key => $val) {
				$retval['dml_'.$key] = $this->db->query($val);
				if ($retval['dml_'.$key] == 1)
					$local_success = true;
				else
					$local_succes = false;
			}
			$retval['dml_status'] = $local_success ? 'success' : 'fail';
		}

		if ($retval['ddl_status'] == 'success' && $retval['dml_status'] == 'success')
			$retval['status'] = 'success';
		echo '<br>';
		print_r($retval);
		echo '</pre>';
		exit;
		return $retval;
	}

	private function get_array($sql_result, $free_result_when_done = true) {
		$ret = array();
		if (is_object($sql_result) && $sql_result->num_rows > 0) {
			while ($row = $sql_result->fetch_assoc())
				array_push($ret, $row);
		} 
		if($free_result_when_done)
			$this->db->free_result();

		return $ret;
	}
}
