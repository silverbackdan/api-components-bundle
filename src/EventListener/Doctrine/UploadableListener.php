<?php

/*
 * This file is part of the Silverback API Components Bundle Project
 *
 * (c) Daniel West <daniel@silverback.is>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Silverback\ApiComponentsBundle\EventListener\Doctrine;

use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Mapping\ClassMetadataInfo;
use Doctrine\Persistence\Event\LoadClassMetadataEventArgs;
use Silverback\ApiComponentsBundle\AnnotationReader\UploadableAnnotationReader;

/**
 * @author Vincent Chalamon <vincent@les-tilleuls.coop>
 */
final class UploadableListener
{
    private UploadableAnnotationReader $uploadableAnnotationReader;

    public function __construct(UploadableAnnotationReader $uploadableAnnotationReader)
    {
        $this->uploadableAnnotationReader = $uploadableAnnotationReader;
    }

    public function loadClassMetadata(LoadClassMetadataEventArgs $eventArgs): void
    {
        /** @var ClassMetadataInfo $metadata */
        $metadata = $eventArgs->getClassMetadata();
        $className = $metadata->getName();
        if (!$this->uploadableAnnotationReader->isConfigured($className)) {
            return;
        }

        $em = $eventArgs->getObjectManager();
        if (!$em instanceof EntityManagerInterface) {
            return;
        }

        $configuration = $this->uploadableAnnotationReader->getConfiguration($className);
        if (!$metadata->hasField($configuration->filesInfoField)) {
            $metadata->mapField([
                'fieldName' => $configuration->filesInfoField,
                'type' => 'array',
                'nullable' => true,
            ]);
        }

        $propertyConfigurations = $this->uploadableAnnotationReader->getConfiguredProperties($className, true, true);

        foreach ($propertyConfigurations as $propertyConfiguration) {
            if (!$metadata->hasField($propertyConfiguration->property)) {
                $metadata->mapField([
                    'fieldName' => $propertyConfiguration->property,
                    'type' => 'string',
                    'nullable' => true,
                ]);
            }
        }
    }
}
