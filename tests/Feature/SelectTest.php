<?php

declare(strict_types=1);

beforeEach(function () {
    // Set up database connection for testing
    $this->db = $this->getQueryBuilder();

    // Create the qb_user table for testing
    $this->db->create('qb_user')
        ->column('id')->bigInt()->unsigned()->autoIncrement()->primary()
        ->column('name')->string(100)->required()
        ->column('email')->string(150)->required()
        ->column('age')->int()
        ->column('country')->string(50)
        ->execute();

    // Create a posts table for join testing
    $this->db->create('qb_posts')
        ->column('id')->bigInt()->unsigned()->autoIncrement()->primary()
        ->column('user_id')->bigInt()->unsigned()
        ->column('title')->string(200)->required()
        ->column('content')->text()
        ->column('status')->string(20)->default('draft')
        ->execute();

    // Insert sample data for testing
    $this->db->insert('qb_user', [
        [
            'name' => 'John Doe',
            'email' => 'john@example.com',
            'age' => 30,
            'country' => 'USA'
        ],
        [
            'name' => 'Jane Smith',
            'email' => 'jane@example.com',
            'age' => 25,
            'country' => 'Canada'
        ],
        [
            'name' => 'Bob Johnson',
            'email' => 'bob@example.com',
            'age' => 35,
            'country' => 'UK'
        ],
        [
            'name' => 'Alice Brown',
            'email' => 'alice@example.com',
            'age' => 28,
            'country' => 'USA'
        ]
    ]);

    $this->db->insert('qb_posts', [
        [
            'user_id' => 1,
            'title' => 'First Post',
            'content' => 'This is the first post content',
            'status' => 'published'
        ],
        [
            'user_id' => 1,
            'title' => 'Second Post',
            'content' => 'This is the second post content',
            'status' => 'draft'
        ],
        [
            'user_id' => 2,
            'title' => 'Jane\'s Post',
            'content' => 'This is Jane\'s post content',
            'status' => 'published'
        ]
    ]);
});

afterEach(function () {
    // Clean up test data after each test
    try {
        $this->db->drop('qb_posts');
        $this->db->drop('qb_user');
    } catch (Exception $e) {
        // Handle cleanup errors gracefully
    }
});

test('select method chains correctly', function () {
    $select = $this->db->select('*')
        ->from('qb_posts');

    $this->assertInstanceOf(\CodesVault\Howdyqb\Statement\Select::class, $select);
});

test('can select all columns from table', function () {
    $results = $this->db->select('*')
        ->from('qb_user')
        ->get();

    $this->assertGreaterThan(0, count($results));
    $this->assertArrayHasKey('name', $results[0]);
    $this->assertArrayHasKey('email', $results[0]);
    $this->assertArrayHasKey('age', $results[0]);
    $this->assertArrayHasKey('country', $results[0]);
});

test('can select specific columns from table', function () {
    $results = $this->db->select('name', 'email')
        ->from('qb_user')
        ->get();

    $this->assertGreaterThan(0, count($results));
    $this->assertArrayHasKey('name', $results[0]);
    $this->assertArrayHasKey('email', $results[0]);
    $this->assertArrayNotHasKey('age', $results[0]);
});

test('can use distinct clause', function () {
    $results = $this->db->select('country')
        ->distinct()
        ->from('qb_user')
        ->get();

    $countries = array_column($results, 'country');
    $uniqueCountries = array_unique($countries);

    $this->assertEquals(count($countries), count($uniqueCountries));
});

test('can use where clause with equals operator', function () {
    $results = $this->db->select('*')
        ->from('qb_user')
        ->where('name', '=', 'John Doe')
        ->get();

    $this->assertEquals(1, count($results));
    $this->assertEquals('John Doe', $results[0]['name']);
    $this->assertEquals('john@example.com', $results[0]['email']);
});

test('can use where clause with comparison operators', function () {
    $results = $this->db->select('*')
        ->from('qb_user')
        ->where('age', '>', 30)
        ->get();

    $this->assertGreaterThan(0, count($results));
    foreach ($results as $result) {
        $this->assertGreaterThan(30, (int)$result['age']);
    }
});

