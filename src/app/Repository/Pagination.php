<?php

namespace App\Repository;

use App\Http\Request;

class Pagination
{
    private QueryBuilder $queryBuilder;

    private Request $request;

    private int $nrOfItemsOfPage;

    public function __construct(QueryBuilder $queryBuilder, Request $request)
    {
        $this->queryBuilder = $queryBuilder;
        $this->request = $request;
    }

    public function setItemsOnPage(int $itemsOfPage): self
    {
        $this->nrOfItemsOfPage = $itemsOfPage;
        return $this;
    }

    public function executeByPage(int $page = 0, bool $arrayForm = false): array
    {
        $this->queryBuilder->getQuery();

        $query = $this->queryBuilder->getDql();

        $offset = $page > 0 ? $page * $this->nrOfItemsOfPage : 0;

        $query.= " LIMIT {$this->nrOfItemsOfPage} OFFSET {$offset}";

        $this->queryBuilder->setDql($query);

        if (!$arrayForm) {
            return $this->queryBuilder->getResult();
        }

        return $this->queryBuilder->getNormalResult();
    }


    public function execute(): array
    {
        $pageNr = $this->request->query->get('page');

        if (empty($pageNr)) {
            $pageNr = 0;
        } else {
            $pageNr *= $this->nrOfItemsOfPage;
        }

        $this->queryBuilder->getQuery();

        $query = $this->queryBuilder->getDql();

        $query.= " LIMIT {$this->nrOfItemsOfPage} OFFSET {$pageNr} ";

        $this->queryBuilder->setDql($query);

        return $this->queryBuilder->getResult();
    }





}