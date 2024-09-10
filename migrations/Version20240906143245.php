<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240906143245 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE pure_statut DROP FOREIGN KEY FK_42A0200DB959D80B');
        $this->addSql('DROP INDEX IDX_42A0200DB959D80B ON pure_statut');
        $this->addSql('ALTER TABLE pure_statut DROP pure_commande_id');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE pure_statut ADD pure_commande_id INT NOT NULL');
        $this->addSql('ALTER TABLE pure_statut ADD CONSTRAINT FK_42A0200DB959D80B FOREIGN KEY (pure_commande_id) REFERENCES pure_commande (id) ON UPDATE NO ACTION ON DELETE NO ACTION');
        $this->addSql('CREATE INDEX IDX_42A0200DB959D80B ON pure_statut (pure_commande_id)');
    }
}
