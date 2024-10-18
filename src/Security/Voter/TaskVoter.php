<?php
namespace App\Security\Voter;

use App\Entity\Task;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;
use Symfony\Component\Security\Core\User\UserInterface;

final class TaskVoter extends Voter
{
    public const EDIT = 'TASK_EDIT';
    public const VIEW = 'TASK_VIEW';
    public const CREATE = 'TASK_CREATE';
    public const DELETE = 'TASK_DELETE';

    protected function supports(string $attribute, mixed $subject): bool
    {
        return in_array($attribute, [self::EDIT, self::VIEW, self::CREATE, self::DELETE])
            && $subject instanceof Task;
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
        $roles = [];
        foreach ($user->getUserCompanyRoles() as $userCompanyRole) {
            if ($userCompanyRole->getCompany() === $company) {
                $roles[] = $userCompanyRole->getRole();
            }
        }
        return $roles;
    }
}
