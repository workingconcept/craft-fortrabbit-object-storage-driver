<?php
/**
 * Imager Fortrabbit Object Storage plugin for Craft CMS 3.x
 *
 * @link      https://workingconcept.com/
 * @copyright Copyright (c) 2018 Working Concept Inc.
 */

namespace workingconcept\imagerfortrabbitobjectstoragedriver\externalstorage;

use Craft;
use craft\helpers\FileHelper;

use Aws\S3\S3Client;
use Aws\S3\Exception\S3Exception;

use aelvan\imager\models\ConfigModel;
use aelvan\imager\services\ImagerService;
use aelvan\imager\externalstorage\ImagerStorageInterface;

use League\Flysystem\AwsS3v3\AwsS3Adapter;


class FortrabbitObjectStorage implements ImagerStorageInterface
{
    public static function upload(string $file, string $uri, bool $isFinal, array $settings)
    {
        /** @var ConfigModel $settings */
        $config = ImagerService::getConfig();

        $clientConfig = [
            'version'      => 'latest',
            'region'       => $settings['region'],
            'endpoint'     => $settings['endpoint'],
            'credentials'  => [
                'key'    => $settings['accessKey'],
                'secret' => $settings['secretAccessKey'],
            ],
        ];

        try {
            $client = new S3Client($clientConfig);
        } catch (\InvalidArgumentException $e) {
            Craft::error('Invalid configuration of S3 Client: '.$e->getMessage(), __METHOD__);
            return false;
        }
        
        if (isset($settings['folder']) && $settings['folder'] !== '') {
            $uri = ltrim(FileHelper::normalizePath($settings['folder'].'/'.$uri), '/');
        }

        $opts = $settings['requestHeaders'];
        $cacheDuration = $isFinal ? $config->cacheDurationExternalStorage : $config->cacheDurationNonOptimized;

        if (!isset($opts['Cache-Control'])) {
            $opts['CacheControl'] = 'max-age='.$cacheDuration.', must-revalidate';
        }

        $opts = array_merge($opts, [
            'Bucket' => $settings['bucket'],
            'Key' => $uri,
            'Body' => fopen($file, 'r'),
            'ACL' => 'public-read',
        ]);

        try {
            $client->putObject($opts);
        } catch (S3Exception $e) {
            Craft::error('An error occured while uploading to fortrabbit Object Storage: '.$e->getMessage(), __METHOD__);

            return false;
        }

        return true;
    }

}
