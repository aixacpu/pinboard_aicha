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
        $pin = new Pin();
        $pin->setCreatedAt(new \DateTimeImmutable());
        $pin->setUpdatedAt(new \DateTime());

        $form = $this->createForm(PinType::class, $pin);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em->persist($pin);
            $em->flush();
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
        $form = $this->createForm(PinType::class, $pin);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $pin->setUpdatedAt(new \DateTime());
            $em->flush();
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
        $em->remove($pin);
        $em->flush();
        return $this->redirectToRoute('app_pin_index');
    }
}
