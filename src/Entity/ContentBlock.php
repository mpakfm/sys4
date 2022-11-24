<?php

namespace App\Entity;

use App\Repository\ContentBlockRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=ContentBlockRepository::class)
 */
class ContentBlock
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
     * @ORM\Column(type="string", length=255)
     */
    private $code;

    /**
     * @ORM\Column(type="datetime", unique=true)
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
     * @ORM\Column(type="smallint", options={"default" : 1})
     */
    private $active;

    /**
     * @ORM\Column(type="integer", options={"default" : 500})
     */
    private $sort;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $meta_title_section;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $meta_title_element;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function setId(int $id): self
    {
        $this->id = $id;

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

    public function getCode(): ?string
    {
        return $this->code;
    }

    public function setCode(string $code): self
    {
        $this->code = $code;

        return $this;
    }

    public function getDtCreate(): ?\DateTimeInterface
    {
        return $this->dt_create;
    }

    public function setDtCreate(?\DateTimeInterface $dt_create=null): self
    {
        if (is_null($dt_create)) {
            $dt_create = new \DateTime();
        }
        $this->dt_create = $dt_create;

        return $this;
    }

    public function getDtUpdate(): ?\DateTimeInterface
    {
        return $this->dt_update;
    }

    public function setDtUpdate(?\DateTimeInterface $dt_update=null): self
    {
        if (is_null($dt_update)) {
            $dt_update = new \DateTime();
        }
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

    public function getMetaTitleSection(): ?string
    {
        return $this->meta_title_section;
    }

    public function setMetaTitleSection(?string $meta_title_section): self
    {
        $this->meta_title_section = $meta_title_section;

        return $this;
    }

    public function getMetaTitleElement(): ?string
    {
        return $this->meta_title_element;
    }

    public function setMetaTitleElement(?string $meta_title_element): self
    {
        $this->meta_title_element = $meta_title_element;

        return $this;
    }
}
