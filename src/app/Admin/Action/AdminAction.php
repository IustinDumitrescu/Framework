<?php
/**
 * Created by PhpStorm.
 * User: Iusti
 * Date: 28-May-22
 * Time: 9:45 PM
 */

namespace App\Admin\Action;


use App\Admin\Config\AdminDashboardConfigurator;

final class AdminAction
{

    private $name;

    private $label;

    private $icon;

    private $global;

    private $page;

    public function __construct(
        string $name,
        string $label = '',
        string $icon = '',
        bool $global = false,
        string $page = AdminDashboardConfigurator::PageIndex
    )
    {
        $this->name = $name;
        $this->label = $label;
        $this->icon = $icon;
        $this->global = $global;
        $this->page = $page;
    }


    /**
     * @return mixed
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @return mixed
     */
    public function getLabel(): string
    {
        return $this->label;
    }

    /**
     * @return mixed
     */
    public function getIcon(): string
    {
        return $this->icon;
    }

    /**
     * @return mixed
     */
    public function getGlobal(): bool
    {
        return $this->global;
    }

    public function getPage(): string
    {
        return $this->page;
    }


}