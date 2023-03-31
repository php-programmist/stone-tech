<?php

namespace App\Entity;

use App\Repository\ContentRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=ContentRepository::class)
 */
class Content
{

    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */

    private $id;

    /**
     * @ORM\Column(type="string", length=50)
     */
    private $page_type;

    /**
     * @ORM\Column(type="string", length=100)
     */
    private $path;

    /**
     * @ORM\Column(type="string", length=150)
     */
    private $name;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $seo_title;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    private $seo_description;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $parent;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    private $seo_text;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $card_title;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    private $card_description;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $card_price;


    /**
     * @ORM\Column(type="boolean")
     */
    private $top_menu;

    /**
     * @ORM\Column(type="boolean")
     */
    private $index_menu;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $thumb_img;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $updated;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $card_image;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    private $seo_text_hidden;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $seo_text_img;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $menu_name;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $menu_order;

    /**
     * @ORM\ManyToOne(targetEntity=Category::class, inversedBy="content", cascade={"persist", "remove"})
     */
    private $category_id;

    /**
     * @ORM\OneToMany(targetEntity=CityPages::class, mappedBy="parent")
     */
    private $cityPages;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $template;

    public function __construct()
    {
        $this->cityPages = new ArrayCollection();
    }

    public function __toString(){
        return (string) $this->getName();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getPageType(): ?string
    {
        return $this->page_type;
    }

    public function setPageType(string $page_type): self
    {
        $this->page_type = $page_type;

        return $this;
    }

    public function getPath(): ?string
    {
        return $this->path;
    }

    public function setPath(string $path): self
    {
        $this->path = $path;

        return $this;
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

    public function getSeoTitle(): ?string
    {
        return $this->seo_title;
    }

    public function setSeoTitle(?string $seo_title): self
    {
        $this->seo_title = $seo_title;

        return $this;
    }

    public function getSeoDescription(): ?string
    {
        return $this->seo_description;
    }

    public function setSeoDescription(?string $seo_description): self
    {
        $this->seo_description = $seo_description;

        return $this;
    }

    public function getParent(): ?int
    {
        return $this->parent;
    }

    public function setParent(?int $parent): self
    {
        $this->parent = $parent;

        return $this;
    }

    public function getSeoText(): ?string
    {
        return $this->seo_text;
    }

    public function setSeoText(?string $seo_text): self
    {
        $this->seo_text = $seo_text;

        return $this;
    }

    public function getCardTitle(): ?string
    {
        return $this->card_title;
    }

    public function setCardTitle(?string $card_title): self
    {
        $this->card_title = $card_title;

        return $this;
    }

    public function getCardDescription(): ?string
    {
        return $this->card_description;
    }

    public function setCardDescription(?string $card_description): self
    {
        $this->card_description = $card_description;

        return $this;
    }

    public function getCardPrice(): ?int
    {
        return $this->card_price;
    }

    public function setCardPrice(?int $card_price): self
    {
        $this->card_price = $card_price;

        return $this;
    }

    public function getTopMenu(): ?bool
    {
        return $this->top_menu;
    }

    public function setTopMenu(bool $top_menu): self
    {
        $this->top_menu = $top_menu;

        return $this;
    }

    public function getIndexMenu(): ?bool
    {
        return $this->index_menu;
    }

    public function setIndexMenu(bool $index_menu): self
    {
        $this->index_menu = $index_menu;

        return $this;
    }

    public function getThumbImg(): ?string
    {
        return $this->thumb_img;
    }

    public function setThumbImg(?string $thumb_img): self
    {
        $this->thumb_img = $thumb_img;

        return $this;
    }

    public function getUpdated(): ?\DateTimeInterface
    {
        return $this->updated;
    }

    public function setUpdated(?\DateTimeInterface $updated): self
    {
        $this->updated = $updated;

        return $this;
    }

    public function getCardImage(): ?string
    {
        return $this->card_image;
    }

    public function setCardImage(?string $card_image): self
    {
        $this->card_image = $card_image;

        return $this;
    }

    public function getSeoTextHidden(): ?string
    {
        return $this->seo_text_hidden;
    }

    public function setSeoTextHidden(?string $seo_text_hidden): self
    {
        $this->seo_text_hidden = $seo_text_hidden;

        return $this;
    }

    public function getSeoTextImg(): ?string
    {
        return $this->seo_text_img;
    }

    public function setSeoTextImg(?string $seo_text_img): self
    {
        $this->seo_text_img = $seo_text_img;

        return $this;
    }

    public function getMenuName(): ?string
    {
        return $this->menu_name;
    }

    public function setMenuName(?string $menu_name): self
    {
        $this->menu_name = $menu_name;

        return $this;
    }

    public function getMenuOrder(): ?int
    {
        return $this->menu_order;
    }

    public function setMenuOrder(?int $menu_order): self
    {
        $this->menu_order = $menu_order;

        return $this;
    }

    public function getCategoryId(): ?Category
    {
        return $this->category_id;
    }

    public function setCategoryId(?Category $category_id): self
    {
        $this->category_id = $category_id;

        return $this;
    }

    /**
     * @return Collection|CityPages[]
     */
    public function getCityPages(): Collection
    {
        return $this->cityPages;
    }

    public function addCityPage(CityPages $cityPage): self
    {
        if (!$this->cityPages->contains($cityPage)) {
            $this->cityPages[] = $cityPage;
            $cityPage->setParent($this);
        }

        return $this;
    }

    public function removeCityPage(CityPages $cityPage): self
    {
        if ($this->cityPages->removeElement($cityPage)) {
            // set the owning side to null (unless already changed)
            if ($cityPage->getParent() === $this) {
                $cityPage->setParent(null);
            }
        }

        return $this;
    }

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Measure", inversedBy="contents")
     */

    private $measure;

    public function getMeasure(): ?Measure
    {
        return $this->measure;
    }

    public function setMeasure(?Measure $measure): self
    {
        $this->measure = $measure;

        return $this;
    }

    /**
     * @return string
     */
    public function getTemplate():?string
    {
        return $this->template;
    }

    /**
     * @param string|null $template
     * @return $this
     */
    public function setTemplate(?string $template):self
    {
        $this->template = $template;
        return $this;
    }
}
