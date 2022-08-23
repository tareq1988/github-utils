<?php

require_once __DIR__ . '/vendor/autoload.php';

use Github\Client;
use Dotenv\Dotenv;

$dotenv = Dotenv::createImmutable(__DIR__);
$dotenv->load();

$user = $argv[1] ?? '';
$repo = $argv[2] ?? '';

if (!$user || !$repo) {
    echo 'Please provide a valid user and repository name' . PHP_EOL;
    echo 'Usage: php labels.php <username/org> <repo>' . PHP_EOL;
    exit;
}

echo 'Repository: ' . $user . '/' . $repo . PHP_EOL . PHP_EOL;

$client = new Client();
$client->authenticate($_ENV['TOKEN'], null, Github\AuthMethod::ACCESS_TOKEN);

$defaults = json_decode(file_get_contents(__DIR__.'/labels/defaults.json'));
$labels = json_decode(file_get_contents(__DIR__.'/labels/labels.json'));

/**
 * Delete labels
 *
 * @param array $labels
 * @param Client $client
 * @param string $user
 * @param string $repo
 *
 * @return void
 */
function deleteLabels(array $labels, Client $client, string $user, string $repo)
{
    foreach ($labels as $label) {
        try {
            echo 'Deleting ' . $label->name . PHP_EOL;
            $client->api('issue')->labels()->deleteLabel($user, $repo, $label->name);
        } catch (\Github\Exception\RuntimeException $ex) {
            echo 'Exception on ' . $label->name . ': ' . $ex->getMessage() . PHP_EOL;
        }
    }
}

/**
 * Create labels
 *
 * @param array $labels
 * @param Client $client
 * @param string $user
 * @param string $repo
 *
 * @return void
 */
function createLabels(array $labels, Client $client, string $user, string $repo)
{
    foreach ($labels as $label) {
        try {
            echo 'Creating ' . $label->name . PHP_EOL;
            $client->api('issue')->labels()->create($user, $repo, [
                'name' => $label->name,
                'color' => $label->color,
                'description' => isset($label->description) ? $label->description : '',
            ]);
        } catch (Exception $ex) {
            echo 'Exception on ' . $label->name . ': ' . $ex->getMessage() . PHP_EOL;
        }
    }
}

// Delete default labels
deleteLabels($defaults, $client, $user, $repo);

// Create new labels
createLabels($labels, $client, $user, $repo);
