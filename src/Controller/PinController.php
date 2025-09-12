<?php

namespace App\Controller;

use App\Entity\Pin;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/pin')]
class PinController extends AbstractController
{
    #[Route('/', name: 'app_pin_index')]
    public function index(EntityManagerInterface $em): Response
    {
        // Vérifie que l'utilisateur est connecté
        $this->denyAccessUnlessGranted('ROLE_USER');

        $pins = $em->getRepository(Pin::class)->findAll();

        return $this->render('pin/index.html.twig', [
            'pins' => $pins,
        ]);
    }

    #[Route('/create', name: 'app_pin_create')]
    public function create(Request $request, EntityManagerInterface $em): Response
    {
        $this->denyAccessUnlessGranted('ROLE_USER');

        $pin = new Pin();

        if ($request->isMethod('POST')) {
            $pin->setTitle($request->request->get('title'));
            $pin->setDescription($request->request->get('description'));
            $pin->setCreatedAt(new \DateTimeImmutable());
            $pin->setUpdatedAt(new \DateTime());
            $pin->setUser($this->getUser());

            // Upload image
            $imageFile = $request->files->get('imageFile');
            if ($imageFile) {
                $newFilename = uniqid().'.'.$imageFile->guessExtension();
                $imageFile->move(
                    $this->getParameter('kernel.project_dir').'/public/uploads/pins',
                    $newFilename
                );
                $pin->setImageName($newFilename);
            }

            $em->persist($pin);
            $em->flush();

            $this->addFlash('success', 'Pin créé avec succès ✅');

            return $this->redirectToRoute('app_pin_index');
        }

        return $this->render('pin/create.html.twig', [
            'pin' => $pin,
        ]);
    }

    #[Route('/{id}', name: 'app_pin_show')]
    public function show(Pin $pin): Response
    {
        $this->denyAccessUnlessGranted('ROLE_USER');

        return $this->render('pin/show.html.twig', [
            'pin' => $pin,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_pin_edit')]
    public function edit(Request $request, Pin $pin, EntityManagerInterface $em): Response
    {
        $this->denyAccessUnlessGranted('ROLE_USER');

        if ($pin->getUser() !== $this->getUser()) {
            throw $this->createAccessDeniedException();
        }

        if ($request->isMethod('POST')) {
            $pin->setTitle($request->request->get('title'));
            $pin->setDescription($request->request->get('description'));
            $pin->setUpdatedAt(new \DateTime());

            $imageFile = $request->files->get('imageFile');
            if ($imageFile) {
                $newFilename = uniqid().'.'.$imageFile->guessExtension();
                $imageFile->move(
                    $this->getParameter('kernel.project_dir').'/public/uploads/pins',
                    $newFilename
                );
                $pin->setImageName($newFilename);
            }

            $em->flush();

            $this->addFlash('success', 'Pin modifié avec succès ✏️');
            return $this->redirectToRoute('app_pin_index');
        }

        return $this->render('pin/edit.html.twig', [
            'pin' => $pin,
        ]);
    }

    #[Route('/{id}/delete', name: 'app_pin_delete', methods: ['POST'])]
    public function delete(Request $request, Pin $pin, EntityManagerInterface $em): Response
    {
        $this->denyAccessUnlessGranted('ROLE_USER');

        if ($pin->getUser() !== $this->getUser()) {
            throw $this->createAccessDeniedException();
        }

        if ($this->isCsrfTokenValid('delete'.$pin->getId(), $request->request->get('_token'))) {
            $em->remove($pin);
            $em->flush();
            $this->addFlash('danger', 'Pin supprimé ❌');
        }

        return $this->redirectToRoute('app_pin_index');
    }
}
