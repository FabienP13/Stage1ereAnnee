<?php

namespace App\Controller;

use App\Entity\Ad;
use App\Form\AdType;
use App\Entity\Image;
use App\Repository\AdRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
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
     * Permet de créer une annonce
     * 
     *@Route ("/ads/new", name="ads_create")
     * 
     * @return Response
     */
    public function create( Request $request, EntityManagerInterface $manager){

        //Création formulaire
        $ad = new Ad();

    
        $form = $this->createForm(AdType::class, $ad);

        //Envoyez les champs du formulaires et les enregistrer
        $form->handleRequest($request);

        
        if($form->isSubmitted() && $form->isValid()){
            foreach($ad->getImages() as $image){
                $image->setAd($ad);
                $manager->persist($image);
            }

            $ad->setAuthor($this->getUser());
            
            $manager->persist($ad);
            $manager->flush();
            
            
            //Création flash 
            $this->addFlash(
                'success',
                "L'annonce <strong> {$ad->getTitle()}</strong> a bien été enregistrée !"
            );

            //rediriger vers la page de l'annonce
            return $this->redirectToRoute('ads_show', [
                'slug' => $ad->getSlug()
            ]);
        }

        return $this->render('ad/new.html.twig', [
            'form' => $form->createView()
        ]);
    }

    /**
     * Permet d'afficher le form d'édition
     *
     * @Route("/ads/{slug}/edit", name="ads_edit")
     * 
     * @return Response
     */
    public function edit(Ad $ad, Request $request, EntityManagerInterface $manager){
        $form = $this->createForm(AdType::class, $ad);

           //Envoyez les champs du formulaires et les enregistrer
           $form->handleRequest($request);

        
           if($form->isSubmitted() && $form->isValid()){
               foreach($ad->getImages() as $image){
                   $image->setAd($ad);
                   $manager->persist($image);
               }
               
               $manager->persist($ad);
               $manager->flush();
               
               
               //Création flash 
               $this->addFlash(
                   'success',
                   "Les modifications de l'annonce <strong> {$ad->getTitle()}</strong> ont bien été enregistrées !"
               );
   
               //rediriger vers la page de l'annonce
               return $this->redirectToRoute('ads_show', [
                   'slug' => $ad->getSlug()
               ]);
               }

        return $this->render('ad/edit.html.twig', [
            'form' => $form->createView(),
            'ad' => $ad
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
