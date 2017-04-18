<?php
namespace CA\BlogBundle\DataFixtures\ORM;

use Doctrine\Common\DataFixtures\FixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use CA\BlogBundle\Entity\User;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
//use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;


class LoadUserData extends Controller implements FixtureInterface
{
    public function load(ObjectManager $manager)
    {
        $user = new User();
        $userAdmin = new User();

        $user->setUsername('Martin');
        $plainPassword = 'platypus';
        $user->setSalt('12345');
        $encoder = $this->container->get('security.password_encoder');
        $encoded = $encoder->encodePassword($user, $plainPassword);
        $user->setPassword($encoded);
        $user->setRoles(array('ROLE_BLOGGER'));

        $user->setMail('martin@coding.eu');

        $userAdmin->setUsername('Gecko');
        $userAdmin->setSalt('12345');
        $plainPassword = 'coding';
        $encoder = $this->container->get('security.password_encoder');
        $encoded = $encoder->encodePassword($userAdmin, $plainPassword);
        $userAdmin->setPassword($encoded);

        $userAdmin->setRoles(array('ROLE_ADMIN'));

        $userAdmin->setMail('gecko@coding.eu');

        $manager->persist($user);
        $manager->persist($userAdmin);
        $manager->flush();



    }
}
?>
