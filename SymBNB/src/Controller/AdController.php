<?php

namespace App\Controller;

use App\Entity\Ad;
use App\Repository\AdRepository;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class AdController extends AbstractController
{   
    //Récupérer les données de l'entité Ad

    /**
     * @Route("/ads", name="ads_index")
     */
    public function index(AdRepository $repo)
    {        
        $ads = $repo->findAll();

        return $this->render('ad/index.html.twig', [
            'ads' => $ads,
        ]);
    }


    /**
     * Permet d'afficher une seule annonce 
     * 
     * @Route("/ads/{slug}", name ="ads_show")
     * 
     * @return Response
     */

     //convertir le paramètre slug avec ParamConverter
    public function show(Ad $ad){
    
        return $this->render('ad/show.html.twig', [
            'ad' => $ad
            ]);
    }
}
