<?php

namespace App\DataFixtures;

use App\Entity\User;
use App\Entity\Company;
use App\Entity\Project;
use App\Entity\Task;
use App\Entity\UserCompanyRole;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class AppFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        // Create Users
        $user1 = new User();
        $user1->setEmail('user1@local.host')
            ->setRoles(['ROLE_USER'])
            ->setPassword(password_hash('password1', PASSWORD_BCRYPT));
        $manager->persist($user1);

        // Create Users
        $user2 = new User();
        $user2->setEmail('user2@local.host')
            ->setRoles(['ROLE_USER'])
            ->setPassword(password_hash('password2', PASSWORD_BCRYPT));
        $manager->persist($user2);

        // Create Admin
        $admin = new User();
        $admin->setEmail('admin@local.host')
            ->setRoles(['ROLE_ADMIN'])
            ->setPassword(password_hash('admin_password', PASSWORD_BCRYPT));
        $manager->persist($admin);

        // Create Companies
        $company1 = new Company();
        $company1->setName('Company One')
            ->setSiret('12345678901234')
            ->setAddress('123 Street, City');
        $manager->persist($company1);

        // Create Companies
        $company2 = new Company();
        $company2->setName('Company Two')
            ->setSiret('98765432109876')
            ->setAddress('456 Avenue, Town');
        $manager->persist($company2);

        // Assign Roles to Users in Companies
        $userCompanyRole1 = new UserCompanyRole();
        $userCompanyRole1->setUser($user1)
            ->setCompany($company1)
            ->setRole('manager');
        $manager->persist($userCompanyRole1);

        // Assign Roles to Users in Companies
        $userCompanyRole2 = new UserCompanyRole();
        $userCompanyRole2->setUser($user2)
            ->setCompany($company1)
            ->setRole('consultant');
        $manager->persist($userCompanyRole2);

        // Assign Roles to Users in Companies
        $userCompanyRole3 = new UserCompanyRole();
        $userCompanyRole3->setUser($admin)
            ->setCompany($company2)
            ->setRole('admin');
        $manager->persist($userCompanyRole3);

        // Create Projects for the First Company
        $project1 = new Project();
        $project1->setTitle('Project One')
            ->setDescription('Description for Project One')
            ->setCompany($company1);
        $manager->persist($project1);

        // Create Projects for the Second Company
        $project2 = new Project();
        $project2->setTitle('Project Two')
            ->setDescription('Description for Project Two')
            ->setCompany($company1);
        $manager->persist($project2);


        // Create Tasks for the First Project
        $task = new Task();
        $task->setTitle("Task for Project 2")
            ->setDescription("Description for Task")
            ->setProject($project2)
            ->setCreatedAt(new \DateTime());
        $manager->persist($task);

        // Flush the changes to the database
        $manager->flush();
    }
}
