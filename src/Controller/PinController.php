<?php

namespace App\Controller;

use App\Entity\Pin;
use App\Form\PinType;
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
        $pins = $em->getRepository(Pin::class)->findAll();

        return $this->render('pin/index.html.twig', [
            'pins' => $pins,
        ]);
    }

    #[Route('/create', name: 'app_pin_create')]
    public function create(Request $request, EntityManagerInterface $em): Response
    {
        // Vérifier si l’utilisateur est connecté
        $this->denyAccessUnlessGranted('ROLE_USER');

        $pin = new Pin();
        $pin->setCreatedAt(new \DateTimeImmutable());
        $pin->setUpdatedAt(new \DateTime());
        $pin->setUser($this->getUser());

        $form = $this->createForm(PinType::class, $pin);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em->persist($pin);
            $em->flush();

            $this->addFlash('success', 'Pin créé avec succès');
            return $this->redirectToRoute('app_pin_index');
        }

        return $this->render('pin/create.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    #[Route('/{id}', name: 'app_pin_show')]
    public function show(Pin $pin): Response
    {
        return $this->render('pin/show.html.twig', [
            'pin' => $pin,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_pin_edit')]
    public function edit(Request $request, Pin $pin, EntityManagerInterface $em): Response
    {
        $this->denyAccessUnlessGranted('ROLE_USER');

        // Sécurité : seul l’auteur peut modifier
        if ($pin->getUser() !== $this->getUser()) {
            throw $this->createAccessDeniedException("Vous ne pouvez modifier que vos propres pins !");
        }

        $form = $this->createForm(PinType::class, $pin);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $pin->setUpdatedAt(new \DateTime());
            $em->flush();

            $this->addFlash('success', 'Pin modifié avec succès');
            return $this->redirectToRoute('app_pin_index');
        }

        return $this->render('pin/edit.html.twig', [
            'form' => $form->createView(),
            'pin' => $pin,
        ]);
    }

    #[Route('/{id}/delete', name: 'app_pin_delete')]
    public function delete(Pin $pin, EntityManagerInterface $em): Response
    {
        $this->denyAccessUnlessGranted('ROLE_USER');

        // Sécurité : seul l’auteur peut supprimer
        if ($pin->getUser() !== $this->getUser()) {
            throw $this->createAccessDeniedException("Vous ne pouvez supprimer que vos propres pins !");
        }

        $em->remove($pin);
        $em->flush();

        $this->addFlash('danger', 'Pin supprimé');
        return $this->redirectToRoute('app_pin_index');
    }
}
