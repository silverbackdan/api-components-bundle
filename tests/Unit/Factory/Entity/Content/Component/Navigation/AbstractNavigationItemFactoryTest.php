<?php

namespace Silverback\ApiComponentBundle\Tests\Unit\Factory\Entity\Content\Component\Navigation;

use Silverback\ApiComponentBundle\Entity\Content\Component\Navigation\AbstractNavigationItem;
use Silverback\ApiComponentBundle\Entity\Route\Route;
use Silverback\ApiComponentBundle\Exception\InvalidFactoryOptionException;
use Silverback\ApiComponentBundle\Factory\Entity\Content\Component\Navigation\AbstractNavigationItemFactory;
use Silverback\ApiComponentBundle\Tests\Unit\Factory\Entity\AbstractFactory;

class AbstractNavigationItemFactoryTest extends AbstractFactory
{
    protected $presets = ['component'];

    /**
     * @inheritdoc
     */
    public function setUp()
    {
        $this->className = AbstractNavigationItemFactory::class;
        $this->componentClassName = AbstractNavigationItem::class;
        $this->isFinal = false;
        $this->testOps = [
            'className' => 'dummy-class',
            'label' => 'Nav item label',
            'route' => new Route(),
            'fragment' => 'dummy-fragment'
        ];
        parent::setUp();
    }

    public function test_invalid_option_handling(): void
    {
        $this->expectException(InvalidFactoryOptionException::class);
        $method = $this->reflection->getMethod('setOptions');
        $method->setAccessible(true);
        $method->invokeArgs($this->factory, [[ 'ops' => null ]]);
    }
}