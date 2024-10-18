<?php
namespace App\Security\Voter;

use App\Entity\UserCompanyRole;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;
use Symfony\Component\Security\Core\User\UserInterface;

final class UserCompagnyRoleVoter extends Voter
{
    public const EDIT = 'USER_ROLE_EDIT';
    public const VIEW = 'USER_ROLE_VIEW';
    public const CREATE = 'USER_ROLE_CREATE';
    public const DELETE = 'USER_ROLE_DELETE';

    protected function supports(string $attribute, mixed $subject): bool
    {
        return in_array($attribute, [self::EDIT, self::VIEW, self::CREATE, self::DELETE])
            && $subject instanceof UserCompanyRole;
    }

    protected function voteOnAttribute(string $attribute, mixed $subject, TokenInterface $token): bool
    {
        $user = $token->getUser();

        if (!$user instanceof UserInterface) {
            return false;
        }

        $userRoles = $this->getUserRolesInCompany($user, $subject->getCompany());

        switch ($attribute) {
            case self::CREATE:
                return in_array('admin', $userRoles);

            case self::EDIT:
                return in_array('admin', $userRoles);

            case self::DELETE:
                return in_array('admin', $userRoles);

            case self::VIEW:
                return in_array('admin', $userRoles) || in_array('manager', $userRoles);
        }

        return false;
    }

    private function getUserRolesInCompany(UserInterface $user, $company): array
    {
        $roles = [];
        foreach ($user->getUserCompanyRoles() as $userCompanyRole) {
            if ($userCompanyRole->getCompany() === $company) {
                $roles[] = $userCompanyRole->getRole();
            }
        }
        return $roles;
    }
}
