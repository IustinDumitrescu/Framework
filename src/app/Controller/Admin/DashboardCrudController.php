<?php
/**
 * Created by PhpStorm.
 * User: Iusti
 * Date: 06-May-22
 * Time: 8:58 PM
 */

namespace App\Controller\Admin;



use App\Admin\Config\AdminContext;
use App\Admin\Fields\AdminDashboardField;
use App\Admin\Config\AdminDashboardConfigurator;
use App\Admin\Template\AdminTemplate;
use App\Entity\AdminEntity;
use App\Entity\UserEntity;
use App\Http\Request;
use App\Interfaces\SessionInterface;


class DashboardCrudController extends AbstractCrudController
{

     public const rootUrl = '/admin';


    /**
     * @param SessionInterface $session
     * @param Request $request
     * @return string
     */
    public function admin(SessionInterface $session, Request $request): string
    {
        $adminTemplate = $this->initializeAdminLayout($session, $request);

        $template = $this->configureDashboard($adminTemplate["user"], $adminTemplate["admin"], $request);

        if (!$template) {
            $this->redirect('/admin');
        }

        $adminTemplate["templateAdmin"] = $template;

        return $this->render('/admin/homeLayout', $adminTemplate );
    }

    /**
     * @param UserEntity $user
     * @param AdminEntity $admin
     * @param Request $request
     * @return AdminTemplate|null
     */
    private function configureDashboard(
        UserEntity $user,
        AdminEntity $admin,
        Request $request
    ): ?AdminTemplate
    {
        return AdminDashboardConfigurator::configureForThisUser($user, $admin, $request, $this->container)
            ->configureItems(
                AdminDashboardField::new('Admini',AdminCrudController::class, [
                    "SUPER-ADMIN"
                ], 'fa-solid fa-users')
                    ->new('Newsletter Category', NewsletterCategoryCrudController::class, [
                        "SUPER-ADMIN",
                        "newsletter"
                    ], 'fa fa-circle-info')
                    ->new('Newsletter Content',NewsletterContentCrudController::class,
                        [
                            "SUPER-ADMIN",
                            "newsletter"
                        ], 'fa fa-bars')
            )->getConfiguration();

    }

    protected function handleNewEntityPersist(array $dataFromForm): array
    {
        return  [];
    }


    public function getEntityName(): string
    {
        return '';
    }

    public function getName(): string
    {
        return '';
    }

    public function configureFields(AdminContext $context): array
    {
        return [];
    }
}