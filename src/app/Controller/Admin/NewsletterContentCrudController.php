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

        $newsletter = $em->findBy(NewsletterContent::class,
            [
                "titlu" => filter_var($dataFromForm["titlu"], FILTER_SANITIZE_FULL_SPECIAL_CHARS)
            ]
        );

        if (!empty($newsletter)) {
            return [
                "string" => "Newsletter-ul exista deja",
                "value" => false
            ];
        }

        $newsletterContent
            ->setTitlu(filter_var($dataFromForm["titlu"], FILTER_SANITIZE_FULL_SPECIAL_CHARS));

        $newsletterContent
            ->setSlug(str_replace(' ', '-', strtolower(trim(preg_replace('/[^A-Za-z0-9]/',' ',$newsletterContent->getTitlu())))));

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

    public function handleEdit(array $data, AdminContext $context, EntityManager $em, object $entity)
    {
        $arrayOfChanges = [];

        $files = $context->getRequest()->files->all();

        if (!empty($data["titlu"]) && $data["titlu"] !== $entity->getTitlu()) {
            $arrayOfChanges["titlu"] = filter_var($data["titlu"], FILTER_SANITIZE_FULL_SPECIAL_CHARS);
            $arrayOfChanges["slug"] = str_replace(' ', '-', strtolower(trim(preg_replace('/[^A-Za-z0-9]/',' ',$arrayOfChanges["titlu"]))));
        }

        if (!empty($data["content"]) && $data["content"] !== $entity->getContent()) {
            $arrayOfChanges["content"] = $data["content"];
        }

        if (!empty($files["img_prin"])) {
            $mediaUpload = new MediaUploadService(
                $files["img_prin"]["size"],
                2000000,
                $files["img_prin"]["tmp_name"],
                'upload/newsletter/imgArticol',
                $files["img_prin"]["type"],
                ['image/jpg', 'image/jpeg','image/png'],
            );

            $resultOfUpload = $mediaUpload->uploadFile();

            if ($resultOfUpload === 'success') {
                $arrayOfChanges["img_prin"] = $mediaUpload->getNewFile();
                $mediaUpload->deleteFile(Kernel::getRootDirectory(). 'public/'.$entity->getImgPrin());
            }
        }

        if (!empty($arrayOfChanges)) {
            $newsletterService = $context->getContainer()->get(NewsletterService::class);

            $newsletterService->updateBulkNewsletterContent($entity, $arrayOfChanges);
        }

        $this->redirectToRoute('/admin', [
            "crudCon" => $context->getControllerName(),
            "action" => AdminContext::AdminActionEdit,
            "entityId" => $entity->getId(),
            "signature" => $context->createSignatureUrl($context->getAdmin(), AdminContext::AdminActionEdit)
        ]);
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

        $slug = new AdminListingField('Slug', 'slug', TextType::class, []);

        $imgPrin = new AdminListingField('Imagine JPG,PNG,JPEG', 'img_prin', UploadableFieldType::class, [
            "accept" => "image/*"
        ]);

        $content = new AdminListingField('Continut', 'content', TextareaType::class, [
            "class" => 'tinymce'
        ]);

        $createdAt = new AdminListingField('Creat la', 'created_at', DateType::class, []);
        $updatedAt = new AdminListingField('Editat la', 'updated_at', DateType::class, []);

        return match ($context->getCurrentAction()->getName()) {
            AdminContext::AdminActionShow, AdminContext::AdminActionIndex => [
                $id, $category, $titlu, $content,$imgPrin, $createdAt, $updatedAt, $slug
            ],
            AdminContext::AdminActionNew, AdminContext::AdminActionEdit => [
                $category, $titlu, $content, $imgPrin
            ],
        };
    }
}