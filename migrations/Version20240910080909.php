<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240910080909 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE pure_annonce CHANGE disponibilite disponible TINYINT(1) NOT NULL, CHANGE date_expiration duree_disponibilite DATETIME NOT NULL');
        $this->addSql('ALTER TABLE pure_produit DROP INDEX UNIQ_C6DFA578805AB2F, ADD INDEX IDX_C6DFA578805AB2F (annonce_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE pure_annonce CHANGE disponible disponibilite TINYINT(1) NOT NULL, CHANGE duree_disponibilite date_expiration DATETIME NOT NULL');
        $this->addSql('ALTER TABLE pure_produit DROP INDEX IDX_C6DFA578805AB2F, ADD UNIQUE INDEX UNIQ_C6DFA578805AB2F (annonce_id)');
    }
}
