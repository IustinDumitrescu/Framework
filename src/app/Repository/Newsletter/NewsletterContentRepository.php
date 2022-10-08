<?php

namespace App\Repository\Newsletter;

use App\Entity\Newsletter\NewsletterCategory;
use App\Entity\Newsletter\NewsletterContent;
use App\Repository\EntityManager;
use App\Utils\Utils;

class NewsletterContentRepository extends EntityManager
{
    public function updateBulkNewsletterContent(NewsletterContent $newsletterContent, array $arrayOfChanges): NewsletterContent
    {
        $qb = $this->createQueryBuilder()
            ->setOperation('update')
            ->update(NewsletterContent::class)
            ->where("id = ". $newsletterContent->getId())
            ->set('updated_at', (new \DateTime())->format('Y-m-d H:i:s'));

        if (!empty($arrayOfChanges)) {
            foreach ($arrayOfChanges as $field => $value) {
                $set = 'set'. Utils::dashesToCamelCase($field);
                $qb->set($field, $value);
                $newsletterContent
                    ->$set($value);
            }
        }

        $qb
            ->getQuery()
            ->execute();

        return $newsletterContent->setUpdatedAt((new \DateTime())->format('Y-m-d H:i:s'));
    }

    public function getSomeNewsletterOfEachCategory(array $arrayOfIds): array
    {
        $stringOfIds = '';

        foreach ($arrayOfIds as $id) {
            $stringOfIds .= "$id,";
        }

        $stringOfIds = rtrim($stringOfIds, ',');

        $query = "
SELECT sbs1.* 
FROM (
    SELECT 
        nc.*,
         ROW_NUMBER() OVER(PARTITION BY nc.id_categorie ORDER BY RAND()) rn
    FROM newsletter_content nc
    where nc.id_categorie in ($stringOfIds)       
) sbs1
WHERE sbs1.rn <= 3
ORDER BY sbs1.created_at DESC
        ";

        return $this->createQueryBuilder()
            ->from(NewsletterContent::class)
            ->setDql($query)
            ->getResult();
    }

    public function getNewsletterByCategory(
        NewsletterCategory $category,
        ?string $query,
        bool $arrayForm = false
    ): \App\Repository\QueryBuilder
    {
        $qb = $this->createQueryBuilder()
            ->setOperation('select')
            ->from(NewsletterContent::class)
            ->where('id_categorie ='. $category->getId())
            ->select(['*']);

        if ($query) {
            $qb
                ->andWhere("titlu LIKE '%$query%'");
        }

        return $qb;
    }
}