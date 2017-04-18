<?php
namespace CA\BlogBundle\Security\Voter;

use CA\BlogBundle\Entity\Post;
use CA\BlogBundle\Entity\User;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\AccessDecisionManagerInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;

class BlogVoter extends Voter
{
    // these strings are just invented: you can use anything
    const EDIT = 'edit';
    const DELETE = 'delete';

    private $decisionManager;

    public function __construct(AccessDecisionManagerInterface $decisionManager)
    {
        $this->decisionManager = $decisionManager;
    }

    protected function supports($attribute, $subject)
    {
        // if the attribute isn't one we support, return false
        if (!in_array($attribute, array(self::EDIT, self::DELETE))) {
            return false;
        }

        // only vote on Post objects inside this voter
        if (!$subject instanceof Post) {
            return false;
        }

        return true;
    }

    protected function voteOnAttribute($attribute, $subject, TokenInterface $token)
    {
        // ROLE_SUPER_ADMIN can do anything! The power!
        if ($this->decisionManager->decide($token, array('ROLE_ADMIN'))) {
            return true;
        }

        $user = $token->getUser();

        if (!$user instanceof User) {
            // the user must be logged in; if not, deny access
            return false;
        }

        // you know $subject is a Post object, thanks to supports
        /** @var Post $post */
        $post = $subject;

        switch ($attribute) {
            case self::EDIT:
                return $this->canEdit($post, $user);
            case self::DELETE:
                return $this->canDelete($post, $user);
        }

        throw new \LogicException('This code should not be reached!');
    }

    private function canEdit(Post $post, User $user)
    {
        // if they can edit, they can view
        if ($this->canDelete($post, $user)) {
            return true;
        }

        // the Post object could have, for example, a method isPrivate()
        // that checks a boolean $private property
        return false;
    }

    private function canDelete(Post $post, User $user)
    {
        // this assumes that the data object has a getOwner() method
        // to get the entity of the user who owns this data object
        return $user === $post->getUser();
    }
}
?>
