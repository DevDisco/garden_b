<?php

namespace App\Controller;

use App\Entity\Garden;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;

class GetController extends AbstractController
{
    /**
     * @Route("/get/{id}", name="garden_get")
     * @Method({"GET"})
     */
    public function show($id)
    {
        $garden = $this->getDoctrine()->getRepository(Garden::class)->find($id);
        
        //$garden = ['username' => 'jane.doe', 'id' => $id];

        return $this->json($garden);
    }
}
