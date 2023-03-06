<?php

namespace App\Entity;

use App\Repository\PostRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: PostRepository::class)]
class Post
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;


    #[ORM\Column(length: 255)]
    private ?string $contenu = null;

    #[ORM\Column]
    private ?\DateTimeImmutable $createdAt = null;

    #[ORM\ManyToOne(inversedBy: 'posts')]
    #[ORM\JoinColumn(nullable: false)]
    private ?User $createdBy = null;

    #[ORM\OneToMany(mappedBy: 'LinkedPost', targetEntity: Commentaires::class, orphanRemoval: true)]
    private Collection $commentaires;

    #[ORM\ManyToMany(targetEntity: User::class, inversedBy: 'retweets')]
    #[ORM\JoinTable(name: 'retweets')]
    private Collection $retweet;

    #[ORM\ManyToMany(targetEntity: User::class, inversedBy: 'likes')]
    #[ORM\JoinTable(name: 'likes')]
    private Collection $likes;

    #[ORM\Column(nullable: false)]
    private ?\DateTimeImmutable $lastModified = null;


    public function __construct()
    {
        $this->commentaires = new ArrayCollection();
        $this->retweet = new ArrayCollection();
        $this->likes = new ArrayCollection();
    }

   

    #[ORM\ManyToOne(inversedBy: 'posts')]
    #[ORM\JoinColumn(nullable: false)]

    public function getId(): ?int
    {
        return $this->id;
    }

    
    public function __toString() {
        return $this->contenu;
    }

    public function getContenu(): ?string
    {
        return $this->contenu;
    }

    public function setContenu(string $contenu): self
    {
        $this->contenu = $contenu;

        return $this;
    }

    public function getCreatedAt(): ?\DateTimeImmutable
    {
        return $this->createdAt;
    }

    public function setCreatedAt(\DateTimeImmutable $createdAt): self
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    public function getCreatedBy(): ?User
    {
        return $this->createdBy;
    }

    public function setCreatedBy(?User $createdBy): self
    {
        $this->createdBy = $createdBy;

        return $this;
    }

    /**
     * @return Collection<int, Commentaires>
     */
    public function getCommentaires(): Collection
    {
        return $this->commentaires;
    }

    public function addCommentaire(Commentaires $commentaire): self
    {
        if (!$this->commentaires->contains($commentaire)) {
            $this->commentaires->add($commentaire);
            $commentaire->setLinkedPost($this);
        }

        return $this;
    }

    public function removeCommentaire(Commentaires $commentaire): self
    {
        if ($this->commentaires->removeElement($commentaire)) {
            // set the owning side to null (unless already changed)
            if ($commentaire->getLinkedPost() === $this) {
                $commentaire->setLinkedPost(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, User>
     */
    public function getRetweet(): Collection
    {
        return $this->retweet;
    }

    public function addRetweet(User $retweet): self
    {
        if (!$this->retweet->contains($retweet)) {
            $this->retweet->add($retweet);
        }

        return $this;
    }

    public function removeRetweet(User $retweet): self
    {
        $this->retweet->removeElement($retweet);

        return $this;
    }

    /**
     * @return Collection<int, User>
     */
    public function getLikes(): Collection
    {
        return $this->likes;
    }

    public function addLike(User $like): self
    {
        if (!$this->likes->contains($like)) {
            $this->likes->add($like);
        }

        return $this;
    }

    public function removeLike(User $like): self
    {
        $this->likes->removeElement($like);

        return $this;
    }

    public function getLastModified(): ?\DateTimeImmutable
    {
        return $this->lastModified;
    }

    public function setLastModified(?\DateTimeImmutable $lastModified): self
    {
        $this->lastModified = $lastModified;

        return $this;
    }




}