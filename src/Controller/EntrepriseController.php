<?php

namespace App\Controller;

use App\Entity\Entreprise;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class EntrepriseController extends AbstractController
{
    #[Route('/entreprise', name: 'entreprises', methods:['GET'])]
    public function getEntreprises(EntityManagerInterface $entityManager): JsonResponse
    {
        $entreprises = $entityManager->getRepository(Entreprise::class)->findBy(['deletedAt' => null]);
        return $this->json($entreprises);
    }

    #[Route('/entreprise/update/{id}', name: 'entreprise.update', methods:['PUT'])]
    public function updateEntreprise(Request $request, EntityManagerInterface $entityManager, $id): JsonResponse
    {
        $entreprise = $entityManager->getRepository(Entreprise::class)->find($id);

        if(!$entreprise){
            return $this->json(['message' => 'Pas d\'entreprise trouvée'], Response::HTTP_NOT_FOUND);
        }

        $data = json_decode($request->getContent(), true);

        $entreprise->setSiret($data['siret']);
        $entreprise->setRaisonSociale($data['raisonSociale']);
        $entreprise->setVille($data['ville']);
        $entreprise->setCp($data['cp']);
        $entreprise->setAdresse($data['adresse']);
        $entreprise->setSiren($data['siren']);
        $entreprise->setUpdatedAt(new \DateTimeImmutable());

        $entityManager->flush();

        return $this->json($entreprise);
    }

    #[Route('/entreprise/delete/{id}', name: 'entreprise.delete', methods:['DELETE'])]
    public function deleteEntreprise(EntityManagerInterface $entityManager, $id): JsonResponse
    {
        $entreprise = $entityManager->getRepository(Entreprise::class)->find($id);

        if (!$entreprise) {
            return $this->json(['message' => 'Pas d\'entreprise trouvée'], Response::HTTP_NOT_FOUND);
        }

        $entreprise->setDeletedAt(new \DateTimeImmutable());
        $entityManager->flush();

        return $this->json(['message' => 'Suppression réussie']);
    }

    #[Route('/entreprise/save', name: 'entreprise.save', methods:['POST'])]
    public function saveEntreprise(Request $request, EntityManagerInterface $entityManager): JsonResponse
    {
        $data = json_decode($request->getContent(), true);

        // Crée une nouvelle instance de l'entité Entreprise (ou autre entité correspondante)
        $entreprise = new Entreprise();
        $entreprise->setSiret($data['siret']);
        $entreprise->setRaisonSociale($data['raisonSociale']);
        $entreprise->setVille($data['ville']);
        $entreprise->setCp($data['codePostal']);
        $entreprise->setAdresse($data['adresse']);
        $entreprise->setSiren($data['siren']);
        $entreprise->setCreatedAt(new \DateTimeImmutable());

        // Persist et flush pour enregistrer en base
        $entityManager->persist($entreprise);
        $entityManager->flush();

        return new JsonResponse(['message' => 'Entreprise enregistrée avec succès'], 201);
    }
}
