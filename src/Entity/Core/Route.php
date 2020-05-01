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

namespace Silverback\ApiComponentsBundle\Entity\Core;

use ApiPlatform\Core\Annotation\ApiResource;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Silverback\ApiComponentsBundle\Annotation as Silverback;
use Silverback\ApiComponentsBundle\Entity\Utility\IdTrait;
use Silverback\ApiComponentsBundle\Entity\Utility\TimestampedTrait;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @author Daniel West <daniel@silverback.is>
 *
 * @Silverback\Timestamped
 * @ApiResource(
 *     collectionOperations={
 *         "get"={"security"="is_granted('ROLE_SUPER_ADMIN')"},
 *         "post"
 *     }
 * )
 * @ORM\Entity(repositoryClass="Silverback\ApiComponentsBundle\Repository\Core\RouteRepository")
 * @Assert\Expression(
 *     "!(this.pageTemplate == null & this.pageData == null) & !(this.pageTemplate != null & this.pageData != null)",
 *     message="Please specify either pageTemplate or pageData, not both."
 * )
 */
class Route
{
    use IdTrait;
    use TimestampedTrait;

    /**
     * @ORM\Column(unique=true)
     * @Assert\NotNull()
     */
    public string $route;

    /**
     * @ORM\Column(unique=true)
     * @Assert\NotNull()
     */
    public string $name;

    /**
     * @ORM\ManyToOne(targetEntity="Silverback\ApiComponentsBundle\Entity\Core\Route", inversedBy="redirectedFrom")
     * @ORM\JoinColumn(name="redirect", referencedColumnName="id", onDelete="SET NULL")
     */
    public ?Route $redirect = null;

    /**
     * @ORM\OneToMany(targetEntity="Silverback\ApiComponentsBundle\Entity\Core\Route", mappedBy="redirect")
     */
    public Collection $redirectedFrom;

    /**
     * @ORM\OneToOne(targetEntity="Silverback\ApiComponentsBundle\Entity\Core\PageTemplate", mappedBy="route")
     * @ORM\JoinColumn(onDelete="SET NULL", nullable=true)
     */
    public ?PageTemplate $pageTemplate = null;

    /**
     * @ORM\OneToOne(targetEntity="Silverback\ApiComponentsBundle\Entity\Core\AbstractPageData", mappedBy="route")
     * @ORM\JoinColumn(onDelete="SET NULL", nullable=true)
     */
    public ?AbstractPageData $pageData = null;

    public function __construct()
    {
        $this->setId();
        $this->redirectedFrom = new ArrayCollection();
    }
}
