<?php
namespace User\Authentication;

use Laminas\Authentication\Storage\StorageInterface;

class CustomStorage implements StorageInterface
{

    protected $storageFile;

    public function __construct($storageFile)
    {
        $this->storageFile = $storageFile;
    }

    public function isEmpty()
    {
        return ! file_exists($this->storageFile);
    }

    public function read()
    {
        $contents = '';
        if (file_exists($this->storageFile)) {
            $contents = trim(file_get_contents($this->storageFile));
        }
        return json_decode($contents);
    }

    public function write($contents)
    {
        file_put_contents($this->storageFile, json_encode($contents));
    }

    public function clear()
    {
        if (file_exists($this->storageFile)) {
            unlink($this->storageFile);
        }
    }
}