<?php
declare(strict_types=1);
namespace App\Entity;
use Doctrine\Common\Collections\ArrayCollection;

ini_set('display_errors', "1");
ini_set('display_startup_errors', "1");
error_reporting(E_ALL);

class SecondLineAgent extends Agent
{
    /**
     * SecondLineAgent constructor.
     */
    public function __construct()
    {
        parent::__construct();
        $this->setRoles(["ROLE_AGENT_SECOND_LINE"]);
    }
}