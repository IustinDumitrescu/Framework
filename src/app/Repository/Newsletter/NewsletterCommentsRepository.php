<?php

namespace App\Repository\Newsletter;

use App\Entity\Newsletter\NewsletterComments;
use App\Entity\Newsletter\NewsletterContent;
use App\Entity\UserEntity;
use App\Repository\EntityManager;
use App\Repository\QueryBuilder;
use JetBrains\PhpStorm\ArrayShape;

class NewsletterCommentsRepository extends EntityManager
{
    #[ArrayShape(["comentariu" => "string", "imgPrin" => "null|string", "date" => "\DateTime|null|string", "name" => "string"])]
    public function createComment(NewsletterContent $content, UserEntity $user, string $comment): array
    {
        $commentariu = new NewsletterComments();

        $commentariu
            ->setIdNewsletter($content->getId())
            ->setIdUser($user->getId())
            ->setComentariu($comment)
            ->setCreatedAt((new \DateTime())->format('Y-m-d H:i:s'));

        $persist = $this->persist($commentariu);

        $this->flush($persist);

        return [
            "comentariu" => $commentariu->getComentariu(),
            "imgPrin" => $user->getImgPrin(),
            "date" => $commentariu->getCreatedAt(),
            "name" => $user->getFirstName() . " " . $user->getLastName()
        ];
    }

    public function getQbOfNewsletterComments(NewsletterContent $content): QueryBuilder
    {
        return $this->createQueryBuilder()
            ->setOperation(QueryBuilder::Select)
            ->select(['*'])
            ->from(NewsletterComments::class)
            ->where('id_newsletter ='.$content->getId())
            ->orderBy('created_at', 'DESC');
    }


}