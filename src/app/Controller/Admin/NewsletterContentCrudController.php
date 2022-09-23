<?php

namespace App\Controller\Admin;

use App\Admin\Action\AdminAction;
use App\Admin\Config\AdminContext;
use App\Admin\Config\AdminDashboardConfigurator;
use App\Admin\Fields\AdminListingField;
use App\Entity\Newsletter\NewsletterCategory;
use App\Entity\Newsletter\NewsletterContent;
use App\Form\InputTypes\ChoiceType;
use App\Form\InputTypes\DateType;
use App\Form\InputTypes\NumberType;
use App\Form\InputTypes\TextareaType;
use App\Form\InputTypes\TextType;
use App\Form\InputTypes\UploadableFieldType;
use App\Http\Request;
use App\Kernel;
use App\Repository\EntityManager;
use App\Service\MediaUploadService;
use App\Service\NewsletterService;
use App\Utils\Utils;

class NewsletterContentCrudController extends AbstractCrudController
{

    public function getEntityName(): string
    {
        return NewsletterContent::class;
    }

    public function getName(): string
    {
        return 'NewsletterContent';
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
            new AdminAction(AdminContext::AdminActionEdit,
                'Edit',
                'fas fa-edit m-1',
                false,
                AdminDashboardConfigurator::PageEdit
            ),
            new AdminAction(AdminContext::AdminActionNew,
                'Adauga',
                'fa-solid fa-plus m-1',
                true,
                AdminDashboardConfigurator::PageNew,
                'custom/adminNew.php'
            ),
            new AdminAction(AdminContext::AdminActionDelete,
                'Delete',
                'fa-solid fa-trash-can',
                false,
                AdminDashboardConfigurator::PageDelete
            )
        ];
    }


    protected function handleNewEntityPersist(array $dataFromForm): array
    {
        $request = $this->container->get(Request::class);

        $em = $this->container->get(EntityManager::class);

        $newsletterContent = new NewsletterContent();

        $files = $request->files->all();

        if (empty($dataFromForm["titlu"])) {
            return [
                "string" => "Titlul nu poate fi gol",
                "value" => false
            ];
        }

        $newsletterContent
            ->setTitlu(filter_var($dataFromForm["titlu"], FILTER_SANITIZE_FULL_SPECIAL_CHARS));

        if (empty($dataFromForm["content"])) {
            return [
                "string" => "Continutul nu poate fi gol",
                "value" => false
            ];
        }

        $newsletterContent
            ->setContent($dataFromForm["content"]);

        if (empty($dataFromForm["id_categorie"])
            || !filter_var($dataFromForm["id_categorie"], FILTER_VALIDATE_INT)) {
            return [
                "string" => "Categorie Invalida",
                "value" => false
            ];
        }

        $category = $em->find(NewsletterCategory::class, (int)$dataFromForm["id_categorie"]);

        if (!$category) {
            return [
                "string" => "Categorie Invalida",
                "value" => false
            ];
        }

        $newsletterContent
            ->setIdCategorie($category->getId());

        if (empty($files["img_prin"])) {
            return [
                "string" => "Imaginea nu poate fi goala",
                "value" => false
            ];
        }

        $mediaUpload = new MediaUploadService(
            $files["img_prin"]["size"],
            2000000,
            $files["img_prin"]["tmp_name"],
            'upload/newsletter/imgArticol',
            $files["img_prin"]["type"],
            ['image/jpg', 'image/jpeg','image/png'],
        );

        $result = $mediaUpload->uploadFile();

        if ($result !== 'success') {
            return [
                "string" => $result,
                "value" => false
            ];
        }

        $newsletterContent
            ->setImgPrin($mediaUpload->getNewFile())
            ->setCreatedAt((new \DateTime())->format('Y-m-d H:i:s'));

        $persist = $em->persist($newsletterContent);

        $em->flush($persist);

        return [
            "string" => "Ati creat articolul cu succes !",
            "value" => true
        ];
    }

    public function configureFields(AdminContext $context): array
    {
        $newsletterService = $context->getContainer()->get(NewsletterService::class);

        $categories = $newsletterService->getNewsletterCategory();

        $choices = [];

        if (!empty($categories)) {
            foreach ($categories as $category) {
                $choices[$category->getDenumire()] = $category->getId();
            }
        }

        $id = new AdminListingField('Id','id',NumberType::class, []);
        $category = new AdminListingField('Categorie', 'id_categorie',ChoiceType::class,
            [
                "required" => true,
                "choices" => $choices
            ]
        );

        $titlu = new AdminListingField('Titlu', 'titlu', TextType::class, [
            "required" => true
        ]);
        $imgPrin = new AdminListingField('Imagine JPG,PNG,JPEG', 'img_prin', UploadableFieldType::class, [
            "required" => true,
            "accept" => "image/*"
        ]);

        $content = new AdminListingField('Continut', 'content', TextareaType::class, [
            "class" => 'tinymce'
        ]);

        $createdAt = new AdminListingField('Creat la', 'created_at', DateType::class, []);
        $updatedAt = new AdminListingField('Editat la', 'updated_at', DateType::class, []);

        return match ($context->getCurrentAction()->getName()) {
            AdminContext::AdminActionShow, AdminContext::AdminActionIndex => [
                $id, $category, $titlu, $content,$imgPrin, $createdAt, $updatedAt
            ],
            AdminContext::AdminActionNew, AdminContext::AdminActionEdit => [
                $category, $titlu, $content, $imgPrin
            ],
        };
    }
}