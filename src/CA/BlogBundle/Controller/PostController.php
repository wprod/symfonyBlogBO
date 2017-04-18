<?php

namespace CA\BlogBundle\Controller;

use CA\BlogBundle\Entity\Post;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

/**
 * Post controller.
 *
 */
class PostController extends Controller
{
    /**
     * Lists all post entities.
     *
     */
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();

        $posts = $em->getRepository('CABlogBundle:Post')->findAll();

        return $this->render('post/index.html.twig', array(
            'posts' => $posts,
        ));
    }

    /**
     * Creates a new post entity.
     *
     */
    public function newAction(Request $request)
    {

        if (!$this->get('security.authorization_checker')->isGranted('ROLE_BLOGGER')) {
            return $this->redirectToRoute('login');
        }

        $post = new Post();
        $post->setCreated(new \DateTime());
        $post->setUpdated(null);

        $post->setUser($this->getUser());
        $form = $this->createForm('CA\BlogBundle\Form\PostType', $post);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $em = $this->getDoctrine()->getManager();
            $em->persist($post);
            $em->flush();

            return $this->redirectToRoute('post_show', array('id' => $post->getId()));
        }

        return $this->render('post/new.html.twig', array(
            'post' => $post,
            'form' => $form->createView(),
        ));
    }

    /**
     * Finds and displays a post entity.
     *
     */
    public function showAction(Post $post)
    {
        $deleteForm = $this->createDeleteForm($post);
        $category = $post->getCategory()->getValues();
        return $this->render('post/show.html.twig', array(
                'post' => $post,
                'categories' => $category,
                'delete_form' => $deleteForm->createView(),
            )
        );
    }


    /**
     * Finds and displays a post entity by his title.
     *
     */
    public function showTitleAction(Post $post)
    {
        $deleteForm = $this->createDeleteForm($post);

        return $this->render('post/show.html.twig', array(
                'post' => $post,
                'delete_form' => $deleteForm->createView(),
            )
        );
    }

    /**
     * Displays a form to edit an existing post entity.
     *
     */
    public function editAction(Request $request, Post $post)
    {

        $this->denyAccessUnlessGranted('edit', $post);

        $deleteForm = $this->createDeleteForm($post);
        $post->setUpdated(new \DateTime());
        $editForm = $this->createForm('CA\BlogBundle\Form\PostType', $post);
        $editForm->handleRequest($request);

        if ($editForm->isSubmitted() && $editForm->isValid()) {
            $this->getDoctrine()->getManager()->flush();
            return $this->redirectToRoute('post_index');
        }

        return $this->render('post/edit.html.twig', array(
            'post' => $post,
            'edit_form' => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Deletes a post entity.
     *
     */
    public function deleteAction(Request $request, Post $post)
    {
        $this->denyAccessUnlessGranted('delete', $post);

        $form = $this->createDeleteForm($post);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->remove($post);
            $em->flush();
        }

        return $this->redirectToRoute('post_index');
    }

    /**
     * Creates a form to delete a post entity.
     *
     * @param Post $post The post entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm(Post $post)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('post_delete', array('id' => $post->getId())))
            ->setMethod('DELETE')
            ->getForm();
    }
}
