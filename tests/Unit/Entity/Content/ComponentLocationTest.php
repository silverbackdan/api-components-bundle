<?php

namespace Silverback\ApiComponentBundle\Tests\Unit\Entity\Content;

use Silverback\ApiComponentBundle\Entity\Component\ComponentLocation;
use Silverback\ApiComponentBundle\Tests\Unit\Entity\AbstractEntity;
use Symfony\Component\Validator\Constraints\NotBlank;

class ComponentLocationTest extends AbstractEntity
{
    public function test_constraints()
    {
        $entity = new \Silverback\ApiComponentBundle\Entity\Component\ComponentLocation();
        $constraints = $this->getConstraints($entity);
        $this->assertTrue($this->instanceInArray(NotBlank::class, $constraints['content']));
        $this->assertTrue($this->instanceInArray(NotBlank::class, $constraints['component']));
    }
}