test('can chain multiple andWhere clauses', function () {
    $results = $this->db->select('*')
        ->from('qb_user')
        ->where('country', '=', 'USA')
        ->andWhere('age', '>', 25)
        ->andWhere('age', '<', 35)
        ->get();

    $this->assertGreaterThan(0, count($results));
    foreach ($results as $result) {
        $this->assertEquals('USA', $result['country']);
        $this->assertGreaterThan(25, (int)$result['age']);
        $this->assertLessThan(35, (int)$result['age']);
    }
});

test('can use orWhere clause', function () {
    $results = $this->db->select('*')
        ->from('qb_user')
        ->where('country', '=', 'USA')
        ->orWhere('country', '=', 'Canada')
        ->get();

    $this->assertGreaterThan(0, count($results));
    foreach ($results as $result) {
        $this->assertContains($result['country'], ['USA', 'Canada']);
    }
});

test('can use whereNot clause', function () {
    $results = $this->db->select('*')
        ->from('qb_user')
        ->whereNot('country', '=', 'USA')
        ->get();

    $this->assertGreaterThan(0, count($results));
    foreach ($results as $result) {
        $this->assertNotEquals('USA', $result['country']);
    }
});

test('can use andNot clause', function () {
    $results = $this->db->select('*')
        ->from('qb_user')
        ->where('age', '>', 20)
        ->andNot('country', '=', 'USA')
        ->get();

    $this->assertGreaterThan(0, count($results));
    foreach ($results as $result) {
        $this->assertGreaterThan(20, (int)$result['age']);
        $this->assertNotEquals('USA', $result['country']);
    }
});

test('can use whereIn clause', function () {
    $results = $this->db->select('*')
        ->from('qb_user')
		->whereIn('country', 'USA', 'UK')
        ->get();

    $this->assertGreaterThan(0, count($results));
    foreach ($results as $result) {
        $this->assertContains($result['country'], ['USA', 'UK']);
    }
});

test('can use orderBy clause ascending', function () {
    $results = $this->db->select('*')
        ->from('qb_user')
        ->orderBy('age', 'ASC')
        ->get();

    $this->assertGreaterThan(0, count($results));

    $ages = array_column($results, 'age');
    $sortedAges = $ages;
    sort($sortedAges);

    $this->assertEquals($sortedAges, $ages);
});

test('can use orderBy clause descending', function () {
    $results = $this->db->select('*')
        ->from('qb_user')
        ->orderBy('age', 'DESC')
        ->get();

    $this->assertGreaterThan(0, count($results));

    $ages = array_column($results, 'age');
    $sortedAges = $ages;
    rsort($sortedAges);

    $this->assertEquals($sortedAges, $ages);
});

test('can use orderBy with multiple columns', function () {
    $results = $this->db->select('*')
        ->from('qb_user')
        ->orderBy(['country', 'age'], 'ASC')
        ->get();

    $this->assertGreaterThan(0, count($results));
    // Basic verification that results are returned
    $this->assertIsArray($results);
});

test('can use groupBy clause', function () {
    $results = $this->db->select('country')
        ->from('qb_user')
        ->groupBy('country')
        ->get();

    $countries = array_column($results, 'country');
    $uniqueCountries = array_unique($countries);

    $this->assertEquals(count($countries), count($uniqueCountries));
});

test('can use limit clause', function () {
    $results = $this->db->select('*')
        ->from('qb_user')
        ->limit(2)
        ->get();

    $this->assertLessThanOrEqual(2, count($results));
});

test('can use offset clause', function () {
    $allResults = $this->db->select('*')
        ->from('qb_user')
        ->orderBy('id', 'ASC')
        ->get();

    $offsetResults = $this->db->select('*')
        ->from('qb_user')
        ->orderBy('id', 'ASC')
		->limit(5)
        ->offset(2)
        ->get();

    $this->assertEquals(count($allResults) - 2, count($offsetResults));
});

