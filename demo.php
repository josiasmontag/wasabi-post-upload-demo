<?php
require 'vendor/autoload.php';

use Aws\S3\PostObjectV4;
use Aws\S3\S3Client;
use Dotenv\Dotenv;
use Illuminate\Http\Client\Factory;


$dotenv = Dotenv::createImmutable(__DIR__);
$dotenv->load();


$client = new S3Client([
    'version'                 => 'latest',
    'region'                  => $_ENV['S3_STORAGE_REGION'],
    'endpoint'                => $_ENV['S3_STORAGE_ENDPOINT'],
    'use_path_style_endpoint' => true,
    'credentials'             => [
        'key'    => $_ENV['S3_STORAGE_KEY'],
        'secret' => $_ENV['S3_STORAGE_SECRET'],
    ]
]);


$formInputs = [
    'acl'                   => 'private',
    'key'                   => 'test/${filename}',
    'success_action_status' => '201'
];

$options = [
    ['acl' => 'private'],
    ['bucket' => $_ENV['S3_BUCKET_FILES']],
    ['starts-with', '$key', 'test/'],
    ['success_action_status' => '201']
];


$postObject = new PostObjectV4(
    $client,
    $_ENV['S3_BUCKET_FILES'],
    $formInputs,
    $options,
    '+12 hours'
);

echo var_dump($postObject->getFormAttributes()) . "\n";
echo var_dump($postObject->getFormInputs()) . "\n";

$httpClient = new Factory();

$httpClient->asMultipart()
    ->attach('file', time(), 'upload.txt')
    ->post($postObject->getFormAttributes()['action'], $postObject->getFormInputs())
    ->throw();