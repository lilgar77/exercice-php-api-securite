<?php

namespace App\Security\Voter;

use App\Entity\Company;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;
use Symfony\Component\Security\Core\User\UserInterface;

final class CompanyVoter extends Voter
{
    public const VIEW = 'COMPANY_VIEW';
    public const CREATE = 'COMPANY_CREATE';
    public const EDIT = 'COMPANY_EDIT';
    public const DELETE = 'COMPANY_DELETE';

    private const ROLE_ADMIN = 'ROLE_ADMIN';
    private const ROLE_MANAGER = 'ROLE_MANAGER';
    private const ROLE_CONSULTANT = 'ROLE_CONSULTANT';

    protected function supports(string $attribute, mixed $subject): bool
    {
        return in_array($attribute, [self::VIEW, self::CREATE, self::EDIT, self::DELETE])
            && $subject instanceof Company;
    }

    protected function voteOnAttribute(string $attribute, mixed $subject, TokenInterface $token): bool
    {
        $user = $token->getUser();

        if (!$user instanceof UserInterface) {
            return false;
        }

        // Récupère les rôles de l'utilisateur dans la société
        $userRoles = $this->getUserRolesInCompany($user, $subject);

        switch ($attribute) {
            case self::CREATE:
                return in_array(self::ROLE_ADMIN, $userRoles);

            case self::EDIT:
                return in_array(self::ROLE_ADMIN, $userRoles);

            case self::DELETE:
                return in_array(self::ROLE_ADMIN, $userRoles);

            case self::VIEW:
                return in_array(self::ROLE_ADMIN, $userRoles) ||
                    in_array(self::ROLE_MANAGER, $userRoles) ||
                    in_array(self::ROLE_CONSULTANT, $userRoles);
        }

        return false;
    }

    private function getUserRolesInCompany(UserInterface $user, Company $company): array
    {
        $role = $company->getUserRoles()->filter(function ($userRole) use ($user) {
            return $userRole->getUser() === $user;
        })->first();

        if ($role) {
            dump($role->getRole());
            return [$role->getRole()];
        }

        return [];
    }
}
