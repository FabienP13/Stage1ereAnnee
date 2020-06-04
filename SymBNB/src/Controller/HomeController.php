<?php

namespace App\Controller;

use App\Repository\AdRepository;
use App\Repository\UserRepository;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;


class HomeController extends AbstractController {


    /**
     * @Route("/hello/{prenom}/{age}", name = "hello")
     * @Route("/hello", name = "hello_base")
     * @Route("hello/{prenom}", name ="hello_prenom")
     * 
     * Montre la page qui dit bonjour
     * 
     * @return void
     */
    public function hello($prenom = "anonyme", $age = 0){
        return $this->render( 'hello.html.twig' , [
            'prenom' => $prenom,
            'age' => $age ]
        );
    }

    /**
     * @Route("/", name="homepage")
     */
    public function home(AdRepository $adRepo, UserRepository $userRepo){

        $prenoms = ["Lior" => 31 , "Joseph" => 12 , "Anne" => 55];

        return $this->render(
            'home.html.twig', 
            [
                'ads' => $adRepo->findBestAds(3),
                'users' => $userRepo->findBestUsers(2)
            ]
        );
    }

}



?>