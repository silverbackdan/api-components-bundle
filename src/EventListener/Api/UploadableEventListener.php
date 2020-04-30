<?php

/*
 * This file is part of the Silverback API Component Bundle Project
 *
 * (c) Daniel West <daniel@silverback.is>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Silverback\ApiComponentBundle\EventListener\Api;

use Doctrine\Persistence\ManagerRegistry;
use Silverback\ApiComponentBundle\Annotation\UploadableField;
use Silverback\ApiComponentBundle\AnnotationReader\UploadableAnnotationReader;
use Silverback\ApiComponentBundle\Utility\ClassMetadataTrait;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Event\ViewEvent;
use Symfony\Component\PropertyAccess\PropertyAccess;

/**
 * @author Vincent Chalamon <vincent@les-tilleuls.coop>
 */
final class UploadableEventListener
{
    use ClassMetadataTrait;

    private UploadableAnnotationReader $uploadableAnnotationReader;

    public function __construct(ManagerRegistry $registry, UploadableAnnotationReader $uploadableAnnotationReader)
    {
        $this->initRegistry($registry);
        $this->uploadableAnnotationReader = $uploadableAnnotationReader;
    }

    public function onPreWrite(ViewEvent $event): void
    {
        $request = $event->getRequest();
        $data = $request->attributes->get('data');
        if (
            empty($data) ||
            !$this->uploadableAnnotationReader->isConfigured($data) ||
            $request->isMethod(Request::METHOD_DELETE)
        ) {
            return;
        }

        $propertyAccessor = PropertyAccess::createPropertyAccessor();
        $classMetadata = $this->getClassMetadata($data);
        /**
         * @var UploadableField[] $fieldConfiguration
         */
        foreach ($this->uploadableAnnotationReader->getConfiguredProperties($data, true, true) as $fileProperty => $fieldConfiguration) {
            $file = $propertyAccessor->getValue($data, $fileProperty);
            $classMetadata->setFieldValue($data, $fieldConfiguration->property, '/new_filepath');
            // todo Upload file to adapter
            // todo Set fileName on object
            // todo Set $field value to null for security/performance reasons
        }
    }
}
