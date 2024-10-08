<?php

namespace App\Security\Voter;

use App\Entity\Project;
use App\Entity\UserCompanyRole;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;
use Symfony\Component\Security\Core\User\UserInterface;
#[ApiResource(
    description: 'This is a project resource',
    attributes: [
        'pagination_items_per_page' => 10,
        'normalization_context' => ['groups' => ['read']],
        'denormalization_context' => ['groups' => ['write']],
    ]
)]
final class ProjectVoter extends Voter
{
    public const EDIT = 'POST_EDIT';
    public const VIEW = 'POST_VIEW';
    public const CREATE = 'POST_CREATE';
    public const DELETE = 'POST_DELETE';

    protected function supports(string $attribute, mixed $subject): bool
    {
        // Check if the attribute is supported and if the subject is a Project
        return in_array($attribute, [self::EDIT, self::VIEW, self::CREATE, self::DELETE])
            && $subject instanceof Project;
    }

    protected function voteOnAttribute(string $attribute, mixed $subject, TokenInterface $token): bool
    {
        $user = $token->getUser();

        // If the user is anonymous, deny access
        if (!$user instanceof UserInterface) {
            return false;
        }

        $userRoles = $this->getUserRolesInCompany($user, $subject->getCompany());

        switch ($attribute) {
            case self::CREATE:
                return in_array('admin', $userRoles) || in_array('manager', $userRoles);

            case self::EDIT:
                return in_array('admin', $userRoles) || in_array('manager', $userRoles);

            case self::DELETE:
                return in_array('admin', $userRoles);

            case self::VIEW:
                return in_array('admin', $userRoles) || in_array('manager', $userRoles) || in_array('consultant', $userRoles);
        }

        return false;
    }

    private function getUserRolesInCompany(UserInterface $user, $company): array
    {
        return []; // Return an array of roles (e.g., ['admin', 'manager'])
    }
}
