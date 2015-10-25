This wrapper allows minimizing S3 access, maintaining a local cache (on server).

[install]
add to your composer.json
{
    ...
    "require": {
       ...
       "jlaso/aws-s3-wrapper": "*",
       ...
    }
}

and run composer update 

or run
composer require jlaso/aws-s3-wrapper *

[Configuration]
You can use the src/config.ini  (created from src/config.ini.sample)  in order to simplify access to the wrapper with
S3Wrapper::getInstance().

Or you can create your own instance passing the data the wrapper needs to be created:
new S3Wrapper($accessKey, $secretKey, $bucket);

[Permissions]
You have to create cache file and give 0777 permissions.

[Test]
In order to OOB test you can use the file test/test.php.

[Use]

Fetch the content o a file.

Save a file.

Get the list of files.

Delete a file
