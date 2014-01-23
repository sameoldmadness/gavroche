Gavroche
========

Gavroche is SugarCRM request generator for Yandex.Tank.


Bootstrap
---------

	require_once 'Request.php';
	require_once 'Gavroche.php';

	$gavroche = new Gavroche(
		'localhost',    // host
		'sugarcrm_dev'  // path
	);


Collect session keys by given user/password pairs
-------------------------------------------------

	$gavroche->grabSession(array(
		array('user' => 'user1', 'password' => 'password1'),
		array('user' => 'user2', 'password' => 'password2'),
	));

Collected keys will be used in following requests.


Create simple get request
-------------------------

	echo $gavroche->get('index.php?module=Home&action=index');

Customize the request
---------------------

	echo $gavroche->get('index.php?module=Home&action=index', array(
		'host'     => 'myhost',     // overwrite Gavroche's host
		'instance' => 'myinstance', // overwrite Gavroche's instance
		'session'  => null,         // overwrite or clear Gavroche's session_id
	));


Tag the request
---------------

[More on tags](link http://yandextank.readthedocs.org/en/latest/tutorial.html#tags).

	echo $gavroche->get('index.php?module=Home&action=index', 'my_request');

Or do both
----------

	echo $gavroche->get('index.php?module=Home&action=index', array(
		'host'     => 'myhost',     // overwrite Gavroche's host
		'instance' => 'myinstance', // overwrite Gavroche's instance
		'session'  => null,         // overwrite or clear Gavroche's session_id
	), 'my_request');

Create post request with data
-----------------------------

	echo $gavroche->post('index.php?action=Login&module=Users', array(
		'data' => array(
			'module'        => 'Users',
			'action'        => 'Authenticate',
			'return_module' => 'Users',
			'return_action' => 'Login',
			'cant_login'    => '',
			'login_module'  => '',
			'login_action'  => '',
			'login_record'  => '',
			'user_name'     => 'login',
			'user_password' => 'password',
		),
	), 'login');

Create multiple requests from Apache's access.log content
---------------------------------------------------------

	$requests = $gavroche->fromAccessLogContent('
		127.0.0.1 - - [23/Dec/2013:06:32:26 +0000] "GET /sugarcrm_dev/install/processing.gif HTTP/1.1" 304 187 "http://127.0.0.1/sugarcrm_dev/install.php" "Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/31.$
		127.0.0.1 - - [23/Dec/2013:06:32:26 +0000] "POST /sugarcrm_dev/install.php HTTP/1.1" 200 1755 "http://127.0.0.1/sugarcrm_dev/install.php" "Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/31.0.1650.63$
		127.0.0.1 - - [23/Dec/2013:06:32:27 +0000] "GET /sugarcrm_dev/include/images/sugarcrm_login.png HTTP/1.1" 304 186 "http://127.0.0.1/sugarcrm_dev/install.php" "Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko)$
		127.0.0.1 - - [23/Dec/2013:06:34:29 +0000] "GET /sugarcrm_dev/install/processing.gif HTTP/1.1" 304 187 "http://127.0.0.1/sugarcrm_dev/install.php" "Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/31.$
		127.0.0.1 - - [23/Dec/2013:06:34:29 +0000] "POST /sugarcrm_dev/install.php HTTP/1.1" 200 436 "http://127.0.0.1/sugarcrm_dev/install.php" "Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/31.0.1650.63 $
		127.0.0.1 - - [23/Dec/2013:06:34:32 +0000] "POST /sugarcrm_dev/install.php HTTP/1.1" 200 1520 "http://127.0.0.1/sugarcrm_dev/install.php" "Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/31.0.1650.63$
		127.0.0.1 - - [23/Dec/2013:06:34:42 +0000] "GET /sugarcrm_dev/install/install.css HTTP/1.1" 304 209 "http://127.0.0.1/sugarcrm_dev/install.php" "Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/31.0.1$
		127.0.0.1 - - [23/Dec/2013:06:34:43 +0000] "GET /sugarcrm_dev/include/images/sugar_md_open.png HTTP/1.1" 304 187 "http://127.0.0.1/s
	');

	foreach ($requests as $request) {
		echo $request;
	}

Or from access.log location
---------------------------

	$requests = $gavroche->fromAccessLog(
		'/var/log/apache2/access.log',
		5 // optional limit
	);

	foreach ($requests as $request) {
		echo $request;
	}

Visit predefined records
------------------------

	$account_ids = array(
		'72567b76-d073-09d5-626e-4e68753b6de9',
		'827d8872-3df5-13f9-e921-4e6875a76f67',
		'85dbba83-ed8d-9b75-bd93-4e687514ebb8',
	);

	foreach ($account_ids as $account_id) {
		echo $gavroche->get('index.php?module=Accounts&action=DetailView&record=' . $account_id, 'view_account');
	}
