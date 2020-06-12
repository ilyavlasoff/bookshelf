<?php

namespace App\Entity;

use App\Repository\BookRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=BookRepository::class)
 */
class Book
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @return string
     * @ORM\Column(type="string", nullable=false)
     */
    private $bookName;

    /**
     * @var string
     * @ORM\Column(type="string", nullable=true)
     */
    private $author;

    /**
     * @return string
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $coverPath;

    /**
     * @return string
     * @ORM\Column(type="string", length=255, nullable=false)
     */
    private $filePath;

    /**
     * @return \DateTime
     * @ORM\Column(type="datetime")
     */
    private $readDate;

    /**
     * @return int
     * @ORM\ManyToOne(targetEntity="User", inversedBy="books")
     * @ORM\JoinColumn(name="owner", referencedColumnName="id")
     */
    private $user;

    /**
     * @return string
     * @ORM\Column(type="string", length=2048, nullable=true)
     */
    private $description;

    /**
     * @return int
     * @ORM\Column(type="integer", nullable=true)
     */
    private $mark;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getBookName(): ?string
    {
        return $this->bookName;
    }

    public function setBookName(string $bookName): self
    {
        $this->bookName = $bookName;

        return $this;
    }

    public function getAuthor(): ?string
    {
        return $this->author;
    }

    public function setAuthor(?string $author): self
    {
        $this->author = $author;

        return $this;
    }

    public function getCoverPath(): ?string
    {
        return $this->coverPath;
    }

    public function setCoverPath(?string $coverPath): self
    {
        $this->coverPath = $coverPath;

        return $this;
    }

    public function getFilePath(): ?string
    {
        return $this->filePath;
    }

    public function setFilePath(string $filePath): self
    {
        $this->filePath = $filePath;

        return $this;
    }

    public function getReadDate(): ?\DateTimeInterface
    {
        return $this->readDate;
    }

    public function setReadDate(\DateTimeInterface $readDate): self
    {
        $this->readDate = $readDate;

        return $this;
    }

    public function getUser(): ?User
    {
        return $this->user;
    }

    public function setUser(?User $user): self
    {
        $this->user = $user;

        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(?string $description): self
    {
        $this->description = $description;

        return $this;
    }

    public function getMark(): ?int
    {
        return $this->mark;
    }

    public function setMark(?int $mark): self
    {
        $this->mark = $mark;

        return $this;
    }
}
