# Imager Storage Driver for fortrabbit Object Storage

This is an external storage driver for Imager that uploads your Imager transforms to fortrabbit's object storage. Object Storage is an AWS S3 compatible storage type, so the plugin utilizes the same S3 client as Imager's AWS storage does.

This plugin also serves as a good reference point if you want to create your own external storage driver for Imager to integrate with an unsupported third-party object storage. It's really simple, and you can do it either from a Craft 3 plugin, if you want to share it with the rest of the community (please do!), or a module, if you're using something proprietary/custom.

## Requirements

This plugin requires Craft CMS 3.0.0 or later, and Imager 2.0 or later. 

## Installation

To install the plugin, follow these instructions.

1. Open your terminal and go to your Craft project:

        cd /path/to/project

2. Then tell Composer to load the plugin:

        composer require workingconcept/imager-fortrabbit-object-storage-driver

3. In the Control Panel, go to Settings → Plugins and click the “Install” button for "Imager Storage Driver for fortrabbit Object Storage".


## Configuration

Configure the storage driver by adding new key named `fortrabbit` to the `storagesConfig` config setting in your **imager.php config file**, with the following configuration:

    'storageConfig' => [
        'fortrabbit' => [
            'endpoint' => 'https://' . getenv('OBJECT_STORAGE_SERVER'),
            'accessKey' => getenv('OBJECT_STORAGE_KEY'),
            'secretAccessKey' => getenv('OBJECT_STORAGE_SECRET'),
            'region' => getenv('OBJECT_STORAGE_REGION'),
            'bucket' => getenv('OBJECT_STORAGE_BUCKET'),
            'folder' => 'transforms',
            'requestHeaders' => array(),
        ]
    ],

Enable the storage driver by adding the key `fortrabbit` to Imager's `storages` config setting:

    'storages' => ['fortrabbit'],

Here's an example config, note that the endpoint has to be a complete URL with scheme, and as always you need to make sure that `imagerUrl` is pointed to the right location:

    'imagerUrl' => 'https://foo.objects.frb.io/transforms/',
    'storages' => ['fortrabbit'],
    'storageConfig' => [
        'dospaces'  => [
            'endpoint' => 'https://foo.objects.frb.io',
            'accessKey' => 'foo',
            'secretAccessKey' => '••••••••••••••••••••••••••••••••••••••••••••••••••••••••••••••••',
            'region' => 'us-east-1',
            'bucket' => 'imager-test-bucket',
            'folder' => 'transforms',
            'requestHeaders' => array(),
        ]
    ],
    
Also remember to always empty your Imager transforms cache when adding or removing external storages, as the transforms won't be uploaded if the transform already exists in the cache.