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

    protected function supports(string $attribute, mixed $subject): bool
    {
        // Vérifie si l'attribut est supporté et si le sujet est une Company
        return in_array($attribute, [self::VIEW, self::CREATE, self::EDIT, self::DELETE])
            && $subject instanceof Company;
    }

    protected function voteOnAttribute(string $attribute, mixed $subject, TokenInterface $token): bool
    {
        $user = $token->getUser();

        // Si l'utilisateur est anonyme, refuse l'accès
        if (!$user instanceof UserInterface) {
            return false;
        }

        // On suppose que l'utilisateur a un moyen d'obtenir ses rôles dans la société
        $userRoles = $this->getUserRolesInCompany($user, $subject);

        switch ($attribute) {
            case self::CREATE:
                return in_array('admin', $userRoles);

            case self::EDIT:
                return in_array('admin', $userRoles);

            case self::DELETE:
                return in_array('admin', $userRoles);

            case self::VIEW:
                return in_array('admin', $userRoles) || in_array('manager', $userRoles) || in_array('consultant', $userRoles);
        }

        return false;
    }

    private function getUserRolesInCompany(UserInterface $user, Company $company): array
    {
        return []; // Retourne un tableau de rôles
    }
}
