<?php

namespace App\Entity\Newsletter;

class NewsletterCategory
{
     public const TableName = 'newslettercategory';

     private ?int $id;

     private ?string $denumire;

     private null|string|\DateTime $created_at;

     private null|string|\DateTime $updated_at = null;

    /**
     * @return int|null
     */
    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * @param int|null $id
     * @return NewsletterCategory
     */
    public function setId(?int $id): self
    {
        $this->id = $id;
        return $this;
    }

    /**
     * @return string|null
     */
    public function getDenumire(): ?string
    {
        return $this->denumire;
    }

    /**
     * @param string|null $denumire
     * @return NewsletterCategory
     */
    public function setDenumire(?string $denumire): self
    {
        $this->denumire = $denumire;
        return $this;
    }

    /**
     * @return \DateTime|string|null
     */
    public function getCreatedAt(): \DateTime|string|null
    {
        return $this->created_at;
    }

    /**
     * @param \DateTime|string|null $created_at
     * @return NewsletterCategory
     */
    public function setCreatedAt(\DateTime|string|null $created_at): self
    {
        $this->created_at = $created_at;
        return $this;
    }

    /**
     * @return \DateTime|string|null
     */
    public function getUpdatedAt(): \DateTime|string|null
    {
        return $this->updated_at;
    }

    /**
     * @param \DateTime|string|null $updated_at
     * @return NewsletterCategory
     */
    public function setUpdatedAt(\DateTime|string|null $updated_at): self
    {
        $this->updated_at = $updated_at;
        return $this;
    }


}