<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240923104320 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE pure_produit DROP FOREIGN KEY FK_C6DFA5782A6C5E5');
        $this->addSql('ALTER TABLE pure_produit DROP FOREIGN KEY FK_C6DFA578805AB2F');
        $this->addSql('ALTER TABLE pure_produit DROP FOREIGN KEY FK_C6DFA57BCF5E72D');
        $this->addSql('DROP TABLE pure_produit');
        $this->addSql('ALTER TABLE pure_annonce ADD categorie_id INT DEFAULT NULL, ADD nom VARCHAR(255) NOT NULL, ADD image VARCHAR(255) NOT NULL, ADD prix NUMERIC(5, 2) NOT NULL, DROP titre, CHANGE date_publication date_creation DATETIME NOT NULL');
        $this->addSql('ALTER TABLE pure_annonce ADD CONSTRAINT FK_D39D8595BCF5E72D FOREIGN KEY (categorie_id) REFERENCES pure_categorie (id)');
        $this->addSql('CREATE INDEX IDX_D39D8595BCF5E72D ON pure_annonce (categorie_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE pure_produit (id INT AUTO_INCREMENT NOT NULL, annonce_id INT DEFAULT NULL, categorie_id INT DEFAULT NULL, pure_user_id INT NOT NULL, nom VARCHAR(255) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci`, description LONGTEXT CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci`, date_creation DATETIME NOT NULL, image VARCHAR(255) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci`, prix NUMERIC(5, 2) NOT NULL, INDEX IDX_C6DFA5782A6C5E5 (pure_user_id), INDEX IDX_C6DFA578805AB2F (annonce_id), INDEX IDX_C6DFA57BCF5E72D (categorie_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB COMMENT = \'\' ');
        $this->addSql('ALTER TABLE pure_produit ADD CONSTRAINT FK_C6DFA5782A6C5E5 FOREIGN KEY (pure_user_id) REFERENCES pure_user (id) ON UPDATE NO ACTION ON DELETE NO ACTION');
        $this->addSql('ALTER TABLE pure_produit ADD CONSTRAINT FK_C6DFA578805AB2F FOREIGN KEY (annonce_id) REFERENCES pure_annonce (id) ON UPDATE NO ACTION ON DELETE NO ACTION');
        $this->addSql('ALTER TABLE pure_produit ADD CONSTRAINT FK_C6DFA57BCF5E72D FOREIGN KEY (categorie_id) REFERENCES pure_categorie (id) ON UPDATE NO ACTION ON DELETE NO ACTION');
        $this->addSql('ALTER TABLE pure_annonce DROP FOREIGN KEY FK_D39D8595BCF5E72D');
        $this->addSql('DROP INDEX IDX_D39D8595BCF5E72D ON pure_annonce');
        $this->addSql('ALTER TABLE pure_annonce ADD titre VARCHAR(255) DEFAULT NULL, DROP categorie_id, DROP nom, DROP image, DROP prix, CHANGE date_creation date_publication DATETIME NOT NULL');
    }
}
