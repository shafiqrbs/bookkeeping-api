<?php


namespace App\Controller;


use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class DefaultController extends AbstractController
{
    /**
     * @Route("/")
     * @return JsonResponse
     */
    public function index(): JsonResponse
    {
        return $this->json([
            'status' => Response::HTTP_OK,
            'message' => 'Bookkeeping',
        ]);
    }

}