<?php

namespace App\Controller;

use App\Entity\Newsletter\NewsletterCategory;
use App\Entity\Newsletter\NewsletterContent;
use App\Form\Newsletter\NewsletterCommentType;
use App\Form\Newsletter\NewsletterSearchType;
use App\Http\Request;
use App\Interfaces\SessionInterface;
use App\Repository\EntityManager;
use App\Service\NewsletterService;

class NewsletterController extends AbstractController
{
    /**
     * @param SessionInterface $session
     * @param Request $request
     * @param NewsletterService $newsletterService
     * @return bool|string
     */
    public function newsletterHome(
        SessionInterface $session,
        Request $request,
        NewsletterService $newsletterService
    ): bool|string
    {
        $templateVars = $this->initializeLayout($session, $request,null);

        $templateVars["currentUrl"] = $request->getHost();

        $templateVars["newsletterContainer"] = $newsletterService->getSomeNewsletterOfEachCategory();

        return $this->render('newsletter-home', $templateVars);
    }

    /**
     * @param SessionInterface $session
     * @param Request $request
     * @param NewsletterService $newsletterService
     * @param string $slugCategorie
     * @return bool|string
     */
    public function newsletterOfCattegory(
        SessionInterface $session,
        Request $request,
        NewsletterService $newsletterService,
        string $slugCategorie
    ): bool|string
    {
        $templateVars = $this->initializeLayout($session, $request,null);

        $em = $this->container->get(EntityManager::class);

        $queryRough = $request->query->get('query');

        $query = !empty($queryRough)
            ? strtolower(trim(str_replace(' ', '',filter_var($queryRough, FILTER_SANITIZE_FULL_SPECIAL_CHARS))))
            : null;

        if (empty($slugCategorie)) {
             $this->redirectToRoute('/newsletter', []);
        }

        $templateVars["category"] = $em->findBy(NewsletterCategory::class,
            [
                "slug" => filter_var($slugCategorie, FILTER_SANITIZE_FULL_SPECIAL_CHARS)
            ]
        );

        if (empty($templateVars["category"])) {
            $this->redirectToRoute('/newsletter', []);
        }

        $formNewsletterSearch = $this->createForm(NewsletterSearchType::class,
            [
                'name' => 'Newsletter Form',
                'method' => 'GET',
                'action' => '',
                'id' => 'newsletterForm',
                "options" => [
                    "class" => 'd-flex',
                    "style" => 'justify-content: space-around;'
                ]
            ]
        );

        $templateVars["formNewsletterSearch"] = $formNewsletterSearch->createView();

        $templateVars["apiKey"] = $this->createApiToken($session, 'apiNewsletterCategory');

        $templateVars["newsletterContainer"] = $newsletterService->getNewsletterByCategory(
            $templateVars["category"][0],
            $request,
            $query
        );
        return $this->render('articole-categorie', $templateVars);
    }


    /**
     * @param Request $request
     * @param NewsletterService $newsletterService
     * @param string $slugCategorie
     * @return false|string|void
     * @throws \JsonException
     */
    public function getNewsletterOfCategoryPaginated(
        Request $request,
        NewsletterService $newsletterService,
        SessionInterface $session,
        string $slugCategorie
    )
    {
        if ($this->isXmlHttpRequest($request) && $request->isMethod() === 'GET') {

            $pageRough = $request->query->get('pageNr');

            $queryRough = $request->query->get('query');

            $key = $request->query->get('key');

            $sessionKey = $session->get('apiNewsletterCategory');

            if (!empty($key) && $key === $sessionKey) {
                $query = !empty($queryRough)
                    ? strtolower(trim(str_replace(' ', '', filter_var($queryRough, FILTER_SANITIZE_FULL_SPECIAL_CHARS))))
                    : null;

                $em = $this->container->get(EntityManager::class);

                $category = $em->findBy(NewsletterCategory::class, [
                    "slug" => filter_var($slugCategorie, FILTER_SANITIZE_FULL_SPECIAL_CHARS)
                ]);

                if (!empty($category) && !empty($pageRough) && filter_var($pageRough, FILTER_VALIDATE_INT)) {
                    $page = (int)$pageRough;
                    $result = $newsletterService->getNewsletterByCategory($category[0], $request, $query, $page, true);
                    return json_encode($result, JSON_THROW_ON_ERROR);
                }
            }
        }

        die();
    }



