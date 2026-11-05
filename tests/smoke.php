<?php

// Each request runs the real bootstrap in a separate process and temporary site.
if (($argv[1] ?? '') === '--request') {
  $_ENV = json_decode($argv[4], true, 512, JSON_THROW_ON_ERROR);
  $_SERVER = [
    'REQUEST_METHOD' => 'GET',
    'REQUEST_URI' => $argv[3],
    'HTTP_HOST' => 'localhost',
    'SERVER_NAME' => 'localhost',
    'SERVER_PORT' => '80',
    'SERVER_PROTOCOL' => 'HTTP/1.1',
    'HTTP_ACCEPT' => 'application/json',
    'SCRIPT_NAME' => '/index.php',
  ];
  set_error_handler(function ($severity, $message, $file, $line) {
    throw new ErrorException($message, 0, $severity, $file, $line);
  });
  chdir($argv[2]);
  ob_start();
  require 'index.php';
  $body = ob_get_clean();
  echo json_encode(['status' => http_response_code() ?: 200, 'body' => $body], JSON_THROW_ON_ERROR);
  exit;
}

$root = dirname(__DIR__);
if (!is_file($root . '/vendor/autoload.php') || !in_array('sqlite', PDO::getAvailableDrivers(), true)) {
  fwrite(STDERR, "Install Composer dependencies and enable pdo_sqlite before running the smoke tests.\n");
  exit(1);
}

$site = sys_get_temp_dir() . '/slime-smoke-' . bin2hex(random_bytes(6));
mkdir($site);
mkdir($site . '/controllers');
copy($root . '/index.php', $site . '/index.php');
file_put_contents($site . '/.env', '');
symlink($root . '/vendor', $site . '/vendor');
symlink($root . '/templates', $site . '/templates');
file_put_contents($site . '/controllers/index.php', <<<'PHP'
<?php
$app->get('/status', function ($request, $response) {
  $response->getBody()->write(json_encode(['database' => $GLOBALS['database']]));
  return $response;
});
$app->get('/binding', function ($request, $response) {
  $GLOBALS['database'] = new PDO('sqlite::memory:');
  $GLOBALS['database']->exec('CREATE TABLE urls (hash TEXT)');
  $payload = "' OR 1=1 --";
  \VPHP\db::insert('urls', ['hash' => 'ordinary']);
  \VPHP\db::insert('urls', ['hash' => $payload]);
  $result = \VPHP\db::find('urls', ['hash' => $payload]);
  $response->getBody()->write(json_encode($result));
  return $response;
});
$app->get('/query-error', function ($request, $response) {
  $GLOBALS['database'] = new PDO('sqlite::memory:');
  \VPHP\db::find('private_table_name', ['id = ?', 1]);
  $response->getBody()->write('incorrect success');
  return $response;
});
PHP
);

function request($site, $path, $settings = []) {
  $settings += ['SITE_MODE' => 'production', 'SITE_TITLE' => 'Smoke test'];
  $process = proc_open(
    [PHP_BINARY, '-d', 'pdo_mysql.default_socket=' . $site . '/missing.sock', __FILE__, '--request', $site, $path, json_encode($settings, JSON_THROW_ON_ERROR)],
    [0 => ['pipe', 'r'], 1 => ['pipe', 'w'], 2 => ['pipe', 'w']],
    $pipes
  );
  fclose($pipes[0]);
  $output = stream_get_contents($pipes[1]);
  $errors = stream_get_contents($pipes[2]);
  fclose($pipes[1]);
  fclose($pipes[2]);
  $code = proc_close($process);
  if ($code !== 0) {
    throw new RuntimeException("Request process failed: $errors");
  }
  $result = json_decode($output, true, 512, JSON_THROW_ON_ERROR);
  $result['stderr'] = $errors;
  return $result;
}

function same($expected, $actual) {
  if ($expected !== $actual) {
    throw new RuntimeException('Expected ' . var_export($expected, true) . ', got ' . var_export($actual, true));
  }
}

function genericFailure($result) {
  same(500, $result['status']);
  same('', $result['stderr']);
  // A single JSON response also rules out direct PDO output before the response.
  $body = json_decode($result['body'], true, 512, JSON_THROW_ON_ERROR);
  same(true, isset($body['message']));
  foreach (['PDO', 'SQLSTATE', 'private_table_name', 'Missing database setting', 'incorrect success', '"database"'] as $detail) {
    same(false, strpos($result['body'], $detail));
  }
}

$tests = [
  'database remains optional' => function () use ($site) {
    $result = request($site, '/status');
    same(200, $result['status']);
    same(['database' => false], json_decode($result['body'], true, 512, JSON_THROW_ON_ERROR));
    same('', $result['stderr']);
  },
  'DBKit 2.0.1 criteria maps bind injection strings as values' => function () use ($site) {
    $result = request($site, '/binding');
    same(200, $result['status']);
    same(['data' => [['hash' => "' OR 1=1 --"]], 'total' => 1], json_decode($result['body'], true, 512, JSON_THROW_ON_ERROR));
    same('', $result['stderr']);
  },
  'connection setup errors reach the production error handler' => function () use ($site) {
    genericFailure(request($site, '/status', ['DB_HOST' => 'localhost']));
  },
  'query failures produce a generic production response' => function () use ($site) {
    genericFailure(request($site, '/query-error'));
  },
  'development mode retains error details' => function () use ($site) {
    $result = request($site, '/status', ['SITE_MODE' => 'development', 'DB_HOST' => 'localhost']);
    same(500, $result['status']);
    same(true, strpos($result['body'], 'Missing database setting: name') !== false);
    same(true, strpos($result['stderr'], 'Missing database setting: name') !== false);
  },
  'custom 404 handler still works' => function () use ($site) {
    $result = request($site, '/missing');
    same(404, $result['status']);
    same(true, strpos($result['body'], 'This page could not be found.') !== false);
    same('', $result['stderr']);
  },
];

if (in_array('mysql', PDO::getAvailableDrivers(), true)) {
  $tests['unavailable database produces a generic production response'] = function () use ($site) {
    // The child uses a nonexistent local socket, never an application database.
    genericFailure(request($site, '/status', [
      'DB_HOST' => 'localhost',
      'DB_NAME' => 'smoke',
      'DB_USER' => 'smoke',
      'DB_PASSWORD' => 'smoke',
    ]));
  };
}

$failures = 0;
try {
  foreach ($tests as $name => $test) {
    try {
      $test();
      echo "PASS $name\n";
    } catch (Throwable $error) {
      $failures++;
      fwrite(STDERR, "FAIL $name: " . $error->getMessage() . "\n");
    }
  }
} finally {
  foreach (['controllers/index.php', 'index.php', '.env', 'vendor', 'templates'] as $path) {
    unlink($site . '/' . $path);
  }
  rmdir($site . '/controllers');
  rmdir($site);
}
echo count($tests) . " tests, $failures failures\n";
exit($failures ? 1 : 0);
