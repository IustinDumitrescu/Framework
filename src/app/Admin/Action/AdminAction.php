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

    private string $name;

    private string $label;

    private string $icon;

    private bool $global;

    private string $page;

    private ?string $template;

    public function __construct(
        string $name,
        string $label = '',
        string $icon = '',
        bool $global = false,
        string $page = AdminDashboardConfigurator::PageIndex,
        ?string $template = null
    )
    {
        $this->name = $name;
        $this->label = $label;
        $this->icon = $icon;
        $this->global = $global;
        $this->page = $page;
        $this->template = $template;
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

    public function getTemplate(): ?string
    {
        return $this->template;
    }


}