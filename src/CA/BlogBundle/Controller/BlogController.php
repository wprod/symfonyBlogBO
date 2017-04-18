<?php

namespace CA\BlogBundle\Controller;

use CA\BlogBundle\CABlogBundle;
use Doctrine\ORM\Mapping\Entity;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class BlogController extends Controller
{
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();

        $tenLastPost = $em->getRepository("CABlogBundle:Post")->findBy(
            array(),
            array("created" => "desc"),
            10,
            0
        );
        
        return $this->render('CABlogBundle:Blog:index.html.twig', array(
            "tenLastPost" => $tenLastPost
        ));
        
        
    }

}
