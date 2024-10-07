<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240926123648 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        // $this->addSql('ALTER TABLE pure_commande ADD CONSTRAINT FK_3ECA1F5782A6C5E5 FOREIGN KEY (pure_user_id) REFERENCES pure_user (id)');
        // $this->addSql('CREATE INDEX IDX_3ECA1F5782A6C5E5 ON pure_commande (pure_user_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE pure_commande DROP FOREIGN KEY FK_3ECA1F5782A6C5E5');
        $this->addSql('DROP INDEX IDX_3ECA1F5782A6C5E5 ON pure_commande');
        $this->addSql('ALTER TABLE pure_commande DROP pure_user_id');
    }
}
