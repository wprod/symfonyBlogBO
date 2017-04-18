<?php

namespace CA\BlogBundle\Controller;

use CA\BlogBundle\Entity\User;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

/**
 * User controller.
 *
 */
class UserController extends Controller
{
    // /**
    //  * Lists all posts of a user.
    //  *
    //  */
    public function listPostsAction($id)
    {
        $em = $this->getDoctrine()->getManager();
        $user = $em->getRepository('CABlogBundle:User')->find($id);

        if ($user === null) {
            throw new NotFoundHttpException("L'utilisateur n'existe pas.");
        }

        // On récupère la liste des candidatures de cette annonce
        $listPosts = $em
            ->getRepository('CABlogBundle:Post')
            ->findBy(array('user' => $user));

        return $this->render('user/listPosts.html.twig', array(
            'user' => $user,
            'listPosts' => $listPosts
        ));
    }

    public function listPostsUsernameAction($username)
    {
        $em = $this->getDoctrine()->getManager();
        $user = $em->getRepository('CABlogBundle:User')->findBy(array("username" => $username));

        if ($user === null) {
            throw new NotFoundHttpException("L'utilisateur n'existe pas.");
        }

        // On récupère la liste des candidatures de cette annonce
        $listPosts = $em
            ->getRepository('CABlogBundle:Post')
            ->findBy(array('user' => $user));

        return $this->render('user/listPosts.html.twig', array(
            'user' => $user,
            'listPosts' => $listPosts
        ));
    }

    /**
     * Creates a new user entity.
     *
     */
    public function registerAction(Request $request)
    {
        $user = new User();
        $form = $this->createForm('CA\BlogBundle\Form\UserType', $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $user->setRoles(array('ROLE_BLOGGER'));
            $plainPassword = $user->getPassword();
            $encoder = $this->container->get('security.password_encoder');
            $encoded = $encoder->encodePassword($user, $plainPassword);
            $user->setPassword($encoded);

            $em = $this->getDoctrine()->getManager();


            // return $this->redirectToRoute('user_show', array('id' => $user->getId()));

            $message = \Swift_Message::newInstance()
                ->setSubject('Hello Email')
                ->setFrom('witters.amand@gmail.com')
                ->setTo($user->getMail())
                ->setBody(
                    $this->renderView(

                    // app/Resources/views/Emails/registration.html.twig

                        'Emails/registration.html.twig',
                        array('name' => $user->getUsername())
                    ),
                    'text/html'
                );

            $this->get('mailer')->send($message);

            $em->persist($user);
            $em->flush();

            return $this->render('Emails/registration.html.twig');
        }

        return $this->render('user/register.html.twig', array(
            'user' => $user,
            'form' => $form->createView(),
        ));
    }

    /**
     * Finds and displays a user entity.
     *
     */
    public function listAction()
    {
        $em = $this->getDoctrine()->getManager();

        $users = $em->getRepository('CABlogBundle:User')->findAll();

        return $this->render('user/list.html.twig', array(
            'users' => $users,
        ));
    }

    /**
     * Displays a form to edit an existing user entity.
     *
     */
    public function editAction(Request $request, User $user)
    {
        $this->denyAccessUnlessGranted('edit', $user);

        $deleteForm = $this->createDeleteForm($user);

        if (!$this->get('security.authorization_checker')->isGranted('ROLE_ADMIN')) {
            $editForm = $this->createForm('CA\BlogBundle\Form\EditUserType', $user);
        } else {
            $editForm = $this->createForm('CA\BlogBundle\Form\EditUserTypeAdmin', $user);
        }

        $editForm->handleRequest($request);

        if ($editForm->isSubmitted() && $editForm->isValid()) {

            $plainPassword = $user->getPassword();
            $encoder = $this->container->get('security.password_encoder');
            $encoded = $encoder->encodePassword($user, $plainPassword);
            $user->setPassword($encoded);

            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('user_show', array('id' => $user->getId()));

        }

        return $this->render('user/edit.html.twig', array(
            'user' => $user,
            'edit_form' => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Deletes a user entity.
     * @Security("has_role('ROLE_ADMIN')")
     *
     */
    public function deleteAction(Request $request, User $user)
    {
        $form = $this->createDeleteForm($user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->remove($user);
            $em->flush();
        }

        return $this->redirectToRoute('user_show');
    }

    /**
     * Creates a form to delete a user entity.
     *
     * @param User $user The user entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm(User $user)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('user_delete', array('id' => $user->getId())))
            ->setMethod('DELETE')
            ->getForm();
    }
}
