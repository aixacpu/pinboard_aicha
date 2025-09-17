<?php

namespace App\Controller;

// On importe l’entité Figurine
use App\Entity\Figurine;
// On importe le formulaire généré automatiquement (FigurineType)
use App\Form\FigurineType;
// Pour interroger la base via Doctrine
use App\Repository\FigurineRepository;
use Doctrine\ORM\EntityManagerInterface;
// Base des contrôleurs Symfony
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
// Pour gérer les requêtes HTTP (GET/POST)
use Symfony\Component\HttpFoundation\Request;
// Pour renvoyer une réponse HTTP
use Symfony\Component\HttpFoundation\Response;
// Pour définir les routes
use Symfony\Component\Routing\Annotation\Route;

#[Route('/figurine')]
class FigurineController extends AbstractController
{
    /**
     * Affiche la liste de toutes les figurines
     */
    #[Route('/', name: 'figurine_index', methods: ['GET'])]
    public function index(FigurineRepository $figurineRepository): Response
    {
        // On envoie toutes les figurines à la vue Twig
        return $this->render('figurine/index.html.twig', [
            'figurines' => $figurineRepository->findAll(),
        ]);
    }

    /**
     * Crée une nouvelle figurine
     */
    #[Route('/new', name: 'figurine_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $em): Response
    {
        $figurine = new Figurine();

        // On génère le formulaire Symfony lié à l’entité Figurine
        $form = $this->createForm(FigurineType::class, $figurine);
        $form->handleRequest($request); // Lie les données du formulaire à l’objet

        // Vérifie si le formulaire a été soumis et est valide
        if ($form->isSubmitted() && $form->isValid()) {
            // On lie la figurine à l’utilisateur connecté
            $figurine->setUser($this->getUser());

            // Gestion de l’upload d’image
            $imageFile = $form->get('image')->getData();
            if ($imageFile) {
                // On génère un nom de fichier unique
                $newFilename = uniqid().'.'.$imageFile->guessExtension();
                // On déplace le fichier dans /public/uploads
                $imageFile->move(
                    $this->getParameter('kernel.project_dir').'/public/uploads',
                    $newFilename
                );
                // On stocke le nom du fichier en DB
                $figurine->setImage($newFilename);
            }

            // Sauvegarde en DB
            $em->persist($figurine);
            $em->flush();

            // Message flash pour l’utilisateur
            $this->addFlash('success', 'Figurine ajoutée ✅');

            // Redirection vers la liste
            return $this->redirectToRoute('figurine_index');
        }

        // Si pas soumis → afficher le formulaire
        return $this->render('figurine/new.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    /**
     * Modifier une figurine
     */
    #[Route('/{id}/edit', name: 'figurine_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Figurine $figurine, EntityManagerInterface $em): Response
    {
        // Vérifie que seul le créateur peut modifier
        if ($figurine->getUser() !== $this->getUser()) {
            throw $this->createAccessDeniedException("Tu ne peux pas modifier cette figurine !");
        }

        // Génère le formulaire avec les données existantes
        $form = $this->createForm(FigurineType::class, $figurine);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // Pas besoin de persist(), Doctrine connaît déjà l’objet
            $em->flush();

            $this->addFlash('success', 'Figurine modifiée ✅');
            return $this->redirectToRoute('figurine_index');
        }

        return $this->render('figurine/edit.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    /**
     * Supprimer une figurine
     */
    #[Route('/{id}/delete', name: 'figurine_delete', methods: ['POST'])]
    public function delete(Request $request, Figurine $figurine, EntityManagerInterface $em): Response
    {
        // Vérifie que seul le créateur peut supprimer
        if ($figurine->getUser() !== $this->getUser()) {
            throw $this->createAccessDeniedException("Tu ne peux pas supprimer cette figurine !");
        }

        // Vérifie le token CSRF (sécurité formulaire)
        if ($this->isCsrfTokenValid('delete'.$figurine->getId(), $request->request->get('_token'))) {
            $em->remove($figurine);
            $em->flush();
            $this->addFlash('success', 'Figurine supprimée ✅');
        }

        return $this->redirectToRoute('figurine_index');
    }
}
