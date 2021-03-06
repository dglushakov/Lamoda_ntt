<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Security\Core\User\UserInterface;

/**
 * @ORM\Entity(repositoryClass="App\Repository\UserRepository")
 * @UniqueEntity(
 *     fields={"username"},
 *     errorPath="username",
 *     message="User already exists"
 * )
 */
class User implements UserInterface
{
    const NUMBERS_OF_SHIFTS=
        [
        1=>1,2=>2,3=>3,4=>4
    ];
    const SECTORS_LIST=[
        'INBOUND'=>'INBOUND',
        'UNPACK'=>'UNPACK',
        'IN'=>'IN',
        'RET'=>'RET',
        'PUT_M1'=>'PUT_M1',
        'PUT_M2'=>'PUT_M2',
        'PUT_K'=>'PUT_K',
        'OUTBOUND'=>'OUTBOUND',
        'PICK_M1'=>'PICK_M1',
        'PICK_M2'=>'PICK_M2',
        'PICK_K'=>'PICK_K',
        'PACK_MB'=>'PACK_MB',
        'PACK_IS'=>'PACK_IS',
        'SORT'=>'SORT',
        'LOAD'=>'LOAD',
        'PUP_porter'=>'PUP_porter',
        'CAD_IN'=>'CAD_IN',
        'QUALITY'=>'QUALITY',
        'OPS_M'=>'OPS_M',
        'INDITEX'=>'INDITEX',
        'MAIN'=>'MAIN',
        'JEWELRY'=>'JEWELRY',
        'NOT_OPS'=>'NOT_OPS',
        'High-altitude_storage' => 'High-altitude_storage',
        'Extra_NTT' => 'Extra_NTT'
        
    ];
    const PROVIDERS_LIST=[
        'lamoda'=>'Lamoda',
        'ar'=>'Arcada',
        'gs'=>'GSR',
        'lt'=>'Leader Team',
        'mk'=>'MK Expert',
        'pr'=>'Premer-M',
        'rc'=>'Realnaya Cifra',
        'sm'=>'SMG',
        'st'=>'Stolica',
        'vp'=>'Vremya Pervih',
        'lp'=>'LPS',


    ];
    const USER_ROLES=[
        'Admin'=>'ROLE_ADMIN',
        'Peep'=>'ROLE_PEEP',
        'Sector manager'=>'ROLE_SECTOR_MANAGER',
    ];

    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=180, unique=true)
     */
    private $username;

    /**
     * @ORM\Column(type="json")
     */
    private $roles = [];

    /**
     * @var string The hashed password
     * @ORM\Column(type="string")
     */
    private $password;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $Sector;

    /**
     * @ORM\Column(type="smallint", nullable=true)
     */
    private $shift;

    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * A visual identifier that represents this user.
     *
     * @see UserInterface
     */
    public function getUsername(): string
    {
        return (string) $this->username;
    }

    public function setUsername(string $username): self
    {
        $this->username = $username;

        return $this;
    }

    /**
     * @see UserInterface
     */
    public function getRoles(): array
    {
        $roles = $this->roles;
        // guarantee every user at least has ROLE_USER
        $roles[] = 'ROLE_USER';

        return array_unique($roles);
    }

    public function setRoles(array $roles): self
    {
        $this->roles = $roles;

        return $this;
    }

    /**
     * @see UserInterface
     */
    public function getPassword(): string
    {
        return (string) $this->password;
    }

    public function setPassword(string $password): self
    {
        $this->password = $password;

        return $this;
    }

    /**
     * @see UserInterface
     */
    public function getSalt()
    {
        // not needed when using the "bcrypt" algorithm in security.yaml
    }

    /**
     * @see UserInterface
     */
    public function eraseCredentials()
    {
        // If you store any temporary, sensitive data on the user, clear it here
        // $this->plainPassword = null;
    }

    public function getSector(): ?string
    {
        return $this->Sector;
    }

    public function setSector(?string $Sector): self
    {
        $this->Sector = $Sector;

        return $this;
    }

    public function getShift(): ?int
    {
        return $this->shift;
    }

    public function setShift(?int $shift): self
    {
        $this->shift = $shift;

        return $this;
    }
}
