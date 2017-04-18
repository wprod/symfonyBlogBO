<?php

namespace CA\BlogBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\TextType;




use CA\BlogBundle\Entity\User;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
class EditUserTypeAdmin extends EditUserType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('username')
            ->add('password', RepeatedType::class, array('type' => PasswordType::class,'invalid_message' => 'Passwords don’t match',
        'first_options'=> array('label' => 'Password'),'second_options' => array('label' => 'Repeat Password')))
            ->add('salt', TextType::class, array('disabled' => true))
            ->add('mail');
    }
}
