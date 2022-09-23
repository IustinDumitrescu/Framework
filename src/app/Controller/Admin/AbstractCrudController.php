<?php
/**
 * Created by PhpStorm.
 * User: Iusti
 * Date: 06-May-22
 * Time: 8:59 PM
 */

namespace App\Controller\Admin;

use App\Admin\Action\AdminAction;
use App\Admin\AdminUtils;
use App\Admin\Config\AdminContext;
use App\Admin\Config\AdminDashboardConfigurator;
use App\Admin\Form\AdminEditType;
use App\Admin\Form\AdminNewType;
use App\Controller\AbstractController;
use App\Http\Request;
use App\Interfaces\SessionInterface;
use App\Kernel;
use App\Repository\EntityManager;
use App\Repository\Pagination;
use App\Repository\QueryBuilder;
use App\Service\MediaUploadService;
use App\Utils\Utils;
use JetBrains\PhpStorm\ArrayShape;
use JetBrains\PhpStorm\Pure;

abstract class AbstractCrudController extends AbstractController
{
    abstract public function getEntityName(): string;

    abstract public function getName(): string;


    public function initializeAdminLayout(SessionInterface $session, Request $request) :array
    {
        $adminTemplate = [];

        if (!$session->isStarted()
            || !$this->isAdmin($session)
            || $session->get('admin') === null
            || $request->cookie->get('a_d_u_s_r') === null) {
            $this->redirectToRoute('/admin/login');
        }

        $adminTemplate["user"] = $this->getUser($session);

        $adminTemplate["admin"] = $session->get('admin');

        return $adminTemplate;
    }

