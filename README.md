# WP Query Builder
<p>
Relational Database Query builder for WordPress.
WP Query Builder uses <code>PDO</code> for database queries. It has <strong>zero dependencies</strong> with third-party query builders or any other composer library.
</p>

<br/>

# Documentation
Documentation website [here](https://wp-querybuilder.pages.dev/).
<br/>
This Query Builder is also used in [Kathamo](https://kathamo.dev) Framework. Kathamo is a modern framework for WordPress plugin development.

<br/>

## Examples

### Create Table
``` php
DB::create('querybuilder')
    ->column('ID')->bigInt()->unsigned()->autoIncrement()->primary()->required()
    ->column('name')->string(255)->required()
    ->column('email')->string(255)->nullable()
    ->index(['ID'])
    ->execute();
```

<br/>

### Insert Statement
``` php
DB::insert('querybuilder', [
    [
        'name' => 'Keramot UL Islam',
        'email' => 'keramotul.@gmail.com',
    ]
]);
```

<br/>

### Update Statement

``` php
DB::update('querybuilders', [
    'name' => 'Keramot UL',
    'email' => 'keramotul.islam@gmail.com'
])
->where('ID', '=', 10)
->andWhere('name', '=', 'Abm Sourav')
->execute();
```

<br>

### Select Statement

``` php
$result =
DB::select('qb.ID', 'qb.name, qb.email')
    ->from('querybuilders')
    ->alias('qb')
    ->groupBy('name')
    ->get();


// *** where clouse
$result =
DB::select('posts.ID', 'posts.post_title')
    ->distinct()
    ->from('posts posts')
    ->where('posts.post_status', '=', 'publish')
    ->orderBy('post_title', 'DESC')
    ->limit(10)->offset(2)
    ->get();

// *** JOIN
DB::select('users.display_name name')
    ->count('posts.ID', 'posts')
    ->from('users users')
    ->join('posts posts')
    ->where('posts.post_status', '=', 'publish')
    ->andWhere('posts.post_type', '=', 'post')
    ->get();

// raw sql
DB::select('posts.post_title')
    ->from('posts posts')
    ->raw("WHERE posts.post_type = 'post'")
    ->andWhere('posts.post_status', '=', 'publish')
    ->raw("LIMIT 10")
    ->get();
```

<br>

### Delete Statement

``` php
// delete one row
DB::delete('posts')
    ->where('ID', '=', 3)
    ->execute();

// delete all records
DB::delete('posts')->execute();
```

<br>

### Drop Statement

``` php
DB::drop('posts');
DB::dropIfExists('terms');
```

<br>
<br>

### Single instence
<p>
Expressions also can be exicuted with one instence of <code>DB</code> class. By doing this database connection will be stablished only once.
</p>

``` php
$db = new DB();

$result =
$db::select('posts.ID', 'posts.post_title')
    ...

$db::create('meta')
    ...
```

<br>
<br>

### Database Connection
By default database connection will set out of the box, automaically. But you can also manually input database configurations. This way, you also can debug your database queries from terminal.

```php
$db = DB::setConnection(
	[
		"dbhost"        => 'mysql_host',
		"dbname"        => 'database_name',
		"dbuser"        => 'database_user',
		"dbpassword"    => 'database_password',
		"prefix"        => 'database_table_prefix'
	]
);
```

<br>
<br>

### Driver

The default driver is `pdo`. But if you want to use `wpdb` which uses Mysqli, you also can do that by changing the driver.
``` php
$db = new DB('wpdb');

$db::select('posts.post_title')
    ->from('posts posts')
    ->get();
```

<br>
<br>

## Dev Envirenment Setup for Contributors
Want to contribute to this package? Please follow the steps below.

<ul>
    <li>Create a local WordPress envirenment setup.</li>
    <li>Create a basic plugin.</li>
    <li>Run <code>composer init</code> into the plugin.</li>
    <li>Clone <code>git@github.com:CodesVault/howdy_qb.git</code> into plugin folder.</li>
    <li>
        Add repository for local package in plugin's <code>composer.json</code>.
        <pre>
        "repositories": [
            {
                "type": "path",
                "url": "./howdy_qb",
				"options": {
					"symlink": true
				}
            }
        ],
        </pre>
    </li>
    <li>Require this package. <code>composer require "codesvault/howdy-qb @dev"</code></li>
</ul>
