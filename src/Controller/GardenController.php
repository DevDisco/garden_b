<?php

namespace App\Controller;

use App\Entity\Garden;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;


class GardenController extends AbstractController
{
    /**
     * @Route("/", name="garden_list")
     * @Method({"GET"})
     */
    public function index(): Response
    {
        $gardens = $this->getDoctrine()->getRepository(Garden::class)->findAll();

        return $this->render('gardens/index.html.twig', ['gardens' => $gardens]);
    }

    /**
     * @Route("/garden/new", name="new_garden")
     * Method({"GET","POST"})
     */
    public function new(Request $request): Response
    {
        $garden = new Garden();

        $form = $this->createFormBuilder($garden)
            ->add('address', TextType::class, ['attr' => ['class' => 'form-control']])
            ->add('zip', TextType::class, ['attr' => ['class' => 'form-control']])
            ->add('address', TextType::class, ['attr' => ['class' => 'form-control']])
            ->add('municipality', TextType::class, ['attr' => ['class' => 'form-control']])
            ->add('intro', TextareaType::class, ['required' => false, 'attr' => ['class' => 'form-control']])
            ->add('description', TextareaType::class, ['required' => false, 'attr' => ['class' => 'form-control']])
            ->add('size', TextType::class, ['attr' => ['class' => 'form-control']])
            ->add('anno', TextType::class, ['attr' => ['class' => 'form-control']])            
            ->add('save', SubmitType::class, ['label' => 'Klaar', 'attr' => ['class' => 'btn btn-primary mt-3']])
            ->getForm();

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $garden = $form->getData();

            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($garden);
            $entityManager->flush();
            
            return $this->redirectToRoute("garden_list");
        }

        return $this->render('gardens/new.html.twig', ['form' => $form->createView()]);
    }

    /**
     * @Route("/garden/edit/{id}", name="edit_garden")
     * Method({"GET","POST"})
     */
    public function edit(Request $request, $id): Response
    {
        $garden = new Garden();
        $garden = $this->getDoctrine()->getRepository(Garden::class)->find($id);

        $form = $this->createFormBuilder($garden)
            ->add('address', TextType::class, ['attr' => ['class' => 'form-control']])
            ->add('zip', TextType::class, ['attr' => ['class' => 'form-control']])
            ->add('address', TextType::class, ['attr' => ['class' => 'form-control']])
            ->add('municipality', TextType::class, ['attr' => ['class' => 'form-control']])
            ->add('intro', TextareaType::class, ['required' => false, 'attr' => ['class' => 'form-control']])
            ->add('description', TextareaType::class, ['required' => false, 'attr' => ['class' => 'form-control']])
            ->add('size', TextType::class, ['attr' => ['class' => 'form-control']])
            ->add('anno', TextType::class, ['attr' => ['class' => 'form-control']])
            ->add('save', SubmitType::class, ['label' => 'Aanpassen', 'attr' => ['class' => 'btn btn-primary mt-3']])
        ->getForm();

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->flush();

            return $this->redirectToRoute("garden_list");
        }

        return $this->render('gardens/edit.html.twig', ['form' => $form->createView(), 'garden' => $garden]);
    }

    /**
     * @Route("/garden/delete/{id}", name="garden_delete")
     * @Method({"DELETE"})
     */
    public function delete(Request $request, $id)
    {
        $garden = $this->getDoctrine()->getRepository(Garden::class)->find($id);


        $entityManager = $this->getDoctrine()->getManager();
        $entityManager->remove($garden);
        $entityManager->flush();
        
        $response = new Response();
        $response->send();
    }

    /**
     * @Route("/garden/{id}", name="garden_show")
     */
    public function show($id): Response
    {
        $garden = $this->getDoctrine()->getRepository(Garden::class)->find($id);

        return $this->render('gardens/show.html.twig', ['garden' => $garden]);
    }
}
