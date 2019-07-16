<?php

namespace App\DataFixtures;

use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class UserFixtures extends Fixture
{
    private $passwordEncoder;

    public function __construct(UserPasswordEncoderInterface $passwordEncoder)
    {
        $this->passwordEncoder = $passwordEncoder;
    }


    public function load(ObjectManager $manager)
    {
        // $product = new Product();
        // $manager->persist($product);

        for ($i=1;$i<20;$i++) {
            $user = new User();
            $user->setUsername('user'.$i);
            $user->setPassword($this->passwordEncoder->encodePassword(
                $user,
                $i
            ));

            $user->setRoles(['ROLE_SECTOR_MANAGER']);
            $user->setShift(rand(1,4));
            $user->setSector(array_rand(USER::SECTORS_LIST));
            $manager->persist($user);
        }

        $goduser=new User();
        $goduser->setUsername('god');
        $goduser->setPassword($this->passwordEncoder->encodePassword(
            $goduser,
            'god'
        ));
        $goduser->setRoles(['ROLE_GOD', 'ROLE_ADMIN', 'ROLE_PEEP', 'ROLE_SECTOR_MANAGER']);
        $manager->persist($goduser);

        $adminuser=new User();
        $adminuser->setUsername('admin');
        $adminuser->setPassword($this->passwordEncoder->encodePassword(
            $adminuser,
            'admin'
        ));
        $adminuser->setRoles(['ROLE_ADMIN']);
        $manager->persist($adminuser);

        $peepuser=new User();
        $peepuser->setUsername('peep');
        $peepuser->setPassword($this->passwordEncoder->encodePassword(
            $peepuser,
            'peep'
        ));
        $peepuser->setRoles(['ROLE_PEEP']);
        $manager->persist($peepuser);

        $manager->flush();
    }
}
