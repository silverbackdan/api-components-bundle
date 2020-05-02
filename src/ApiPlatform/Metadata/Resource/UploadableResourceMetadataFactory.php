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

namespace Silverback\ApiComponentsBundle\ApiPlatform\Metadata\Resource;

use ApiPlatform\Core\Metadata\Resource\Factory\ResourceMetadataFactoryInterface;
use ApiPlatform\Core\Metadata\Resource\ResourceMetadata;
use ApiPlatform\Core\Operation\PathSegmentNameGeneratorInterface;
use Silverback\ApiComponentsBundle\Action\Uploadable\UploadableAction;
use Silverback\ApiComponentsBundle\AnnotationReader\UploadableAnnotationReader;

/**
 * Configures API Platform metadata for file resources.
 *
 * @author Daniel West <daniel@silverback.is>
 */
class UploadableResourceMetadataFactory implements ResourceMetadataFactoryInterface
{
    private ResourceMetadataFactoryInterface $decorated;
    private UploadableAnnotationReader $uploadableHelper;
    private PathSegmentNameGeneratorInterface $pathSegmentNameGenerator;

    public function __construct(ResourceMetadataFactoryInterface $decorated, UploadableAnnotationReader $fileHelper, PathSegmentNameGeneratorInterface $pathSegmentNameGenerator)
    {
        $this->decorated = $decorated;
        $this->uploadableHelper = $fileHelper;
        $this->pathSegmentNameGenerator = $pathSegmentNameGenerator;
    }

    public function create(string $resourceClass): ResourceMetadata
    {
        $resourceMetadata = $this->decorated->create($resourceClass);
        if (!$this->uploadableHelper->isConfigured($resourceClass)) {
            return $resourceMetadata;
        }

        $fields = $this->uploadableHelper->getConfiguredProperties($resourceClass, false, false);
        $properties = [];
        foreach ($fields as $field) {
            $properties[$field] = [
                'type' => 'string',
                'format' => 'binary',
            ];
        }
        $resourceMetadata = $this->getCollectionPostResourceMetadata($resourceMetadata, $properties);

        return $this->getItemPutResourceMetadata($resourceMetadata, $properties);
    }

    private function getCollectionPostResourceMetadata(ResourceMetadata $resourceMetadata, array $properties): ResourceMetadata
    {
        $resourceShortName = $resourceMetadata->getShortName();
        $path = sprintf('/%s/upload', $this->pathSegmentNameGenerator->getSegmentName($resourceShortName));

        $collectionOperations = $resourceMetadata->getCollectionOperations() ?? [];
        $collectionOperations['post_upload'] = array_merge(['method' => 'POST'], $this->getOperationConfiguration($properties, $path));

        return $resourceMetadata->withCollectionOperations($collectionOperations);
    }

    private function getItemPutResourceMetadata(ResourceMetadata $resourceMetadata, array $properties): ResourceMetadata
    {
        $resourceShortName = $resourceMetadata->getShortName();
        $path = sprintf('/%s/{id}/upload', $this->pathSegmentNameGenerator->getSegmentName($resourceShortName));

        $itemOperations = $resourceMetadata->getItemOperations() ?? [];
        $putProperties = $this->getOperationConfiguration($properties, $path);
        $itemOperations['put_upload'] = array_merge(['method' => 'PUT'], $putProperties);
        $itemOperations['patch_upload'] = array_merge(['method' => 'PATCH'], $putProperties);

        return $resourceMetadata->withItemOperations($itemOperations);
    }

    private function getOperationConfiguration(array $properties, string $path): array
    {
        return [
            'controller' => UploadableAction::class,
            'path' => $path,
            'deserialize' => false,
            'read' => 'false',
            'openapi_context' => [
                'requestBody' => [
                    'content' => [
                        'multipart/form-data' => [
                            'schema' => [
                                'type' => 'object',
                                'properties' => $properties,
                            ],
                        ],
                    ],
                ],
            ],
        ];
    }
}
