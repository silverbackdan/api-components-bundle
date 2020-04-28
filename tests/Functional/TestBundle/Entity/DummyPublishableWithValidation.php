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

namespace Silverback\ApiComponentBundle\Tests\Functional\TestBundle\Entity;

use ApiPlatform\Core\Annotation\ApiResource;
use Doctrine\ORM\Mapping as ORM;
use Silverback\ApiComponentBundle\Annotation as Silverback;
use Silverback\ApiComponentBundle\Entity\Utility\IdTrait;
use Silverback\ApiComponentBundle\Entity\Utility\PublishableTrait;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @author Daniel West <daniel@silverback.is>
 * @Silverback\Publishable
 * @ApiResource
 * @ORM\Entity
 */
class DummyPublishableWithValidation
{
    use IdTrait;
    use PublishableTrait;

    public function __construct()
    {
        $this->setId();
    }

    /**
     * This constraint will be applied on draft and published resources.
     *
     * @Assert\NotBlank
     */
    public string $name = '';

    /**
     * This constraint will be applied on published resources only.
     *
     * @Assert\NotBlank(groups={"DummyPublishableWithValidation:published"})
     */
    public string $description = '';
}