<?php

namespace App\Controller;

use App\Entity\Ingredient;
use App\Form\IngredientType;
use App\Repository\IngredientRepository;
use Doctrine\Persistence\ManagerRegistry;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;


class IngredientController extends AbstractController
{
    #[Route('/ingredient', name: 'app_ingredient', methods: ['GET'])]
    public function index(IngredientRepository $repository, PaginatorInterface $paginator, Request $request): Response
    {
        $ingredients = $paginator->paginate(
            $repository->findAll(),
            $request->query->getInt('page', 1), 10
        );
        return $this->render('ingredient/index.html.twig',[
            'ingredients' => $ingredients
        ]);
    }

    #[Route('/ingredient/new', name: 'app_ingredient.new', methods: ['GET', 'POST'])]
    public function new(ManagerRegistry $doctrine, Request $request): Response
    {
        $ingredients = new Ingredient();
        $form = $this->createForm(IngredientType::class, $ingredients);
        
        $form->handleRequest($request);
        if($form->isSubmitted() && $form->isValid()) {
            $em = $doctrine->getManager();
            $em->persist($ingredients);
            $em->flush();
            $this->addFlash('success', 'Un ingredient a ete ajouter avec success');
            return $this->redirectToRoute('app_ingredient');
        }
        
        return $this->render('ingredient/new.html.twig',[
            'form' => $form->createView()
        ]);
    }
    
    #[Route('/ingredient/edit/{id}', name: 'app_ingredient.edit', methods: ['GET', 'POST'])]
    public function edit(Ingredient $ingredients, ManagerRegistry $doctrine , Request $request): Response
    {
        $form = $this->createForm(IngredientType::class, $ingredients);
        
        $form->handleRequest($request);
        if($form->isSubmitted() && $form->isValid()) {
            $em = $doctrine->getManager();
            $em->persist($ingredients);
            $em->flush();
            $this->addFlash('warning', 'Un ingredient a ete modifier avec success');
            return $this->redirectToRoute('app_ingredient');
        }

        return $this->render('ingredient/edit.html.twig',[
            'form' => $form->createView(),
        ]);
    }

    #[Route('/ingredient/delete/{id}', name: 'app_ingredient.delete', methods: ['GET'])]
    public function delete(ManagerRegistry $doctrine, Ingredient $ingredients): Response
    {
        $em = $doctrine->getManager();
        $em->remove($ingredients);
        $em->flush();

        $this->addFlash('delete', 'Votre ingredient a ete supprimer');

        return $this->redirectToRoute('app_ingredient');
    }
}
