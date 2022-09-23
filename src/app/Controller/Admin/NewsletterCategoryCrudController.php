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
        $createdAt = new AdminListingField('Creat la', 'created_at', DateType::class, []);
        $updatedAt = new AdminListingField('Editat la', 'updated_at', DateType::class, []);

        return match ($context->getCurrentAction()->getName()) {
            AdminContext::AdminActionShow, AdminContext::AdminActionIndex => [$id,$denumire, $createdAt, $updatedAt],
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

        $newsLetterCategory = new NewsletterCategory();

        $newsLetterCategory
            ->setDenumire(filter_var($dataFromForm["denumire"], FILTER_SANITIZE_FULL_SPECIAL_CHARS))
            ->setCreatedAt((new \DateTime())->format('Y-m-d H:i:s'));

        $persist = $em->persist($newsLetterCategory);

        $em->flush($persist);

        return  [
            "string" => "Categoria a fost creata cu success!",
            "value" => true
        ];
    }
}