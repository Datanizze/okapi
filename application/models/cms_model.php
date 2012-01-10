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

	public function get_canurl($can_id = null) {
		$query = "SELECT * FROM {$this->prefix}canonical_urls";

		if ($article_id != NULL) 
			if (is_numeric(trim($article_id))) // get article by id
				$query .= " WHERE `id`='{$can_id}' LIMIT 1";

	}

	public function get_main_menu($menu_item_id = null) {
		$query = "SELECT * FROM {$this->prefix}main_menu";

		if ($article_id != NULL)
			if (is_numeric(trim($article_id))) // get article by id
				$query .= " WHERE `id`='{$menu_item_id}' LIMIT 1";

	}

	public function get_article($article_id = null) {
		return $this->get('articles', $article_id);
	}

	public function get($table, $key=null, $get_deactivated = true) {
		$query = "SELECT * FROM {$this->prefix}{$table}";
		$deactivated = $get_deactivated ? '' : (isset($key) ? " AND `active`='1'" : " WHERE `active`='1'") ;

		if ($key != NULL) {
			if (is_numeric(trim($key))) { // get article by id
				$query .= " WHERE `id`='{$key}' {$deactivated} LIMIT 1";
			} else { // get article by key
				$key = $this->db->escape($key);
				$query .= " WHERE `key`='{$key}' {$deactivated} LIMIT 1";
			}
		}

		$res = $this->db->query($query);
		$rows = $this->get_array($res);
		return $rows;
	}

	public function save($table, $data) {
		$retval = null;
		// escape user data
		$data = $this->db->escape($data);

		// check if key is set, if not use urlencoded title...
		$data['key'] = !empty($data['key']) ? $data['key'] : str_replace(' ' , '-', (strtolower($data['title'])));
		// first check if it's a new item or editing existing one.
		$res = $this->db->query("SELECT * FROM `{$this->prefix}{$table}` WHERE `key`='{$data['key']}' LIMIT 1");
		if (is_object($res)) {
			$query = '';
			// let's fix $data['active'] since it is not 0 or 1 but instead on or not set at all! 
			$data['active'] = isset($data['active']) ? (is_numeric($data['active']) ? $data['active'] : 1) : 0;

			if($res->num_rows == 1) { // item exists, therefore we should do an update, not insert
				$query = "UPDATE `{$this->prefix}{$table}` SET ";

				// lets build the col_name=expr...
				foreach ($data as $key => $val) {
					$query .= "`{$key}`='{$val}', ";
				}
				// remove trailing ', '
				$query = substr($query, 0, -2) . ' ';

				// add WHERE
				$query .= "WHERE `key`='{$data['key']}'";

				// run query
				$res = $this->db->query($query);
				if ($res === true) { // TODO: think about how mysql works... insert on dupl_key could be update... oh well, let' do it manually for now.
					@$retval = array('status' => 'success', 'message' => "<strong><a href=\"/page/{$data['key']}\">{$data['title']}</a></strong> was successfully saved!");
				} else {
					$retval = array('status' => 'error', 'message' => "Could not save <strong>{$data['key']}</strong>: {$res}");
				}
			} elseif ($res->num_rows == 0) { // new item
				$query =  "INSERT INTO `{$this->prefix}{$table}` ";
				// Let's build the column,values
				$cols = '(';
				$vals = '(';
				foreach($data as $key => $val) {
					$cols .= "`$key`, ";
					$vals .= "'$val', ";
				}
				// trim leading ', ' and add finishing ')'
				$cols = substr($cols, 0, -2) . ')';
				$vals = substr($vals, 0, -2) . ')';

				// add cols & vals to query and finish it.
				$query .= $cols . ' VALUES ' . $vals;

				// let's run the query!
				$res = $this->db->query($query);
				if ($res === true) { // TODO: think about how mysql works... insert on dupl_key could be update... oh well, let' do it manually for now.
					@$retval = array('status' => 'success', 'message' => "<em>New</em> <strong><a href=\"/page/{$data['key']}\">{$data['title']}</a></strong> was successfully added!");
				} else {
					$retval = array('status' => 'error', 'message' => "Could not add <strong>{$data['key']}</strong>: <br> {$res}");
				}
			}
		} else {
			$retval = array('status' => 'error', 'message' => "Save failed:<br> {$res}");
		}
		return $retval;
	}

	public function delete($table, $key) {
		$retval = null;
		$key = $this->db->escape($key); // escape key
		// build query
		$query = "DELETE FROM `{$this->prefix}{$table}` WHERE `key`='{$key}'";

		// run query
		$res = $this->db->query($query);

		if($res === true) {
			// delete success
			$retval = array('status' => 'success', 'message' => "<strong>{$key}</strong> was successfully deleted!");
		} else {
			$retval = array('status' => 'error', 'message' => "Could not delete <strong>{$key}</strong>: <br> {$res}");
		}

		return $retval;
	}

	public function deactivate($table, $key) {
		$this->activate($table, $key, true);
	}

	public function activate($table, $key, $deactivate = false) {
		$data = array();
		$data['key'] = $key;
		if ($deactivate)
			$data['active'] = 0;
		else 
			$data['active'] = 1;

		return $this->save($table, $data);
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

		$tables_dml['canonical_urls'] = "INSERT INTO `{$this->prefix}canonical_urls` (`canurl`, `realurl`) VALUES 
			('adm', 'cms/admin'),
			('p', 'cms/page'),
			('v', 'cms/page'),
			('view', 'cms/page');";
		$tables_dml['articles'] = "INSERT INTO `{$this->prefix}articles` (`key`, `title`, `content`, `author`) VALUES 
			('bacon_ipsum', 'Bacon Ipsum', '<p>Laboris short ribs aliqua non sed ad.  Pig spare ribs proident chicken non nulla, officia jowl short loin pork loin sed commodo flank pariatur nostrud.  Meatloaf shankle sint cow.  Ham ribeye commodo in cow ut.  Proident venison shank tongue andouille ea.  Proident et bresaola irure non.  Ex beef short ribs incididunt brisket nostrud.</p><p>Drumstick pork chop in, dolore capicola tenderloin tail shankle esse.  Esse irure ham fatback mollit.  Irure pork chop bresaola enim dolore do.  Sed sunt in, eu salami elit kielbasa short loin et pork shank ham ad cillum t-bone.  Tongue cupidatat enim excepteur esse sirloin.  Voluptate dolore bacon, elit dolor shoulder fugiat sint pork pork loin drumstick ea commodo prosciutto.  Capicola pork chop consectetur, beef tenderloin mollit deserunt qui.</p><p>Labore short ribs occaecat tongue t-bone qui.  Consequat culpa laboris pastrami consectetur.  Ex capicola excepteur, beef ribs short loin corned beef eu tri-tip ea chicken reprehenderit short ribs laboris ut jowl.  Nulla kielbasa non, aliquip pig filet mignon beef pork belly excepteur in minim reprehenderit elit.  Spare ribs short loin t-bone, consectetur id filet mignon nisi sed.  Pig eu hamburger dolore ham, shoulder bresaola anim ad.  Pastrami tail deserunt, mollit proident ut pork loin non tenderloin jowl ea enim meatloaf.</p><p>Flank prosciutto consectetur, turkey bresaola cow short loin ex eu culpa hamburger ullamco.  Dolore consectetur reprehenderit deserunt, cillum mollit tri-tip.  Reprehenderit anim voluptate, filet mignon nulla bresaola excepteur esse prosciutto magna biltong quis.  Eu officia pork chop, pig laboris aliquip sunt.  Andouille t-bone qui, excepteur cow swine officia eu do.  Nulla strip steak capicola short loin, tempor quis et non biltong.  Officia fugiat nulla enim ex pork velit laboris eu.</p><p>Boudin deserunt biltong, enim laboris mollit spare ribs rump dolor chuck quis pork belly tongue.  Cillum dolore sirloin turducken.  Ground round do duis magna eu.  Leberkase consequat pancetta, fugiat jerky incididunt pariatur ea sirloin laborum culpa rump dolore velit.  Biltong pig beef magna, beef ribs shankle ex in dolore sunt fugiat tongue flank.  Ut dolore occaecat tenderloin, pork chop qui bacon ut ut eiusmod turkey.  Anim hamburger shank, nostrud jerky beef ribs chicken.</p>', '1');";
		$tables_dml['main_menu'] = "INSERT INTO `{$this->prefix}main_menu` ( `text`, `url`, `title`, `weight`) VALUES 
			('Home', 'cms', 'Go home!', '100'),
			('Admin', 'cms/admin', 'Administrate this site!', '1');";
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
		$ret = null;
		if (is_object($sql_result) && $sql_result->num_rows > 0) {
			$ret = array();
			while ($row = $sql_result->fetch_assoc()) {
				array_push($ret, $row);
			}
		} 
		if($free_result_when_done)
			$this->db->free_result();

		return $ret;
	}

}
