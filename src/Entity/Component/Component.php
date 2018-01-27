<?php

namespace Silverback\ApiComponentBundle\Entity\Component;

use Silverback\ApiComponentBundle\Entity\Page;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;

/**
 * Class BaseComponent
 * @package Silverback\ApiComponentBundle\Entity\Component
 * @author Daniel West <daniel@silverback.is>
 * @ORM\Entity()
 * @ORM\InheritanceType("SINGLE_TABLE")
 * @ORM\DiscriminatorColumn(name="discr", type="string")
 * @ORM\DiscriminatorMap({
 *     "navbar" = "\Silverback\ApiComponentBundle\Entity\Component\Nav\Navbar\Navbar",
 *     "menu" = "\Silverback\ApiComponentBundle\Entity\Component\Nav\Menu\Menu",
 *     "tabs" = "\Silverback\ApiComponentBundle\Entity\Component\Nav\Tabs\Tabs",
 *     "hero" = "\Silverback\ApiComponentBundle\Entity\Component\Hero",
 *     "form" = "\Silverback\ApiComponentBundle\Entity\Component\Form\Form",
 *     "content" = "\Silverback\ApiComponentBundle\Entity\Component\Content",
 *     "feature_columns" = "\Silverback\ApiComponentBundle\Entity\Component\Feature\Columns\FeatureColumns",
 *     "feature_stacked" = "\Silverback\ApiComponentBundle\Entity\Component\Feature\Stacked\FeatureStacked",
 *     "feature_text_list" = "\Silverback\ApiComponentBundle\Entity\Component\Feature\TextList\FeatureTextList",
 *     "gallery" = "\Silverback\ApiComponentBundle\Entity\Component\Gallery\Gallery",
 *     "news" = "\Silverback\ApiComponentBundle\Entity\Component\News\News"
 * })
 */
abstract class Component
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     * @Groups({"page"})
     * @var int
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity="Silverback\ApiComponentBundle\Entity\Page", inversedBy="components")
     * @var Page
     */
    private $page;

    /**
     * @ORM\Column(type="smallint", nullable=false)
     * @var int
     */
    private $sort = 0;

    /**
     * @ORM\ManyToOne(targetEntity="ComponentGroup", inversedBy="components")
     * @var null|ComponentGroup
     */
    private $group;

    /**
     * @ORM\Column(type="string", nullable=true)
     * @Groups({"page"})
     * @var null|string
     */
    private $className;

    // Kept here so children can call this construct function in future if we ever need to do anything to init all components
    public function __construct()
    {}

    /**
     * @return int|null
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param mixed $id
     */
    public function setId($id): void
    {
        $this->id = $id;
    }

    /**
     * @return Page|null
     */
    public function getPage(): ?Page
    {
        return $this->page;
    }

    /**
     * @param Page $page
     * @param int|null $order
     */
    public function setPage(?Page $page, int $order = null): void
    {
        if ($page && null === $order && !$this->getSort()) {
            // auto ordering
            $lastItem = $page->getComponents()->last();
            if ($lastItem) {
                $this->setSort($lastItem->getSort() + 1);
            }
            if (!$page->getComponents()->contains($this)) {
                $page->addComponent($this);
            }
        }

        $this->page = $page;
    }

    /**
     * @return string
     * @Groups({"route", "page"})
     */
    public function getType()
    {
        $explCls = explode('\\', static::class);
        return array_pop($explCls);
    }

    /**
     * @return int
     */
    public function getSort(): int
    {
        return $this->sort;
    }

    /**
     * @param int $sort
     */
    public function setSort(int $sort): void
    {
        $this->sort = $sort;
    }

    /**
     * @return ComponentGroup|null
     */
    public function getGroup(): ?ComponentGroup
    {
        return $this->group;
    }

    /**
     * @param ComponentGroup|null $group
     */
    public function setGroup(?ComponentGroup $group): void
    {
        $this->group = $group;
    }

    /**
     * @return null|string
     */
    public function getClassName(): ?string
    {
        return $this->className;
    }

    /**
     * @param null|string $className
     */
    public function setClassName(?string $className): void
    {
        $this->className = $className;
    }
}
