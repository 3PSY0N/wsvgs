<?php

namespace App\Entity;

use App\Repository\CategoryRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use Symfony\Component\Serializer\Annotation\Groups;

#[ORM\Entity(repositoryClass: CategoryRepository::class)]
class Category
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups(['icon:read', 'category:read', 'searchable'])]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    #[Groups(['icon:read', 'category:read', 'searchable'])]
    private ?string $name = null;

    #[ORM\Column(length: 255, nullable: true)]
    #[Gedmo\Slug(fields: ['name'])]
    #[Groups(['icon:read', 'category:read', 'searchable'])]
    private ?string $slug = null;

    #[ORM\ManyToMany(targetEntity: Icon::class, mappedBy: 'categories', cascade: ['persist'])]
    #[ORM\JoinTable(name: 'icon_category')]
    #[Groups(['category:read'])]
    private Collection $icons;

    public function __construct()
    {
        $this->icons = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
    }

    public function getSlug(): ?string
    {
        return $this->slug;
    }

    public function setSlug(string $slug): self
    {
        $this->slug = $slug;

        return $this;
    }

    /**
     * @return Collection<int, Icon>
     */
    public function getIcons(): Collection
    {
        return $this->icons;
    }

    public function addIcon(Icon $icon): self
    {
        if (!$this->icons->contains($icon)) {
            $this->icons->add($icon);
            $icon->addCategory($this);
        }

        return $this;
    }

    public function removeIcon(Icon $icon): self
    {
        if ($this->icons->removeElement($icon)) {
            $icon->removeCategory($this);
        }

        return $this;
    }

    public function __toString(): string
    {
        return $this->getName();
    }
}
