<?php

namespace App\Service;

use App\Entity\Newsletter\NewsletterCategory;
use App\Repository\Newsletter\NewsletterCategoryRepository;
use App\Repository\Newsletter\NewsletterContentRepository;

final class NewsletterService
{


    private NewsletterContentRepository $newsletterContentRepository;
    private static NewsletterCategoryRepository $newsletterCategoryRepository;

    public function __construct(
        NewsletterContentRepository $newsletterContentRepository,
        NewsletterCategoryRepository $newsletterCategoryRepository
    ) {

        $this->newsletterContentRepository = $newsletterContentRepository;
        self::$newsletterCategoryRepository = $newsletterCategoryRepository;
    }

    public static function getNewsletterCategory(): array
    {
        return self::$newsletterCategoryRepository->findAll(NewsletterCategory::class);
    }


}