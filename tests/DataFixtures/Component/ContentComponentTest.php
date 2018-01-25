<?php

namespace Silverback\ApiComponentBundle\Tests\DataFixtures\Component;

use Doctrine\Common\Persistence\ObjectManager;
use PHPUnit\Framework\TestCase;
use Silverback\ApiComponentBundle\DataFixtures\Component\ContentComponent;
use Silverback\ApiComponentBundle\Entity\Component\Content;
use Silverback\ApiComponentBundle\Entity\Page;

class ContentComponentTest extends TestCase
{
    private $objectManagerProphecy;
    private $component;

    public function setUp ()
    {
        $this->objectManagerProphecy = $this->prophesize(ObjectManager::class);
        $this->component = new ContentComponent();
        $this->component->load($this->objectManagerProphecy->reveal());
    }

    public function test_get_component ()
    {
        $this->assertInstanceOf(Content::class, $this->component->getComponent());
    }

    public function test_default_op_keys ()
    {
        $optionKeys = array_keys($this->component::defaultOps());
        $this->assertContains('lipsum', $optionKeys);
        $this->assertContains('content', $optionKeys);
    }

    public function test_create_lipsum ()
    {
        $component = $this->component->create(new Page(), [
            'lipsum' => ['1', 'short']
        ]);
        $this->assertRegExp('/^<p>.*<\/p>/', $component->getContent());
    }

    public function test_create_custom ()
    {
        $component = $this->component->create(new Page(), [
            'content' => 'ABCDEFG'
        ]);
        $this->assertEquals('ABCDEFG', $component->getContent());
    }
}
