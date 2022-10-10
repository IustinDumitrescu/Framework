<?php

namespace App\Service;

use App\Entity\Newsletter\NewsletterCategory;
use App\Entity\Newsletter\NewsletterComments;
use App\Entity\Newsletter\NewsletterContent;
use App\Entity\UserEntity;
use App\Http\Request;
use App\Repository\Newsletter\NewsletterCategoryRepository;
use App\Repository\Newsletter\NewsletterCommentsRepository;
use App\Repository\Newsletter\NewsletterContentRepository;
use App\Repository\Pagination;
use App\Repository\UserRepository;
use JetBrains\PhpStorm\ArrayShape;

final class NewsletterService
{

    private NewsletterContentRepository $newsletterContentRepository;
    private static NewsletterCategoryRepository $newsletterCategoryRepository;
    private NewsletterCommentsRepository $commentsRepository;
    private UserRepository $userRepository;

    public function __construct(
        NewsletterContentRepository $newsletterContentRepository,
        NewsletterCategoryRepository $newsletterCategoryRepository,
        NewsletterCommentsRepository $commentsRepository,
        UserRepository $userRepository
    ) {

        $this->newsletterContentRepository = $newsletterContentRepository;
        self::$newsletterCategoryRepository = $newsletterCategoryRepository;
        $this->commentsRepository = $commentsRepository;
        $this->userRepository = $userRepository;
    }

    public static function getNewsletterCategory(): array
    {
        return self::$newsletterCategoryRepository->findAll(NewsletterCategory::class);
    }

    public function updateBulkNewsletterContent(NewsletterContent $newsletterContent, array $arrayOfChanges) : void
    {
        $this->newsletterContentRepository->updateBulkNewsletterContent($newsletterContent, $arrayOfChanges);
    }

    public function getSomeNewsletterOfEachCategory(): array
    {
        $categories = self::getNewsletterCategory();

        $arrayOfNewsletters = [];

        if (!empty($categories)) {
            $arrayOfIds = [];

            foreach ($categories as $category) {
                $arrayOfIds[] = $category->getId();
            }

           $newsletterContent = $this->newsletterContentRepository->getSomeNewsletterOfEachCategory($arrayOfIds);

            foreach ($newsletterContent as $newsletter) {
                $currentCategory = null;

                foreach ($categories as $category) {
                    if ($category->getId() === $newsletter->getIdCategorie()) {
                        $currentCategory = $category;
                    }
                }
                if (!isset($arrayOfNewsletters[$currentCategory->getId()]["category"])) {
                    $arrayOfNewsletters[$currentCategory->getId()]["category"] = $currentCategory;
                }
                $arrayOfNewsletters[$currentCategory->getId()]["content"][] = $newsletter;
            }
        }
        return $arrayOfNewsletters;
    }

    /**
     * @param NewsletterCategory $category
     * @param Request $request
     * @param string|null $query
     * @param int $page
     * @return array
     * @throws \Exception
     */
    public function getNewsletterByCategory(
        NewsletterCategory $category,
        Request $request,
        ?string $query,
        int $page = 0,
        bool $arrayForm = false
    ): array
    {
        $qb = $this->newsletterContentRepository->getNewsletterByCategory($category, $query);

        $newsletterContainer = (new Pagination($qb,$request))
            ->setItemsOnPage(12)
            ->executeByPage($page, $arrayForm);


        /* Pentru executii fara paginatii si order by*/

//        $count = count($newsletterContainer);
//
//        if (!empty($newsletterContainer)) {
//            for ($i = 0 ; $i < $count - 1; $i++) {
//                for ($j = 0; $j < $count - $i - 1; $j++) {
//                    if (!$arrayForm) {
//                        $date1 = new \DateTime($newsletterContainer[$j]->getCreatedAt());
//                        $date2 = new \DateTime($newsletterContainer[$j + 1]->getCreatedAt());
//                    } else {
//                        $date1 = new \DateTime($newsletterContainer[$j]["created_at"]);
//                        $date2 = new \DateTime($newsletterContainer[$j+1]["created_at"]);
//                    }
//                    if ($date1 < $date2) {
//                        $first = $newsletterContainer[$j];
//                        $newsletterContainer[$j] = $newsletterContainer[$j + 1];
//                        $newsletterContainer[$j + 1] = $first;
//                    }
//                }
//            }
//
//        }
        return $newsletterContainer;
    }

    #[ArrayShape(["comentariu" => "string", "imgPrin" => "\null|string", "date" => "\DateTime|null|string", "name" => "string"])]
    public function createComment(NewsletterContent $newsletterContent, UserEntity $user, string $comment): array
    {
         return $this->commentsRepository->createComment($newsletterContent, $user, $comment);
    }

    public function getAllCommentsOfNewsletter(
        NewsletterContent $content,
        Request $request,
        int $page = 0,
        bool $arrayForm = false
    ): array
    {
        $arrayOfComents = [];

        $qb = $this->commentsRepository->getQbOfNewsletterComments($content);

        $getAllComments = (new Pagination($qb, $request))
            ->setItemsOnPage(12)
            ->executeByPage($page);

        if (!empty($getAllComments)) {
            $arrayOfUser = [];

            foreach ($getAllComments as $key => $comment) {
                if (!$arrayForm) {
                    $arrayOfComents[$key]["comment"] = $comment;
                    if (!empty($arrayOfUser) && array_key_exists($comment->getIdUser(), $arrayOfUser)) {
                        $arrayOfComents[$key]["user"] = $arrayOfUser[$comment->getIdUser()];
                    } else {
                        $currentUser = $this->userRepository->find(UserEntity::class, $comment->getIdUser());
                        $arrayOfComents[$key]["user"] = $currentUser;
                        $arrayOfUser[$currentUser->getId()] = $currentUser;
                    }
                } else {
                    $arrayOfComents[$key]["comment"] = [
                        "comentariu" => $comment->getComentariu(),
                        "date" => $comment->getCreatedAt(),
                        "id" => $comment->getId()
                    ];

                    if (!empty($arrayOfUser) && array_key_exists($comment->getIdUser(), $arrayOfUser)) {
                        $arrayOfComents[$key]["user"] = [
                            "nume" => $arrayOfUser[$comment->getIdUser()]->getFirstName() . " " . $arrayOfUser[$comment->getIdUser()]->getLastName(),
                            "img" => $arrayOfUser[$comment->getIdUser()]->getImgPrin(),
                            "id" => $arrayOfUser[$comment->getIdUser()]->getId()
                        ];
                    } else {
                        $currentUser = $this->userRepository->find(UserEntity::class, $comment->getIdUser());
                        $arrayOfComents[$key]["user"] = [
                            "nume" => $currentUser->getFirstName() . " " . $currentUser->getLastName(),
                            "img" => $currentUser->getImgPrin(),
                            "id" => $currentUser->getId()
                        ];
                        $arrayOfUser[$currentUser->getId()] = $currentUser;
                    }

                }
            }
        }

        return $arrayOfComents;
    }

    public function deleteComentariu(NewsletterComments $comentariu): void
    {
        $this->commentsRepository->delete($comentariu);
    }



}