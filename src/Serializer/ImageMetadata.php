<?php

namespace Silverback\ApiComponentBundle\Serializer;

use Silverback\ApiComponentBundle\Exception\FileMissingException;
use Silverback\ApiComponentBundle\Exception\FileNotImageException;
use Symfony\Component\Serializer\Annotation\Groups;

class ImageMetadata
{
    /**
     * @Groups({"component", "content", "route"})
     * @var int
     */
    private $width;
    /**
     * @Groups({"component", "content", "route"})
     * @var int
     */
    private $height;
    /**
     * @Groups({"component", "content", "route"})
     * @var string
     */
    private $filePath;
    /**
     * @Groups({"component", "content", "route"})
     * @var string
     */
    private $publicPath;
    /**
     * @Groups({"component", "content", "route"})
     * @var string|null
     */
    private $imagineKey;

    public function __construct(
        string $filePath,
        string $publicPath,
        ?string $imagineKey = null
    )
    {
        $this->filePath = $filePath;
        $this->publicPath = $publicPath;

        if (!file_exists($filePath)) {
            throw new FileMissingException(sprintf('The file %s does not exist while constructing %s', $filePath, self::class));
        }

        if (false === \exif_imagetype($filePath)) {
            throw new FileNotImageException(sprintf('The file %s is not an image while constructing %s', $filePath, self::class));
        }

        [$this->width, $this->height] = getimagesize($filePath);
        $this->imagineKey= $imagineKey;
    }

    /**
     * @return int
     */
    public function getWidth(): int
    {
        return $this->width;
    }

    /**
     * @return int
     */
    public function getHeight(): int
    {
        return $this->height;
    }

    /**
     * @return string
     */
    public function getFilePath(): string
    {
        return $this->filePath;
    }

    /**
     * @return string
     */
    public function getPublicPath(): string
    {
        return $this->publicPath;
    }

    /**
     * @return null|string
     */
    public function getImagineKey(): ?string
    {
        return $this->imagineKey;
    }
}
