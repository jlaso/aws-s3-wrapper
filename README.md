#Class

This wrapper allows minimizing S3 access, maintaining a local cache (on server).

#Installation
add to the composer.json file of your project
{
    ...
    "require": {
       ...
       "jlaso/aws-s3-wrapper": "dev-master",
       ...
    }
}

and run ```composer update``` 

or run
```composer require jlaso/aws-s3-wrapper```

#Configuration

You can use the src/config.ini  (created from src/config.ini.sample)  in order to simplify access to the wrapper with
S3Wrapper::getInstance().

Or you can create your own instance passing the data the wrapper needs to be created:
new S3Wrapper($accessKey, $secretKey, $bucket);

#Permissions
You have to create cache folder and give 0755 permissions. Likely you have to change the owner/group to apache user/group.

#Test
In order to OOB test you can use the file samples/basic.php.

#Use

###Fetch the content o a file.

```php
$s3 = new S3Wrapper($access, $secretm $bucket);
$s3->getFileIfNewest($localFile, $remoteFile);
// you have now the contents of $remoteFile on $localFile
```

###Save a file.

```php
$s3 = new S3Wrapper($access, $secretm $bucket);
$s3->saveFile($remoteFile, $content);
```

###Get the list of files.

```php
$s3 = new S3Wrapper($access, $secretm $bucket);
$fileList = $s3->getFilesList($path);
```

###Delete a file

```php
$s3 = new S3Wrapper($access, $secretm $bucket);
$s3->deleteFile($localFile, $remoteFile);
```