test('can use limit and offset together', function () {
    $results = $this->db->select('*')
        ->from('qb_user')
        ->orderBy('id', 'ASC')
        ->limit(2)
        ->offset(1)
        ->get();

    $this->assertLessThanOrEqual(2, count($results));
});

test('can use count function', function () {
    $results = $this->db->select()
		->count('*', 'total')
		->from('qb_user')
		->groupBy('id')
		->get();

    $this->assertEquals(4, count($results));

	$result = $this->db->select()
		->count('*', 'total')
		->from('qb_user')
		->get();

    $this->assertArrayHasKey('total', $result[0]);
    $this->assertIsNumeric($result[0]['total']);
    $this->assertEquals(4, (int)$result[0]['total']);
});

test('can use table alias', function () {
    $results = $this->db->select('user.name', 'user.email')
        ->from('qb_user')
        ->alias('user')
        ->get();

    $this->assertGreaterThan(0, count($results));
    $this->assertArrayHasKey('name', $results[0]);
    $this->assertArrayHasKey('email', $results[0]);
});

test('can perform inner join', function () {
    $results = $this->db->select('user.name', 'post.title')
        ->from('qb_user user')
        ->innerJoin('qb_posts post', 'user.id', 'post.user_id')
        ->get();

    $this->assertGreaterThan(0, count($results));
    foreach ($results as $result) {
        $this->assertArrayHasKey('name', $result);
        $this->assertArrayHasKey('title', $result);
    }
});

test('can perform left join', function () {
    $results = $this->db->select('user.name', 'post.title')
        ->from('qb_user user')
        ->leftJoin('qb_posts post', 'user.id', 'post.user_id')
        ->get();

    $this->assertGreaterThan(0, count($results));
    // Should include users even if they don't have posts
    $this->assertGreaterThanOrEqual(4, count($results));
});

test('can perform right join', function () {
    $results = $this->db->select('user.name', 'post.title')
        ->from('qb_user')
        ->alias('user')
        ->rightJoin('qb_posts post', 'user.id', 'post.user_id')
        ->get();

    $this->assertGreaterThan(0, count($results));
    foreach ($results as $result) {
        $this->assertArrayHasKey('title', $result);
    }
});

test('can use raw SQL', function () {
    $results = $this->db->select('*')
        ->from('qb_user')
        ->raw("WHERE age > 25")
        ->get();

    $this->assertGreaterThan(0, count($results));
    foreach ($results as $result) {
        $this->assertGreaterThan(25, (int)$result['age']);
    }
});

test('can combine multiple query methods', function () {
    $results = $this->db->select('user.name', 'user.country', 'post.title')
        ->from('qb_user user')
        ->leftJoin('qb_posts post', 'user.id', 'post.user_id')
        ->where('user.age', '>', 25)
        ->andWhere('post.status', '=', 'published')
        ->orderBy('user.name', 'ASC')
        ->limit(10)
        ->get();

    $this->assertGreaterThanOrEqual(0, count($results));
    foreach ($results as $result) {
        $this->assertArrayHasKey('name', $result);
        $this->assertArrayHasKey('country', $result);
        if ($result['title'] !== null) {
            $this->assertArrayHasKey('title', $result);
        }
    }
});

test('can get SQL query without executing', function () {
    $queryData = $this->db->select('*')
        ->from('qb_user')
        ->where('age', '>', 25)
        ->getSql();

    $this->assertIsArray($queryData);
    $this->assertArrayHasKey('query', $queryData);
    $this->assertArrayHasKey('params', $queryData);
    $this->assertIsString($queryData['query']);
    $this->assertIsArray($queryData['params']);
});

test('returns empty result for non-existent conditions', function () {
    $results = $this->db->select('*')
        ->from('qb_user')
        ->where('name', '=', 'Non Existent User')
        ->get();

    $this->assertEquals(0, count($results));
});

test('can use callable where clause', function () {
    $results = $this->db->select('*')
        ->from('qb_user')
        ->where(function($query) {
            $query->where('country', '=', 'USA')
                  ->orWhere('age', '>', 30);
        })
        ->get();

    $this->assertGreaterThan(0, count($results));
});
