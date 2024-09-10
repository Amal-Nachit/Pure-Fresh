<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240909122628 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE pure_produit DROP INDEX UNIQ_C6DFA57BCF5E72D, ADD INDEX IDX_C6DFA57BCF5E72D (categorie_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE pure_produit DROP INDEX IDX_C6DFA57BCF5E72D, ADD UNIQUE INDEX UNIQ_C6DFA57BCF5E72D (categorie_id)');
    }
}