    #[Pure] public function configureActions(): array
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
                 AdminDashboardConfigurator::PageNew
             ),
            new AdminAction(AdminContext::AdminActionDelete,
                'Delete',
                'fa-solid fa-trash-can',
                false,
                AdminDashboardConfigurator::PageDelete
            )
        ];
    }

    public function index(AdminContext $context): array
    {
        $queryBuilder = $this->getIndexQueryBuilder($context);

        return (new Pagination($queryBuilder, $context->getRequest()))
            ->setItemsOnPage(20)
            ->execute();
    }

    /**
     * @param AdminContext $context
     * @return array
     */
    protected function getIndexQueryBuilder(AdminContext $context): QueryBuilder
    {
        $fields = [];

        foreach ($context->getFields() as $field) {
            $fields[] = $field->getId();
        }

        $em = $this->container->get(EntityManager::class);

        return $em->createQueryBuilder()
            ->setOperation('select')
            ->from($context->getController()->getEntityName())
            ->select($fields);
    }

    #[ArrayShape(["formBuilder" => "mixed", "flash" => "array|bool"])]
    public function new(AdminContext $context): array
    {
        $formAdminNew = $this->createForm(AdminNewType::class,
            [
                "name" => $context->getController()->getName(),
                "method" => "POST",
                "action" => $context->getCurrentRequest(),
                "id" => $context->getController()->getName()."FormNew",
                "options" => []
            ],
            [
                "data" => $context->getFields()
            ]
        );

        if ($formAdminNew->isSubmitted() && $formAdminNew->isValid()) {
            $dataFromForm = $formAdminNew->getData();

            $flash = $this->handleNewEntityPersist($dataFromForm);
        }

        return [
            "formBuilder" => $formAdminNew,
            "flash" => $flash ?? []
        ];

    }

    public function show(AdminContext $context): ?array
    {
        $entityId = $context->getRequestParams()["entityId"];

        $em = $this->container->get(EntityManager::class);

        $entityName = $context->getController()->getEntityName();

        $entity = $em->findBy($entityName, ["id" => $entityId]);

        if (empty($entity)) {
            return [];
        }

        $params = [];

        foreach ($context->getFields() as $field) {
            $get = "get".Utils::dashesToCamelCase($field->getId(), true);
            $params[$field->getName()] = $entity[0]->$get();
        }

        return $params;
    }


    abstract protected function handleNewEntityPersist(array $dataFromForm): array;

    public function edit(AdminContext $context)
    {
        $entityId = $context->getRequestParams()["entityId"];

        $name = $context->getController()->getEntityName();

        $em = $this->container->get(EntityManager::class);

        $entity = $em->find($name, $entityId);

        if (!$entity) {
            return [];
        }

        foreach ($context->getFields() as $field) {

            $get = 'get'.Utils::dashesToCamelCase($field->getId(), true);
            $field->setOption("value", $entity->$get());
        }

        $formAdminEdit = $this->createForm(AdminEditType::class, [
                "name" => $context->getController()->getName(),
                "method" => "POST",
                "action" => $context->getCurrentRequest(),
                "id" => $context->getController()->getName()."FormEdit",
                "options" => []
            ],
            [
                "data" => $context->getFields()
            ]
        );

        $flash = [];

        if ($formAdminEdit->isSubmitted() && $formAdminEdit->isValid()) {
            $data = $formAdminEdit->getData();

            $result = $this->handleEdit($data, $context, $em, $entity);

            if ($result) {
                foreach ($result as $key => $value) {
                    if ($key !== 'formBuilder') {
                        $flash[$key] = $value;
                    } else {
                        $formAdminEdit = $value;
                    }
                }
            }
        }

        return [
            "formBuilder" => $formAdminEdit,
            "flash" => $flash
        ];

    }

    protected function handleEdit(array $data, AdminContext $context, EntityManager $em, object $entity): ?array
    {
        $valuesToEdit = [];

        foreach ($data as $key => $value) {
            $method = 'get'.Utils::dashesToCamelCase($key , true);
            if (method_exists($entity, $method)) {
                if (is_bool($entity->$method()) && filter_var($value, FILTER_VALIDATE_BOOLEAN) !== $entity->$method()) {
                    $valuesToEdit[$key] = filter_var($value, FILTER_VALIDATE_BOOLEAN);
                } elseif (is_string($entity->$method()) && filter_var($value, FILTER_SANITIZE_FULL_SPECIAL_CHARS) !== $entity->$method()) {
                    $valuesToEdit[$key] = filter_var($value, FILTER_SANITIZE_FULL_SPECIAL_CHARS);
                } elseif (is_int($entity->$method()) && filter_var($value, FILTER_VALIDATE_INT) && (int)$value !== $entity->$method()) {
                    $valuesToEdit[$key] = (int)$value;
                }
            }
        }

        if (!empty($valuesToEdit)) {

            $queryBuilder = $em->createQueryBuilder()
                ->setOperation('update')
                ->update($context->getController()->getEntityName());

            foreach ($valuesToEdit as $keys => $value) {
                $queryBuilder->set($keys, $value);
            }

            $queryBuilder->set('updated_at', (new \DateTime('now'))->format('Y-m-d H:i:s'));

            $queryBuilder
                ->where("id = ". $entity->getId())
                ->getQuery()
                ->execute();

            foreach ($context->getFields() as $field) {
                if (isset($valuesToEdit[$field->getId()])) {
                    $field->setOption("value", $valuesToEdit[$field->getId()]);
                }
            }

            $formAdminEdit = $this->createForm(AdminEditType::class,
                [
                    "name" => $context->getController()->getName(),
                    "method" => "POST",
                    "action" => $context->getCurrentRequest(),
                    "id" => $context->getController()->getName()."FormEdit",
                    "options" => []
                ],
                [
                    "data" => $context->getFields()
                ]
            );

            $flash["formBuilder"] = $formAdminEdit;
            $flash["string"] = 'Update-ul a fost facut cu succes!';
            $flash["value"] = true;

            return $flash;
        }

        return null;
    }



    public function delete(AdminContext $context): void
    {
        $entityId = $context->getRequestParams()["entityId"];

        $em = $this->container->get(EntityManager::class);

        $entity = $em->find($context->getController()->getEntityName(), (int)$entityId);

        if (empty($entity)) {
          $this->redirectToRoute('/', [
                "crudCron" => $context->getControllerName(),
                "action" => AdminContext::AdminActionIndex,
                "signature" => $context->createSignatureUrl($context->getAdmin(), AdminContext::AdminActionIndex)
            ]);
        }

        $em->createQueryBuilder()
            ->setOperation('delete')
            ->from($context->getController()->getEntityName())
            ->where("id = {$entityId}")
            ->getQuery()
            ->execute();


        $reflection = new \ReflectionClass(get_class($entity));

        $properties = $reflection->getProperties();

        foreach ($properties as $property) {
            $get = 'get'. Utils::dashesToCamelCase($property->getName(),true);
            $valueOfFile = $entity->$get();
            if ($valueOfFile && (str_contains(strtolower($property->getName()), 'img')
                    || str_contains(strtolower($property->getName()), 'imag')
                    || str_contains(strtolower($property->getName()), 'file'))) {

                $root = Kernel::getRootDirectory() .'public/';

                (new MediaUploadService())->deleteFile($root. $valueOfFile);
            }
        }

        $this->redirectToRoute('/admin', [
            "crudCon" => $context->getControllerName(),
            "action" => AdminContext::AdminActionIndex,
            "signature" => $context->createSignatureUrl($context->getAdmin(), AdminContext::AdminActionIndex)
        ]);
    }

    abstract public function configureFields(AdminContext $context);




}