<?php
declare(strict_types=1);
namespace App\Entity;
ini_set('display_errors', "1");
ini_set('display_startup_errors', "1");
error_reporting(E_ALL);
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 */
class Agent extends User
{
    protected int $reopen = 0;

    /**
     * @ORM\OneToMany(targetEntity=Ticket::class, mappedBy="agent")
     */
    protected Collection $tickets;

    /**
     * Agent constructor.
     */
    public function __construct()
    {
        $this->setRoles(["ROLE_AGENT"]);
        $this->tickets = new ArrayCollection();
    }

    /**
     * @return Collection|Ticket[]
     */
    public function getTickets(): Collection
    {
        return $this->tickets;
    }

    public function addTicket(Ticket $ticket): self
    {
        if (!$this->tickets->contains($ticket)) {
            $this->tickets[] = $ticket;
            $ticket->setAgent($this);
        }

        return $this;
    }

    public function removeTicket(Ticket $ticket): self
    {
        if ($this->tickets->contains($ticket)) {
            $this->tickets->removeElement($ticket);
            // set the owning side to null (unless already changed)
            if ($ticket->getAgent() === $this) {
                $ticket->setAgent(null);
            }
        }

        return $this;
    }
}