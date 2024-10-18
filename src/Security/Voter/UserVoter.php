<?php

namespace App\Security\Voter;

use App\Entity\User;
use App\Entity\UserCompanyRole;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;
use Symfony\Component\Security\Core\User\UserInterface;

final class UserVoter extends Voter
{
    public const EDIT = 'USER_EDIT';
    public const VIEW = 'USER_VIEW';
    public const CREATE = 'USER_CREATE';
    public const DELETE = 'USER_DELETE';

    protected function supports(string $attribute, mixed $subject): bool
    {
        return in_array($attribute, [self::EDIT, self::VIEW, self::CREATE, self::DELETE])
            && $subject instanceof User;
    }

    protected function voteOnAttribute(string $attribute, mixed $subject, TokenInterface $token): bool
    {
        $user = $token->getUser();

        // If the user is anonymous, deny access
        if (!$user instanceof UserInterface) {
            return false;
        }

        switch ($attribute) {
            case self::CREATE:
                return in_array('admin', $user->getRoles());

            case self::EDIT:
                return in_array('admin', $user->getRoles());

            case self::DELETE:
                return in_array('admin', $user->getRoles());

            case self::VIEW:
                return in_array('admin', $user->getRoles()) || in_array('manager', $user->getRoles()) || in_array('consultant', $user->getRoles());
        }

        return false;
    }
}
