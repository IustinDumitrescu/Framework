<?php

namespace App\Entity\Newsletter;

use DateTime;

class NewsletterComments
{
    public const TableName = 'newsletter_comments';

    /**
     * @var int|null
     */
    private ?int $id;

    /**
     * @var int|null
     */
    private ?int $id_newsletter;

    /**
     * @var int|null
     */
    private ?int $id_user;

    /**
     * @var string
     */
    private string $comentariu;

    /**
     * @var DateTime|string|null
     */
    private DateTime|null|string $created_at;

    /**
     * @var DateTime|string|null
     */
    private DateTime|null|string $updated_at = null;

    /**
     * @return int|null
     */
    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * @param int|null $id
     * @return NewsletterComments
     */
    public function setId(?int $id): self
    {
        $this->id = $id;
        return $this;
    }

    /**
     * @return int|null
     */
    public function getIdNewsletter(): ?int
    {
        return $this->id_newsletter;
    }

    /**
     * @param int|null $id_newsletter
     * @return NewsletterComments
     */
    public function setIdNewsletter(?int $id_newsletter): self
    {
        $this->id_newsletter = $id_newsletter;
        return $this;
    }

    /**
     * @return int|null
     */
    public function getIdUser(): ?int
    {
        return $this->id_user;
    }

    /**
     * @param int|null $id_user
     * @return NewsletterComments
     */
    public function setIdUser(?int $id_user): self
    {
        $this->id_user = $id_user;
        return $this;
    }

    /**
     * @return string
     */
    public function getComentariu(): string
    {
        return $this->comentariu;
    }

    /**
     * @param string $comentariu
     * @return NewsletterComments
     */
    public function setComentariu(string $comentariu): self
    {
        $this->comentariu = $comentariu;
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
     * @return NewsletterComments
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
     * @return NewsletterComments
     */
    public function setUpdatedAt(DateTime|string|null $updated_at): self
    {
        $this->updated_at = $updated_at;
        return $this;
    }


}