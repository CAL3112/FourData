<?php

namespace App\Controller;

use App\Service\InseeApiService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class InseeController extends AbstractController
{
    private $inseeApiService;

    public function __construct(InseeApiService $inseeApiService)
    {
        $this->inseeApiService = $inseeApiService;
    }

    // #[Route('/token', name: 'token', methods:['GET'])]
    // public function getToken(): Response
    // {
    //     $token = $this->inseeApiService->getToken();

    //     if ($token) {
    //         return new Response($token);
    //     }

    //     return new Response('Failed to retrieve token', Response::HTTP_BAD_REQUEST);
    // }

    #[Route('/entreprise/search/{siret}', name: 'entreprise.search', methods:['GET'])]
    public function getCompanyInfo(string $siret): Response
    {
        try {
            $companyInfo = $this->inseeApiService->getCompanyInfoBySiret($siret);

            if ($companyInfo) {
                return $this->json($companyInfo);
            }

            return new Response('Company not found', Response::HTTP_NOT_FOUND);
        } catch (\Exception $e) {
            return new Response('Error: ' . $e->getMessage(), Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}
