<?php

namespace Dothiv\BusinessBundle\ValueObject;
use JMS\Serializer\Annotation as Serializer;

/**
 * @Serializer\ExclusionPolicy("all")
 */
class PathValue implements StringValue
{
    /**
     * @var \SplFileInfo
     */
    private $fi;


    public function __construct($fileName)
    {
        $this->fi = new \SplFileInfo($fileName);
    }

    /**
     * Static constructor.
     *
     * @param string $fileName
     *
     * @return PathValue
     */
    public static function create($fileName)
    {
        $c = __CLASS__;
        return new $c($fileName);
    }

    /**
     * {@inheritdoc}
     * @Serializer\HandlerCallback("json", direction = "serialization")
     */
    public function __toString()
    {
        return $this->fi->getPathname();
    }

    /**
     * @return string
     */
    public function getPathname()
    {
        return (string)$this;
    }

    /**
     * Adds a suffix the the filename.
     *
     * @param $suffix
     *
     * @return self
     */
    public function addFilenameSuffix($suffix)
    {
        $path                 = $this->fi->getPath();
        $ext                  = $this->fi->getExtension();
        $nameWithoutExtension = preg_replace('/\.' . $ext . '$/', '', $this->fi->getFilename());
        $this->fi             = new \SplFileInfo(sprintf('%s/%s%s.%s', $path, $nameWithoutExtension, $suffix, $ext));
        return $this;
    }

    /**
     * Returns the underlying \SplFileInfo object
     *
     * @return \SplFileInfo
     */
    public function getFileInfo()
    {
        return $this->fi;
    }

    /**
     * @see \SplFileInfo::isFile()
     */
    public function isFile()
    {
        return $this->fi->isFile();
    }

    /**
     * @see \SplFileInfo::isDir()
     */
    public function isDir()
    {
        return $this->fi->isDir();
    }
}
