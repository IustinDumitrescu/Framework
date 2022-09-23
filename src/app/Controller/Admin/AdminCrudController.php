<?php
/**
 * Created by PhpStorm.
 * User: Iusti
 * Date: 28-May-22
 * Time: 6:25 PM
 */

namespace App\Controller\Admin;


use App\Admin\Action\AdminAction;
use App\Admin\Config\AdminContext;
use App\Admin\Config\AdminDashboardConfigurator;
use App\Admin\Fields\AdminListingField;
use App\Entity\AdminEntity;
use App\Entity\UserEntity;
use App\Form\InputTypes\CheckBoxType;
use App\Form\InputTypes\ChoiceType;
use App\Form\InputTypes\DateType;
use App\Form\InputTypes\EmailType;
use App\Form\InputTypes\NumberType;
use App\Http\Request;
use App\Interfaces\SessionInterface;
use App\Repository\EntityManager;
use App\Repository\QueryBuilder;
use JetBrains\PhpStorm\Pure;
use JsonException;

class AdminCrudController extends AbstractCrudController
{
    public function getEntityName(): string
    {
        return AdminEntity::class;
    }

    public function getName(): string
    {
        return "Admini";
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

    #[Pure] public function configureFields(AdminContext $context): array
    {
        $id = new AdminListingField('Id','id',NumberType::class, []);
        $userId = new AdminListingField('Utilizator','user_id', EmailType::class, [
            "required" => "required"
        ]);
        $rol = new AdminListingField('Rol', 'rol', ChoiceType::class, [
            "required" => "required",
            "choices" => [
                ucfirst(AdminEntity::RolNewsletter) => AdminEntity::RolNewsletter,
                ucfirst(AdminEntity::RolVanzator) => AdminEntity::RolVanzator
            ]
        ]);
        $superAdmin = new AdminListingField('Super Admin', 'super_admin', CheckBoxType::class, []);
        $createdAt = new AdminListingField('Creat la', 'created_at', DateType::class, []);
        $updatedAt = new AdminListingField('Ultima editare', 'updated_at', DateType::class, []);

        return match ($context->getCurrentAction()->getName()) {
            AdminContext::AdminActionShow, AdminContext::AdminActionIndex => [$id, $userId, $rol, $superAdmin, $createdAt, $updatedAt],
            AdminContext::AdminActionNew => [$userId, $rol, $superAdmin],
            AdminContext::AdminActionEdit => [$rol, $superAdmin],
            default => []
        };
    }

    /**
     * @param AdminContext $context
     * @return QueryBuilder
     */
    public function getIndexQueryBuilder(AdminContext $context): QueryBuilder
    {
        $admin = $context->getAdmin();

        return parent::getIndexQueryBuilder($context)
            ->where("admin.id != {$admin->getId()}");
    }


    /**
     * @param array $dataFromForm
     * @return array
     */
    protected function handleNewEntityPersist(array $dataFromForm): array
    {
        $email = $dataFromForm["user_id"];

        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            return [
                "string" => "Email-ul este invalid!",
                "value" => false
            ];
        }

        $em = $this->container->get(EntityManager::class);

        $user = $em->findBy(UserEntity::class, ["email" => $email]);

        if (empty($user)) {
            return  [
                "string" => "Nu exista user cu acest email",
                "value" => false
            ];
        }

        $admin = $em->findBy(AdminEntity::class, ["user_id" => $user[0]->getId()]);

        if (!empty($admin)) {
            return  [
                "string" => "User-ul este deja admin",
                "value" => false
            ];
        }

        $rol = $dataFromForm["rol"];

        if (!in_array($rol, AdminEntity::Roluri, true)) {
            return  [
                "string" => "Rolul nu exista",
                "value" => false
            ];
        }

        $superAdmin = $dataFromForm["super_admin"] === "true";

        $adminEntity = new AdminEntity();

        $adminEntity
            ->setRol($rol)
            ->setUserId($user[0]->getId())
            ->setSuperAdmin($superAdmin)
            ->setCreatedAt((new \DateTime('now'))->format('Y-m-d H:i:s'));

        $persist = $em->persist($adminEntity);

        $em->flush($persist);

        return  [
            "string" => "Adminul a fost creat cu success!",
            "value" => true
        ];

    }

    /**
     * @param Request $request
     * @param SessionInterface $session
     * @return string
     * @throws JsonException
     */
    public function ajaxUserAutocomplete(
        Request $request,
        SessionInterface $session
    ) : string
    {
        if ($this->isXmlHttpRequest($request) && $request->isMethod() === 'GET') {
            $data = $request->query->all();

            if (!empty($data)
                && $this->isLoggedIn($session, $request) && $this->isAdmin($session)) {
                $user = $this->getUser($session);

                $queryString = isset($data["param"])
                    ? filter_var($data["param"], FILTER_SANITIZE_SPECIAL_CHARS) : null;

                if ($queryString) {
                     $em = $this->container->get(EntityManager::class);

                     $emailsOfUser = $em->createQueryBuilder()
                         ->setOperation('select')
                         ->from(UserEntity::class)
                         ->select(["email"])
                         ->where("user.email LIKE '%$queryString%'")
                         ->andWhere("user.id != {$user->getId()}")
                         ->getQuery()
                         ->getNormalResult();

                     $response = [];

                     if (empty($emailsOfUser)) {
                         return json_encode(['Nimic'], JSON_THROW_ON_ERROR);
                     }

                     foreach ($emailsOfUser as $email) {
                         $response[] = $email["email"];
                     }

                     return json_encode($response, JSON_THROW_ON_ERROR);
                }
            }
        }

        die;
    }

}