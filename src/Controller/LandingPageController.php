<?php

namespace App\Controller;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Twig_Environment;

class LandingPageController
{
    /** @var Twig_Environment */
    private $twig;

    public function __construct(Twig_Environment $twig)
    {
        $this->twig = $twig;
    }

    /**
     * @Route("/", name="be.landing_page")
     */
    public function showLandingPageAction()
    {
        $body = $this->twig->render('landing-page.html.twig');

        return new Response($body);
    }
}
