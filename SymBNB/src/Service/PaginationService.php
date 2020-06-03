<?php 
namespace App\Service;

use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\RequestStack;
use Twig\Environment;

class PaginationService{
    private $entityClass;
    private $limit = 10;
    private $currentPage = 1;
    private $manager;
    private $twig;
    private $route;
    private $templatePath;

    
    /**
     * Constructeur du service de pagination qui sera appelé par Symfony
     * 
     * N'oubliez pas de configurer votre fichier services.yaml afin que Symfony sache quelle valeur
     * utiliser pour le $templatePath
     *
     * @param EntityManagerInterface $manager
     * @param Environment $twig
     * @param RequestStack $request
     * @param string $templatePath
     */

    public function __construct(EntityManagerInterface $manager, Environment $twig, RequestStack $request, $templatePath){
        $this->route =$request->getCurrentRequest()->attributes->get('_route');
        $this->manager = $manager;
        $this->twig    = $twig;
        $this->templatePath = $templatePath;
    }

    /**
     * Permet de choisir un template de pagination
     *
     * @param string $templatePath
     * @return self
     */

    public function setTemplatePath($templatePath) {
        $this->templatePath = $templatePath; 
        return $this;
    }

    /**
     * Permet de récupérer le templatePath actuellement utilisé
     *
     * @return string
     */ 


    public function getTemplatePath(){
        return $this->templatePath;
    }

    public function setRoute($route) {
        $this->route = $route; 
        return $this;
    }

    public function getRoute(){
        return $this->route;
    }


    /**
     * Permet d'afficher le rendu de la navigation au sein d'un template twig !
     * 
     * On se sert ici de notre moteur de rendu afin de compiler le template qui se trouve au chemin
     * de notre propriété $templatePath, en lui passant les variables :
     * - page  => La page actuelle sur laquelle on se trouve
     * - pages => le nombre total de pages qui existent
     * - route => le nom de la route à utiliser pour les liens de navigation
     *
     * Attention : cette fonction ne retourne rien, elle affiche directement le rendu
     * 
     * @return void
     */


    public function display(){
        $this->twig->display($this->templatePath, [
            'page' => $this->currentPage,
            'pages' => $this->getPages(),
            'route' => $this->route
        ]);
    }


    /**
     * Permet de récupérer le nombre de pages qui existent sur une entité particulière
     * 
     * Elle se sert de Doctrine pour récupérer le repository qui correspond à l'entité que l'on souhaite
     * paginer (voir la propriété $entityClass) puis elle trouve le nombre total d'enregistrements grâce
     * à la fonction findAll() du repository
     * 
     * @throws Exception si la propriété $entityClass n'est pas configurée
     * 
     * @return int
     */

    public function getPages(){
        if(empty($this->entityClass)){
            throw new \Exception("Vous n'avez pas spécifier l'entité sur lauqelle nous devons paginer ! ");
        }
        //1 Connaitre le total des enregistrements de la table
        $repo = $this->manager->getRepository($this->entityClass);
        $total = count($repo->findAll());

        //2 Faire la division , l'arrondi et le renvoyer 
        $pages = ceil($total / $this->limit);

        return $pages;
    }

    /**
     * Permet de récupérer les données paginées pour une entité spécifique
     * 
     * Elle se sert de Doctrine afin de récupérer le repository pour l'entité spécifiée
     * puis grâce au repository et à sa fonction findBy() on récupère les données dans une 
     * certaine limite et en partant d'un offset
     * 
     * @throws Exception si la propriété $entityClass n'est pas définie
     *
     * @return array
     */

    public function getData(){
        if(empty($this->entityClass)){
            throw new \Exception("Vous n'avez pas spécifier l'entité sur lauqelle nous devons paginer ! ");
        }
        //1 calculer offset
        $offset = $this->currentPage * $this->limit - $this->limit;
        //2 demander au repository de trouver les éléments
        $repo = $this->manager->getRepository($this->entityClass);
        $data = $repo->findBy([], [], $this->limit, $offset);
        //3 Renvoyer le éléments en question

        return $data;
    }

    /**
     * Permet de spécifier la page que l'on souhaite afficher
     *
     * @param int $page
     * @return self
     */

    public function setPage($page){
        $this->currentPage = $page;
        return $this;
    }

    /**
     * Permet de récupérer la page qui est actuellement affichée
     *
     * @return int
     */

    public function getPage(){
        return $this->currentPage;
    }

    /**
     * Permet de spécifier le nombre d'enregistrements que l'on souhaite obtenir !
     *
     * @param int $limit
     * @return self
     */

    public function setLimit($limit){
        $this->limit = $limit;
        return $this;
    }

    /**
     * Permet de récupérer le nombre d'enregistrements qui seront renvoyés
     *
     * @return int
     */

    public function getLimit(){
        return $this->limit;
    }

    /**
     * Permet de spécifier l'entité sur laquelle on souhaite paginer
     * Par exemple :
     * - App\Entity\Ad::class
     * - App\Entity\Comment::class
     *
     * @param string $entityClass
     * @return self
     */

    public function setEntityClass($entityClass){
        $this->entityClass = $entityClass;
        return $this;
    }


    /**
     * Permet de récupérer l'entité sur laquelle on est en train de paginer
     *
     * @return string
     */
    public function getEntityClass(){
        return $this->entityClass;
    }
}
