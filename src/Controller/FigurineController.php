<?php

namespace App\Controller;

use App\Entity\Figurine;
use App\Form\FigurineType;
use App\Repository\FigurineRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/figurine')]
class FigurineController extends AbstractController
{
    #[Route('/', name: 'figurine_index', methods: ['GET'])]
    public function index(FigurineRepository $figurineRepository): Response
    {
        return $this->render('figurine/index.html.twig', [
            'figurines' => $figurineRepository->findAll(),
        ]);
    }

    #[Route('/new', name: 'figurine_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $em): Response
    {
        $figurine = new Figurine();
        $form = $this->createForm(FigurineType::class, $figurine);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $figurine->setUser($this->getUser()); // lier au user connecté

            $imageFile = $form->get('image')->getData();
            if ($imageFile) {
                $newFilename = uniqid().'.'.$imageFile->guessExtension();
                $imageFile->move(
                    $this->getParameter('kernel.project_dir').'/public/uploads',
                    $newFilename
                );
                $figurine->setImage($newFilename);
            }

            $em->persist($figurine);
            $em->flush();

            $this->addFlash('success', 'Figurine ajoutée ✅');
            return $this->redirectToRoute('figurine_index');
        }

        return $this->render('figurine/new.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    #[Route('/{id}/edit', name: 'figurine_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Figurine $figurine, EntityManagerInterface $em): Response
    {
        if ($figurine->getUser() !== $this->getUser()) {
            throw $this->createAccessDeniedException("Tu ne peux pas modifier cette figurine !");
        }

        $form = $this->createForm(FigurineType::class, $figurine);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em->flush();
            $this->addFlash('success', 'Figurine modifiée ✅');
            return $this->redirectToRoute('figurine_index');
        }

        return $this->render('figurine/edit.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    #[Route('/{id}/delete', name: 'figurine_delete', methods: ['POST'])]
    public function delete(Request $request, Figurine $figurine, EntityManagerInterface $em): Response
    {
        if ($figurine->getUser() !== $this->getUser()) {
            throw $this->createAccessDeniedException("Tu ne peux pas supprimer cette figurine !");
        }

        if ($this->isCsrfTokenValid('delete'.$figurine->getId(), $request->request->get('_token'))) {
            $em->remove($figurine);
            $em->flush();
            $this->addFlash('success', 'Figurine supprimée ✅');
        }

        return $this->redirectToRoute('figurine_index');
    }
}
