<?php


namespace App\Controller;


use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;

class DefaultController extends AbstractController
{
    public function index(): Response
    {
        return $this->render('index.html.twig');
    }

    public function hello($name)
    {
        return new Response("Hello $name!");
    }
}