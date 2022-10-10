<?php

namespace App\Controller\Admin;

use App\Admin\Action\AdminAction;
use App\Admin\Config\AdminContext;
use App\Admin\Config\AdminDashboardConfigurator;
use App\Admin\Fields\AdminListingField;
use App\Entity\Newsletter\NewsletterComments;
use App\Form\InputTypes\DateType;
use App\Form\InputTypes\NumberType;
use App\Form\InputTypes\TextType;

class NewsletterCommentsCrudController extends AbstractCrudController
{

    public function getEntityName(): string
    {
        return NewsletterComments::class;
    }

    public function configureActions(): array
    {
        return [
            new AdminAction(AdminContext::AdminActionIndex,
                '',
                '',
                true,
                AdminDashboardConfigurator::PageIndex
            ),
            new AdminAction(AdminContext::AdminActionShow,
                'Show',
                'fa-solid fa-eye m-1',
                false,
                AdminDashboardConfigurator::PageShow
            ),
            new AdminAction(AdminContext::AdminActionDelete,
                'Delete',
                'fa-solid fa-trash-can',
                false,
                AdminDashboardConfigurator::PageDelete
            )
        ];
    }

    public function getName(): string
    {
        return 'NewsletterComments';
    }

    protected function handleNewEntityPersist(array $dataFromForm): array
    {
         return [];
    }

    public function configureFields(AdminContext $context): array
    {
        $id = new AdminListingField('Id','id',NumberType::class, []);
        $idUser = new AdminListingField('Id User','id_user',NumberType::class, []);
        $idNewsletter = new AdminListingField('Id Newsletter','id_newsletter',NumberType::class, []);
        $comentariu =  $id = new AdminListingField('Mesaj','id',TextType::class, []);

        $createdAt = new AdminListingField('Creat la', 'created_at', DateType::class, []);
        $updatedAt = new AdminListingField('Editat la', 'updated_at', DateType::class, []);

        return match ($context->getCurrentAction()->getName()) {
            AdminContext::AdminActionShow, AdminContext::AdminActionIndex,
            AdminContext::AdminActionNew, AdminContext::AdminActionEdit => [
                $id, $idUser, $idNewsletter, $comentariu, $createdAt, $updatedAt
            ],
        };
    }
}