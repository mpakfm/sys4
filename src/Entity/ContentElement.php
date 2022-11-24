<?php

namespace App\Entity;

use App\Repository\ContentElementRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=ContentElementRepository::class)
 */
class ContentElement
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $name;

    /**
     * @ORM\Column(type="datetime")
     */
    private $dt_create;

    /**
     * @ORM\Column(type="datetime")
     */
    private $dt_update;

    /**
     * @ORM\Column(type="integer")
     */
    private $user_create;

    /**
     * @ORM\Column(type="integer")
     */
    private $user_update;

    /**
     * @ORM\Column(type="integer")
     */
    private $content_id;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $section_id;

    /**
     * @ORM\Column(type="smallint", options={"default" : 1})
     */
    private $active;

    /**
     * @ORM\Column(type="integer", options={"default" : 500})
     */
    private $sort;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    private $preview_text;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $preview_picture;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    private $detail_text;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $detail_picture;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $code;

    /**
     * @ORM\Column(type="integer", options={"default" : 0})
     */
    private $counter;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $meta_title;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    private $meta_description;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    private $meta_keywords;

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

    public function getDtCreate(): ?\DateTimeInterface
    {
        return $this->dt_create;
    }

    public function setDtCreate(\DateTimeInterface $dt_create): self
    {
        $this->dt_create = $dt_create;

        return $this;
    }

    public function getDtUpdate(): ?\DateTimeInterface
    {
        return $this->dt_update;
    }

    public function setDtUpdate(\DateTimeInterface $dt_update): self
    {
        $this->dt_update = $dt_update;

        return $this;
    }

    public function getUserCreate(): ?int
    {
        return $this->user_create;
    }

    public function setUserCreate(int $user_create): self
    {
        $this->user_create = $user_create;

        return $this;
    }

    public function getUserUpdate(): ?int
    {
        return $this->user_update;
    }

    public function setUserUpdate(int $user_update): self
    {
        $this->user_update = $user_update;

        return $this;
    }

    public function getContentId(): ?int
    {
        return $this->content_id;
    }

    public function setContentId(int $content_id): self
    {
        $this->content_id = $content_id;

        return $this;
    }

    public function getSectionId(): ?int
    {
        return $this->section_id;
    }

    public function setSectionId(?int $section_id): self
    {
        $this->section_id = $section_id;

        return $this;
    }

    public function getActive(): ?int
    {
        return $this->active;
    }

    public function setActive(int $active): self
    {
        $this->active = $active;

        return $this;
    }

    public function getSort(): ?int
    {
        return $this->sort;
    }

    public function setSort(int $sort): self
    {
        $this->sort = $sort;

        return $this;
    }

    public function getPreviewText(): ?string
    {
        return $this->preview_text;
    }

    public function setPreviewText(?string $preview_text): self
    {
        $this->preview_text = $preview_text;

        return $this;
    }

    public function getPreviewPicture(): ?int
    {
        return $this->preview_picture;
    }

    public function setPreviewPicture(?int $preview_picture): self
    {
        $this->preview_picture = $preview_picture;

        return $this;
    }

    public function getDetailText(): ?string
    {
        return $this->detail_text;
    }

    public function setDetailText(?string $detail_text): self
    {
        $this->detail_text = $detail_text;

        return $this;
    }

    public function getDetailPicture(): ?int
    {
        return $this->detail_picture;
    }

    public function setDetailPicture(?int $detail_picture): self
    {
        $this->detail_picture = $detail_picture;

        return $this;
    }

    public function getCode(): ?string
    {
        return $this->code;
    }

    public function setCode(?string $code): self
    {
        $this->code = $code;

        return $this;
    }

    public function getCounter(): ?int
    {
        return $this->counter;
    }

    public function setCounter(?int $counter): self
    {
        $this->counter = $counter;

        return $this;
    }

    public function getMetaTitle(): ?string
    {
        return $this->meta_title;
    }

    public function setMetaTitle(?string $meta_title): self
    {
        $this->meta_title = $meta_title;

        return $this;
    }

    public function getMetaDescription(): ?string
    {
        return $this->meta_description;
    }

    public function setMetaDescription(?string $meta_description): self
    {
        $this->meta_description = $meta_description;

        return $this;
    }

    public function getMetaKeywords(): ?string
    {
        return $this->meta_keywords;
    }

    public function setMetaKeywords(?string $meta_keywords): self
    {
        $this->meta_keywords = $meta_keywords;

        return $this;
    }
}
