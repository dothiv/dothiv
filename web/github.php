<?php

define('REPONAME', 'dothiv');

$fp = fopen(__DIR__ . '/../app/logs/github.log', 'a+');

$log = function ($msg) use ($fp) {
    $now = new DateTime();
    fputs($fp, sprintf("[%s] %s", $now->format('Y-m-d H:i:s'), $msg . PHP_EOL));
};

if (!isset($_POST['payload'])) {
    $log('No payload provided.');
    return;
}
$json = $_POST['payload'];
if (!$payload = json_decode($json)) {
    $log('Could not parse json: ' . $json);
    return;
}

// Repo?
$repo          = $payload->repository->name;
if ($repo != REPONAME) {
  $log(sprintf('Repo is not "%s" but "%s".', REPONAME, $repo));
  return;
}

$wd = __DIR__ . '/..';

// Current branch
$ref = exec(sprintf('cd %s; git symbolic-ref -q HEAD', escapeshellarg($wd)));
$parts  = explode('/', $ref);
$currentBranch = array_pop($parts);

// Which branch?
$ref    = $payload->ref;
$parts  = explode('/', $ref);
$updatedBranch = array_pop($parts);

if ($currentBranch != $updatedBranch) {
  $log(sprintf('Branch is not "%s" but "%s".', $currentBranch, $updatedBranch));
  return;
}

// Set update flag
$updateFlag = sprintf('%s/app/cache/update', $wd);
file_put_contents($updateFlag, $json);

$log(sprintf('Updateflag written to: %s', $updateFlag));
