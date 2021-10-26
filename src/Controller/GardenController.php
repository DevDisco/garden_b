<?php

namespace App\Controller;

use App\Entity\Garden;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Finder\Finder;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
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
            ->add('address', TextType::class, ['attr' => ['class' => 'form-control'], 'label' => 'Straatnaam en huisnummer'])
            ->add('zip', TextType::class, ['attr' => ['class' => 'form-control'], 'label' => 'Postcode (1234AB)'])
            ->add('municipality', TextType::class, ['attr' => ['class' => 'form-control'], 'label' => 'Plaatsnaam'])
            ->add('intro', TextareaType::class, ['required' => false, 'attr' => ['class' => 'form-control'], 'label' => 'Korte tekst voor overzichtspagina'])
            ->add('description', TextareaType::class, ['required' => false, 'attr' => ['class' => 'form-control'], 'label' => 'Lange tekst voor beschrijving tuin'])
            ->add('size', NumberType::class, ['html5'=>true,'attr' => ['class' => 'form-control'], 'label' => 'Oppervlakte'])
            ->add('anno', TextType::class, ['attr' => ['class' => 'form-control'], 'label' => 'Bouwjaar of -eeuw huis'])
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
            ->add('address', TextType::class, ['attr' => ['class' => 'form-control'], 'label' => 'Straatnaam en huisnummer'])
            ->add('zip', TextType::class, ['attr' => ['class' => 'form-control'], 'label' => 'Postcode (1234AB)'])
            ->add('municipality', TextType::class, ['attr' => ['class' => 'form-control'], 'label' => 'Plaatsnaam'])
            ->add('intro', TextareaType::class, ['required' => false, 'attr' => ['class' => 'form-control'], 'label' => 'Korte tekst voor overzichtspagina'])
            ->add('description', TextareaType::class, ['required' => false, 'attr' => ['class' => 'form-control'], 'label' => 'Lange tekst voor beschrijving tuin'])
            ->add('size', NumberType::class, ['html5' => true, 'attr' => ['class' => 'form-control'], 'label' => 'Oppervlakte'])
            ->add('anno', TextType::class, ['attr' => ['class' => 'form-control'], 'label' => 'Bouwjaar of -eeuw huis'])
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
     * @Route("/garden/upload/{id}", name="upload_garden")
     * Method({"GET","POST"})
     */
    public function upload(Request $request, $id): Response
    {
        $garden = new Garden();
        $garden = $this->getDoctrine()->getRepository(Garden::class)->find($id);

        $form = $this->createFormBuilder()
            ->add('image', FileType::class, ['attr' => ['class' => 'form-control'], 'label' => 'Foto toevoegen'])
            ->add('save', SubmitType::class, ['label' => 'Opslaan', 'attr' => ['class' => 'btn btn-primary mt-3']])
            ->getForm();

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $file = $request->files->get('form')['image'];
            $uploads_dir = $this->getParameter('uploads_dir') . "/" . $id;
            $file->move($uploads_dir, $file->getClientOriginalName());
        }

        $image_list = $this->getImages($id);

        return $this->render('gardens/upload.html.twig', ['form' => $form->createView(), 'garden' => $garden, 'images' => $image_list]);
    }

    /**
     * @Route("/garden/delete/{id}", name="garden_delete")
     * @Method({"DELETE"})
     */
    public function delete($id)
    {
        $garden = $this->getDoctrine()->getRepository(Garden::class)->find($id);
        $entityManager = $this->getDoctrine()->getManager();
        $entityManager->remove($garden);
        $entityManager->flush();

        $response = new Response();
        $response->send();
    }


    /**
     * @Route("/garden/remove/{id}/{timestamp}", name="image_delete")
     * @Method({"DELETE"})
     */
    public function remove($id, $timestamp)
    {
        $date = date('Y-m-d H:i:s', $timestamp);
        $dir = $this->getParameter('uploads_url') . "/" . $id . "/";

        //just to be sure, dir should exist
        if (is_dir($dir)) {

            $finder = new Finder();
            $finder->in($dir)->date($date);

            /** @var SplFileInfo $file */
            foreach ($finder as $file) {

                print $path = $dir . $file->getFilename();

                if (is_file($path)) {

                    unlink($path);
                }
            }
        }

        $response = new Response();
        $response->send();
        return $response;
    }

    /**
     * @Route("/garden/{id}", name="garden_show")
     */
    public function show($id): Response
    {
        $garden = $this->getDoctrine()->getRepository(Garden::class)->find($id);
        $image_list = $this->getImages($id);

        return $this->render('gardens/show.html.twig', ['garden' =>$garden, 'images' => $image_list]);
    }

    //shows all the images in the upload folder belonging to garden #id
    private function getImages($id)
    {
        $images = [];
        $dir = $this->getParameter('uploads_url') . "/" . $id . "/";

        //this dir doesn't exists before the first image is uploaded
        if (is_dir($dir)) {

            $finder = new Finder();
            $finder->files()->in($dir);

            /** @var SplFileInfo $file */
            foreach ($finder as $file) {
                $images[] = array("url" => $dir . $file->getFilename(), "file" => $file->getMTime());
            }
        }

        return $images;
    }
}
