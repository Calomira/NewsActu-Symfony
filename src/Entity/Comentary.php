<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use App\Repository\ComentaryRepository;
use Gedmo\Timestampable\Traits\TimestampableEntity;
use Gedmo\SoftDeleteable\Traits\SoftDeleteableEntity;

/**
 * @ORM\Entity(repositoryClass=ComentaryRepository::class)
 */
class Comentary
{
    /**
     * Un 'trait' est une sorte de class PHP qui vous sert à réutiliser des propriétés et des Setters et Getters.
     * Cella est utile lorsque vous avez plusieurs entités qui partagent des propriétés communes.
     */


    #Pour utiliser ces deux classes PHP, il vous faudra 2 dépéndances PHP de Gedmo :composer:require gedmo/doctrine:extensions
    #timestamp : c'est une valeur numérique exprimé en secondes qui représent le temps écoulé (en seconde) depuis le 1er Janv. 1970 00:00

    use TimestampableEntity;
    use SoftDeleteableEntity;


    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $comment;

    /**
     * @ORM\ManyToOne(targetEntity=Article::class, inversedBy="commentaries")
     * @ORM\JoinColumn(nullable=false)
     */

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getComment(): ?string
    {
        return $this->comment;
    }

    public function setComment(string $comment): self
    {
        $this->comment = $comment;

        return $this;
    }
}