    /**
     * @param SessionInterface $session
     * @param Request $request
     * @param NewsletterService $newsletterService
     * @param string $slugCategorie
     * @param string $slugNewsletter
     * @return bool|string
     */
    public function newsletterShow(
        SessionInterface $session,
        Request $request,
        NewsletterService $newsletterService,
        string $slugCategorie,
        string $slugNewsletter
    ): bool|string
    {
        $templateVars = $this->initializeLayout($session, $request,null);

        $em = $this->container->get(EntityManager::class);

        $templateVars["category"] = $em->findBy(NewsletterCategory::class, [
            "slug" => filter_var($slugCategorie, FILTER_SANITIZE_FULL_SPECIAL_CHARS)
        ]);

        if (empty($templateVars["category"])) {
            $this->redirectToRoute('/newsletter', []);
        }

        $templateVars["newsletter"] = $em->findBy(NewsletterContent::class, [
            "slug" => filter_var($slugNewsletter, FILTER_SANITIZE_FULL_SPECIAL_CHARS),
            "id_categorie" => $templateVars["category"][0]->getId()
        ]);

        if (empty($templateVars["newsletter"])) {
            $this->redirectToRoute(sprintf('/newsletter/%s', $templateVars["category"][0]->getSlug()), []);
        }

        if ($templateVars["logged"]) {
            $formComment = $this->createForm(NewsletterCommentType::class,
                [
                    'name' => 'Comment Form',
                    'method' => 'POST',
                    'action' => '',
                    'id' => 'newsletterForm',
                ]
            );
            $templateVars["formComment"] = $formComment->createView();
        }

        $templateVars["apiKey"] = $this->createApiToken($session, 'apiCommentKey');

        $templateVars["comments"] = $newsletterService->getAllCommentsOfNewsletter(
            $templateVars["newsletter"][0],
            $request
        );

        return $this->render('newsletter-show', $templateVars);
    }

    /**
     * @param SessionInterface $session
     * @param Request $request
     * @param NewsletterService $newsletterService
     * @param string $slugCategorie
     * @param string $slugNewsletter
     * @return false|string|void
     * @throws \JsonException
     */
    public function createComment(
        SessionInterface $session,
        Request $request,
        NewsletterService $newsletterService,
        string $slugCategorie,
        string $slugNewsletter
    )
    {
        if ($this->isLoggedIn($session, $request) && $this->isXmlHttpRequest($request) && $request->isMethod() === 'POST') {
            $em = $this->container->get(EntityManager::class);

            $data = $request->request->getJsonDataFromAjaxRequest();

            $category = $em->findBy(NewsletterCategory::class, [
                "slug" => filter_var($slugCategorie, FILTER_SANITIZE_FULL_SPECIAL_CHARS)
            ]);

            if (!empty($data["comentariu"]) && !empty($category)) {
                $newsletter = $em->findBy(NewsletterContent::class, [
                    "slug" => filter_var($slugNewsletter, FILTER_SANITIZE_FULL_SPECIAL_CHARS),
                    "id_categorie" => $category[0]->getId()
                ]);

                if (!empty($newsletter)) {
                    $user = $this->getUser($session);
                    $comment = filter_var($data["comentariu"], FILTER_SANITIZE_FULL_SPECIAL_CHARS);
                    $result = $newsletterService->createComment($newsletter[0], $user, $comment);
                    return json_encode($result, JSON_THROW_ON_ERROR);
                }
            }
        }

        die();
    }


    /**
     * @param SessionInterface $session
     * @param Request $request
     * @param NewsletterService $newsletterService
     * @param string $slugCategorie
     * @param string $slugNewsletter
     * @return false|string|void
     * @throws \JsonException
     */
    public function ajaxGetDataOfComments(
        SessionInterface $session,
        Request $request,
        NewsletterService $newsletterService,
        string $slugCategorie,
        string $slugNewsletter
    )
    {
        if ($this->isXmlHttpRequest($request) && $request->isMethod() === 'GET') {
            $pageRough = $request->query->get('pageNr');
            $key = $request->query->get('key');
            $sessionKey = $session->get('apiCommentKey');

            if (!empty($key) && $key === $sessionKey) {
                $em = $this->container->get(EntityManager::class);

                $category = $em->findBy(NewsletterCategory::class, [
                    "slug" => filter_var($slugCategorie, FILTER_SANITIZE_FULL_SPECIAL_CHARS)
                ]);

                if (!empty($category) && !empty($pageRough) && filter_var($pageRough, FILTER_VALIDATE_INT)) {
                    $newsletter = $em->findBy(NewsletterContent::class, [
                        "slug" => filter_var($slugNewsletter, FILTER_SANITIZE_FULL_SPECIAL_CHARS),
                        "id_categorie" => $category[0]->getId()
                    ]);

                    if (!empty($newsletter)) {
                        $page = (int)$pageRough;
                        $result = $newsletterService->getAllCommentsOfNewsletter($newsletter[0], $request, $page, true);
                        return json_encode($result, JSON_THROW_ON_ERROR);
                    }
                }
            }

            die();
        }

        die();
    }


}