<?php

namespace App\Entity;
use Doctrine\ORM\Mapping as ORM;
/**
 * @ORM\Entity(repositoryClass="App\Repository\CommentFileRepository")
 */
class CommentFile
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;
    /**
     * @ORM\Column(type="string", length=255)
     */
    private $fileUrl;
    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Comment", inversedBy="files")
     * @ORM\JoinColumn(nullable=false)
     */
    private $comment;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $name;
    
    /**
     * @ORM\Column(type="string", length=255)
     */
    
    private $mimeType;
    
    public function getMimeType()
    {
        return $this->mimeType;
    }
    public function setMimeType()
    {
        return $this->mimeType;
    }
    
    public function getId()
    {
        return $this->id;
    }
    public function getFileUrl(): ?string
    {
        return $this->fileUrl;
    }
    public function setFileUrl(string $fileUrl): self
    {
        $this->fileUrl = $fileUrl;
        return $this;
    }
    public function getComment(): ?Comment
    {
        return $this->comment;
    }
    public function setComment(?Comment $comment): self
    {
        $this->comment = $comment;
        return $this;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
    }
}