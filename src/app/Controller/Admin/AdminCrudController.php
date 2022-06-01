<?php
/**
 * Created by PhpStorm.
 * User: Iusti
 * Date: 28-May-22
 * Time: 6:25 PM
 */

namespace App\Controller\Admin;


use App\Admin\Config\AdminContext;
use App\Admin\Fields\AdminListingField;
use App\Entity\AdminEntity;

class AdminCrudController extends AbstractCrudController
{

    public function getEntityName(): string
    {
        return AdminEntity::class;
    }

    public function configureFields(AdminContext $context)
    {
        $id = new AdminListingField('Id','id','number', []);
        $userId = new AdminListingField('Utilizator','userId', 'number', []);
        $rol = new AdminListingField('Rol', 'rol', 'text', []);
        $superAdmin = new AdminListingField('Super Admin', 'super_admin', 'checkbox', []);
        $createdAt = new AdminListingField('Creat la', 'created_at', 'date', []);

        switch ($context->getCurrentAction()->getName()) {
            case AdminContext::AdminActionIndex:
                return [$id, $userId, $rol, $superAdmin, $createdAt ];
                break;
            case  AdminContext::AdminActionNew:
                return [];
                break;

        }
    }

}