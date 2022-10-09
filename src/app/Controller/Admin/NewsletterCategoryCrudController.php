<?php

namespace App\Controller\Admin;

use App\Admin\Config\AdminContext;
use App\Admin\Fields\AdminListingField;
use App\Entity\Newsletter\NewsletterCategory;
use App\Form\InputTypes\DateType;
use App\Form\InputTypes\NumberType;
use App\Form\InputTypes\TextType;
use App\Repository\EntityManager;
use JetBrains\PhpStorm\ArrayShape;

class NewsletterCategoryCrudController extends AbstractCrudController
{

    public function getEntityName(): string
    {
        return NewsletterCategory::class;
    }

    public function getName(): string
    {
        return 'NewsletterCategory';
    }

    public function configureFields(AdminContext $context): array
    {
        $id = new AdminListingField('Id','id',NumberType::class, []);
        $denumire = new AdminListingField('Denumire', 'denumire', TextType::class, [
                "required" => true
            ]
        );
        $slug = new AdminListingField('Slug', 'slug', TextType::class, []);
        $createdAt = new AdminListingField('Creat la', 'created_at', DateType::class, []);
        $updatedAt = new AdminListingField('Editat la', 'updated_at', DateType::class, []);

        return match ($context->getCurrentAction()->getName()) {
            AdminContext::AdminActionShow, AdminContext::AdminActionIndex => [$id,$denumire, $createdAt, $updatedAt, $slug],
            AdminContext::AdminActionNew, AdminContext::AdminActionEdit => [$denumire],
            default => [],
        };
    }

    #[ArrayShape(["string" => "string", "value" => "false"])]
    protected function handleNewEntityPersist(array $dataFromForm): array
    {
        $em = $this->container->get(EntityManager::class);

        if (empty($dataFromForm["denumire"])) {
            return [
                "string" => "Denumirea este invalida!",
                "value" => false
            ];
        }

        $category = $em->findBy(NewsletterCategory::class, [
            "denumire" => filter_var($dataFromForm["denumire"], FILTER_SANITIZE_FULL_SPECIAL_CHARS)
        ]);

        if (!empty($category)) {
            return [
                "string" => "Categoria exista deja !",
                "value" => false
            ];
        }

        $newsLetterCategory = new NewsletterCategory();

        $newsLetterCategory
            ->setDenumire(filter_var($dataFromForm["denumire"], FILTER_SANITIZE_FULL_SPECIAL_CHARS))
            ->setCreatedAt((new \DateTime())->format('Y-m-d H:i:s'));;

        $newsLetterCategory
            ->setSlug(str_replace(' ', '-', strtolower(trim(preg_replace('/[^A-Za-z0-9]/',' ',$newsLetterCategory->getDenumire())))));

        $persist = $em->persist($newsLetterCategory);

        $em->flush($persist);

        return  [
            "string" => "Categoria a fost creata cu success!",
            "value" => true
        ];
    }

    public function handleEdit(array $data, AdminContext $context, EntityManager $em, object $entity)
    {
        $arrayOfChanges = [];

        if (!empty($data["denumire"]) && $data["denumire"] !== $entity->getDenumire()) {
            $arrayOfChanges["denumire"] = filter_var($data["denumire"], FILTER_SANITIZE_FULL_SPECIAL_CHARS);
            $arrayOfChanges["slug"] = str_replace(' ', '-', strtolower(trim(preg_replace('/[^A-Za-z0-9]/',' ',$data["denumire"]))));
            $arrayOfChanges["updated_at"] = (new \DateTime())->format('Y-m-d H:m:i');
        }

        if (!empty($arrayOfChanges)) {
            $queryBuilder = $em->createQueryBuilder()
                ->setOperation('update')
                ->update($context->getController()->getEntityName());

            foreach ($arrayOfChanges as $keys => $value) {
                $queryBuilder->set($keys, $value);
            }

            $queryBuilder
                ->where("id = ". $entity->getId())
                ->getQuery()
                ->execute();
        }

        $this->redirectToRoute('/admin', [
            "crudCon" => $context->getControllerName(),
            "action" => AdminContext::AdminActionEdit,
            "entityId" => $entity->getId(),
            "signature" => $context->createSignatureUrl($context->getAdmin(), AdminContext::AdminActionEdit)
        ]);
    }
}