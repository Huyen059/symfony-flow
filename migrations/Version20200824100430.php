<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20200824100430 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE ticket (id INT AUTO_INCREMENT NOT NULL, customer_id INT NOT NULL, agent_id INT DEFAULT NULL, title VARCHAR(255) NOT NULL, content VARCHAR(255) NOT NULL, status INT NOT NULL, comments LONGTEXT DEFAULT NULL COMMENT \'(DC2Type:array)\', INDEX IDX_97A0ADA39395C3F3 (customer_id), INDEX IDX_97A0ADA33414710B (agent_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE user (id INT AUTO_INCREMENT NOT NULL, email VARCHAR(180) NOT NULL, roles JSON NOT NULL, password VARCHAR(255) NOT NULL, discr VARCHAR(255) NOT NULL, UNIQUE INDEX UNIQ_8D93D649E7927C74 (email), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE ticket ADD CONSTRAINT FK_97A0ADA39395C3F3 FOREIGN KEY (customer_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE ticket ADD CONSTRAINT FK_97A0ADA33414710B FOREIGN KEY (agent_id) REFERENCES user (id)');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE ticket DROP FOREIGN KEY FK_97A0ADA39395C3F3');
        $this->addSql('ALTER TABLE ticket DROP FOREIGN KEY FK_97A0ADA33414710B');
        $this->addSql('DROP TABLE ticket');
        $this->addSql('DROP TABLE user');
    }
}
