<?php

namespace JLaso\S3Wrapper;

class S3Wrapper
{
    /** @var string  */
    protected $bucket;
    /** @var \Aws\S3\S3Client */
    protected $s3Client = null;
    /** @var string  */
    protected $lastRemoteFile = "";
    /** @var S3Wrapper */
    protected static $instance = null;

    /**
     * @param string $accessKeyId
     * @param string $secretAccessKey
     * @param string $bucket
     */
    function __construct($accessKeyId, $secretAccessKey, $bucket)
    {
        $this->bucket = $bucket;

        $this->s3Client = \Aws\S3\S3Client::factory(array(
            'key' => $accessKeyId,
            'secret' => $secretAccessKey,
            'signature' => 'v4',
            'region' => 'eu-central-1',
        ));

        self::$instance = $this;
    }

    /**
     * @return S3Wrapper
     * @throws \Exception
     */
    public static function getInstance()
    {
        if (!self::$instance) {
            if (!file_exists(__DIR__.'/config.ini')){
                throw new \Exception("Unable to create S3Wrapper instance without config.ini file. Please copy config.ini.sample to config.ini and fill in your AWS S3 access data");
            }
            $config = parse_ini_file(__DIR__.'/config.ini', true);
            $config = $config["s3"];
            new S3Wrapper($config["access_key_id"], $config["secret_access_key"], $config["bucket"]);
        }

        return self::$instance;
    }

    /**
     * @param string $localFile
     * @param string $remoteFile
     * @param int $perm
     * @return string
     */
    function getFileIfNewest($localFile, $remoteFile, $perm = 0777)
    {
        $this->lastRemoteFile = $remoteFile;
        $download = false;
        if (!file_exists($localFile)) {
            $download = true;
        } else {
            $iterator = $this->s3Client->getIterator('ListObjects', array(
                'Bucket' => $this->bucket,
                'Prefix' => $remoteFile,
                'Delimiter' => '/',
            ));

            foreach ($iterator as $object) {
                $remoteDate = date("U", strtotime($object['LastModified']));
                $localDate = filemtime($localFile);

                if ($remoteDate > $localDate) {
                    $download = true;
                }
                break;
            }
        }

        if ($download) {
            try {
                $result = $this->s3Client->getObject(array(
                    'Bucket' => $this->bucket,
                    'Key' => $remoteFile,
                ));
            } catch (\Exception $e) {
                error_log("Error recovering $remoteFile from S3: ".$e->getMessage());
                return null;
            }

            file_put_contents($localFile, $result['Body']);
            chmod($localFile, $perm);
            touch($localFile, strtotime($result['LastModified']));
        }

        return $localFile;
    }

    /**
     * @param string $remoteFile
     * @param string $content
     */
    function saveFile($remoteFile, $content)
    {
        $this->lastRemoteFile = $remoteFile;
        $this->s3Client->upload($this->bucket, $remoteFile, $content);
    }

    /**
     * @return string
     */
    public function getLastRemoteFile()
    {
        return $this->lastRemoteFile;
    }

    /**
     * @param string $path
     * @return array
     */
    public function getFilesList($path = "")
    {
        $files = array();
        $options = array(
            'Bucket' => $this->bucket,
        );
        if ($path){
            $options['Prefix'] = $path;
            $options['Delimiter'] = '/';
        }
        $iterator = $this->s3Client->getIterator('ListObjects', $options);

        foreach ($iterator as $object) {

            $files[] = array(
                'timestamp' => date("U", strtotime($object['LastModified'])),
                'filename' => $object['Key'],
            );
        }

        return $files;
    }

    /**
     * @return mixed
     */
    public function listBuckets()
    {
        $buckets = $this->s3Client->listBuckets();

        return $buckets["Buckets"];
    }

    /**
     * @param string $localFile
     * @param string $remoteFile
     */
    public function deleteFile($localFile, $remoteFile)
    {
        @unlink($localFile);

        $this->s3Client->deleteObject(array(
            'Bucket' => $this->bucket,
            'Key' => $remoteFile,
        ));
    }

}