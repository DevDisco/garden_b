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
            ->add('size', TextType::class, ['attr' => ['class' => 'form-control'], 'label' => 'Oppervlakte'])
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
            ->add('size', TextType::class, ['attr' => ['class' => 'form-control'], 'label' => 'Oppervlakte'])
            ->add('anno', TextType::class, ['attr' => ['class' => 'form-control'], 'label' => 'Bouwjaar of -eeuw huis'])
            ->add('save', SubmitType::class, ['label' => 'Aanpassen', 'attr' => ['class' => 'btn btn-primary mt-3']])
        ->getForm();

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->flush();

            return $this->redirectToRoute("garden_list");
        }

        $image_list = $this->getImages($id);
        //trhow into twig
        

        return $this->render('gardens/edit.html.twig', ['form' => $form->createView(), 'garden' => $garden, 'images' => $image_list]);
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
            $uploads_path = $this->getParameter('uploads_path')."/". $id;
            $file->move($uploads_path,$file->getClientOriginalName() );

            return $this->redirectToRoute("edit_garden", ['id'=>$id]);
        }

        return $this->render('gardens/upload.html.twig', ['form' => $form->createView(), 'garden' => $garden]);
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
    
    //http://www.inanzzz.com/index.php/post/kgcu/uploading-images-to-a-private-directory-and-serving-them-in-twig-template

    private function getImages($id)
    {
        $images = [];
        $dir = $this->getParameter('uploads_folder')."/".$id."/";
        $finder = new Finder($this->getParameter('uploads_path') . "/" . $id);
        $finder->files()->in($dir);

        /** @var SplFileInfo $file */
        foreach ($finder as $file) {
            $images[] = $dir.$file->getFilename();
        }

        return $images;
    }
}
