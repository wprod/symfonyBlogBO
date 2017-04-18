<?php

namespace CA\BlogBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;


class CategoryController extends Controller
{
    public function showAction($id)
    {
        $em = $this->getDoctrine()->getManager();
        $category = $em->getRepository('CABlogBundle:Category')->find($id);

        $listPosts = $category->getPosts();

        return $this->render('category/show.html.twig', array(
                'category' => $category,
                'listPosts' => $listPosts
            )
        );
    }

    public function showByNameAction($name)
    {
        $em = $this->getDoctrine()->getManager();
        $category = $em->getRepository('CABlogBundle:Category')->findOneBy(array("name" => $name));

        $listPosts = $category->getPosts();

        return $this->render('category/show.html.twig', array(
                'category' => $category,
                'listPosts' => $listPosts
            )
        );
    }
}