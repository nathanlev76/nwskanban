<?php

namespace App\Entity;

use App\Repository\TaskListRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: TaskListRepository::class)]
class TaskList
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $libelle = null;

    #[ORM\OneToMany(mappedBy: 'task_list', targetEntity: Task::class)]
    private Collection $task_list;

    #[ORM\Column]
    private ?int $board_id = null;

    public function __construct()
    {
        $this->task_list = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getLibelle(): ?string
    {
        return $this->libelle;
    }

    public function setLibelle(string $libelle): self
    {
        $this->libelle = $libelle;

        return $this;
    }

    /**
     * @return Collection<int, Task>
     */
    public function getTaskList(): Collection
    {
        return $this->task_list;
    }

    public function addTaskList(Task $taskList): self
    {
        if (!$this->task_list->contains($taskList)) {
            $this->task_list->add($taskList);
            $taskList->setTaskList($this);
        }

        return $this;
    }

    public function removeTaskList(Task $taskList): self
    {
        if ($this->task_list->removeElement($taskList)) {
            // set the owning side to null (unless already changed)
            if ($taskList->getTaskList() === $this) {
                $taskList->setTaskList(null);
            }
        }

        return $this;
    }

    public function getBoardId(): ?int
    {
        return $this->board_id;
    }

    public function setBoardId(int $board_id): self
    {
        $this->board_id = $board_id;

        return $this;
    }
}
