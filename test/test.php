<?php

include_once __DIR__."/../vendor/autoload.php";

$cache = dirname(__DIR__)."/cache/";
$remoteFile = "test1/test1.txt";

$s3 = \JLaso\S3Wrapper\S3Wrapper::getInstance();

var_dump($s3->listBuckets());

$s3->saveFile($remoteFile, "text1");

var_dump($s3->getFilesList());

$f1 = $s3->getFileIfNewest($cache."test1.txt", $remoteFile);

var_dump(file_get_contents($f1));

$s3->deleteFile($f1, $remoteFile);