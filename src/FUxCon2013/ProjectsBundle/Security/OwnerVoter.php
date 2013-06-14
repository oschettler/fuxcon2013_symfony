<?php
/**
 * Grant edit permission to project owners.
 * @see http://symfony.com/doc/current/cookbook/security/voters.html
 */

namespace FUxCon2013\ProjectsBundle\Security;

use FUxCon2013\ProjectsBundle\Entity\Project;
use Symfony\Component\BrowserKit\Request;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\Security\Core\Authorization\Voter\VoterInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;

class OwnerVoter implements VoterInterface
{
    public function __construct(ContainerInterface $container)
    {
        $this->container     = $container;
    }

    public function supportsAttribute($attribute)
    {
        return $attribute == 'MAY_EDIT';
    }

    public function supportsClass($class)
    {
        // your voter supports all type of token classes, so return true
        return true;
    }

    function vote(TokenInterface $token, $object, array $attributes)
    {
        if (!in_array('MAY_EDIT', $attributes)) {
            return self::ACCESS_ABSTAIN;
        }
        if (!($object instanceof Project)) {
            return self::ACCESS_ABSTAIN;
        }

        $user = $token->getUser();
        $securityContext = $this->container->get('security.context');

        return $securityContext->isGranted('IS_AUTHENTICATED_FULLY')
            && $user->getId() == $object->getUser()->getId()
            ? self::ACCESS_GRANTED
            : self::ACCESS_DENIED;
    }
}
