<?php

namespace App\Entity\Newsletter;

use DateTime;

class NewsletterContent
{
    public const TableName = 'newsletter_content';

    private ?int $id;

    private ?int $id_categorie;

    private ?string $titlu;

    private ?string $content;

    private ?string $img_prin;

    private ?string $slug;

    private null|string|DateTime $created_at;

    private null|string|DateTime $updated_at = null;

    /**
     * @return int|null
     */
    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * @param int|null $id
     * @return NewsletterContent
     */
    public function setId(?int $id): self
    {
        $this->id = $id;
        return $this;
    }

    /**
     * @return int|null
     */
    public function getIdCategorie(): ?int
    {
        return $this->id_categorie;
    }

    /**
     * @param int|null $id_categorie
     * @return NewsletterContent
     */
    public function setIdCategorie(?int $id_categorie): self
    {
        $this->id_categorie = $id_categorie;
        return $this;
    }

    /**
     * @return string|null
     */
    public function getTitlu(): ?string
    {
        return $this->titlu;
    }

    /**
     * @param string|null $titlu
     * @return NewsletterContent
     */
    public function setTitlu(?string $titlu): self
    {
        $this->titlu = $titlu;
        return $this;
    }

    /**
     * @return string|null
     */
    public function getContent(): ?string
    {
        return $this->content;
    }

    /**
     * @param string|null $content
     * @return NewsletterContent
     */
    public function setContent(?string $content): self
    {
        $this->content = $content;
        return $this;
    }

    /**
     * @return DateTime|string|null
     */
    public function getCreatedAt(): DateTime|string|null
    {
        return $this->created_at;
    }

    /**
     * @param DateTime|string|null $created_at
     * @return NewsletterContent
     */
    public function setCreatedAt(DateTime|string|null $created_at): self
    {
        $this->created_at = $created_at;
        return $this;
    }

    /**
     * @return DateTime|string|null
     */
    public function getUpdatedAt(): DateTime|string|null
    {
        return $this->updated_at;
    }

    /**
     * @param DateTime|string|null $updated_at
     * @return NewsletterContent
     */
    public function setUpdatedAt(DateTime|string|null $updated_at): self
    {
        $this->updated_at = $updated_at;
        return $this;
    }

    /**
     * @return string|null
     */
    public function getImgPrin(): ?string
    {
        return $this->img_prin;
    }

    /**
     * @param string|null $img_prin
     * @return NewsletterContent
     */
    public function setImgPrin(?string $img_prin): self
    {
        $this->img_prin = $img_prin;
        return $this;
    }

    /**
     * @return string|null
     */
    public function getSlug(): ?string
    {
        return $this->slug;
    }

    /**
     * @param string|null $slug
     * @return NewsletterContent
     */
    public function setSlug(?string $slug): self
    {
        $this->slug = $slug;
        return $this;
    }



}