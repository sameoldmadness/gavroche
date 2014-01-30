Gavroche
========

Gavroche is SugarCRM request generator for Yandex.Tank.


Bootstrap
---------

```php
require_once 'autoload.php';

$gavroche = new Gavroche\Application(
    'localhost',    // host
    'sugarcrm_dev'  // path
);
```


Collect session keys by given user/password pairs
-------------------------------------------------

```php
$gavroche->grabSession(array(
    array('user' => 'user1', 'password' => 'password1'),
    array('user' => 'user2', 'password' => 'password2'),
));
```

Collected keys will be used in following requests.


Create simple get request
-------------------------

```php
echo $gavroche->get('index.php?module=Home&action=index');
```


Customize the request
---------------------

```php
echo $gavroche->get('index.php?module=Home&action=index', array(
    'host'     => 'myhost',     // overwrite Gavroche's host
    'instance' => 'myinstance', // overwrite Gavroche's instance
    'session'  => null,         // overwrite or clear Gavroche's session_id
));
```


Tag the request
---------------

[More on tags](link http://yandextank.readthedocs.org/en/latest/tutorial.html#tags).

```php
echo $gavroche->get('index.php?module=Home&action=index', 'my_request');
```


Or do both
----------

```php
echo $gavroche->get('index.php?module=Home&action=index', array(
    'host'     => 'myhost',     // overwrite Gavroche's host
    'instance' => 'myinstance', // overwrite Gavroche's instance
    'session'  => null,         // overwrite or clear Gavroche's session_id
), 'my_request');
```


Create post request with data
-----------------------------

```php
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
```


Create multiple requests from log file
---------------------------------------------------------

```php
$gavroche->setLogReader(new LogReader(array(
    null,                     // first column
    LogReader::PARAM_REQUEST, // second column
    LogReader::PARAM_BODY,    // third column
), '|||'));                   // glue

$requests = $gavroche->fromAccessLog('/var/log/nginx/access.log');

foreach ($requests as $request) {
    echo $request;
}
```


Visit predefined records
------------------------

```php
$account_ids = array(
    '72567b76-d073-09d5-626e-4e68753b6de9',
    '827d8872-3df5-13f9-e921-4e6875a76f67',
    '85dbba83-ed8d-9b75-bd93-4e687514ebb8',
);

foreach ($account_ids as $account_id) {
    echo $gavroche->get('index.php?module=Accounts&action=DetailView&record=' . $account_id);
}
```
