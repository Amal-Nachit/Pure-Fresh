<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240906131701 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE pure_annonce (id INT AUTO_INCREMENT NOT NULL, pure_user_id INT NOT NULL, titre VARCHAR(255) DEFAULT NULL, quantite INT NOT NULL, description LONGTEXT NOT NULL, disponibilite TINYINT(1) NOT NULL, date_publication DATETIME NOT NULL, date_expiration DATETIME NOT NULL, approuve TINYINT(1) NOT NULL, INDEX IDX_D39D859582A6C5E5 (pure_user_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE pure_categorie (id INT AUTO_INCREMENT NOT NULL, nom VARCHAR(255) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE pure_commande (id INT AUTO_INCREMENT NOT NULL, pure_user_id INT NOT NULL, date_commande VARCHAR(255) NOT NULL, total NUMERIC(10, 2) NOT NULL, INDEX IDX_3ECA1F5782A6C5E5 (pure_user_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE pure_details_commande (id INT AUTO_INCREMENT NOT NULL, quantite INT NOT NULL, prix NUMERIC(10, 2) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE pure_produit (id INT AUTO_INCREMENT NOT NULL, annonce_id INT DEFAULT NULL, categorie_id INT DEFAULT NULL, pure_user_id INT NOT NULL, nom VARCHAR(255) NOT NULL, description LONGTEXT NOT NULL, date_creation DATETIME NOT NULL, image VARCHAR(255) NOT NULL, prix NUMERIC(5, 2) NOT NULL, UNIQUE INDEX UNIQ_C6DFA578805AB2F (annonce_id), UNIQUE INDEX UNIQ_C6DFA57BCF5E72D (categorie_id), INDEX IDX_C6DFA5782A6C5E5 (pure_user_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE pure_statut (id INT AUTO_INCREMENT NOT NULL, pure_commande_id INT NOT NULL, INDEX IDX_42A0200DB959D80B (pure_commande_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE pure_user (id INT AUTO_INCREMENT NOT NULL, email VARCHAR(180) NOT NULL, roles JSON NOT NULL, password VARCHAR(255) NOT NULL, prenom VARCHAR(255) NOT NULL, nom VARCHAR(255) NOT NULL, telephone VARCHAR(255) NOT NULL, adresse VARCHAR(255) NOT NULL, rgpd DATETIME NOT NULL, UNIQUE INDEX UNIQ_IDENTIFIER_EMAIL (email), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE messenger_messages (id BIGINT AUTO_INCREMENT NOT NULL, body LONGTEXT NOT NULL, headers LONGTEXT NOT NULL, queue_name VARCHAR(190) NOT NULL, created_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', available_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', delivered_at DATETIME DEFAULT NULL COMMENT \'(DC2Type:datetime_immutable)\', INDEX IDX_75EA56E0FB7336F0 (queue_name), INDEX IDX_75EA56E0E3BD61CE (available_at), INDEX IDX_75EA56E016BA31DB (delivered_at), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE pure_annonce ADD CONSTRAINT FK_D39D859582A6C5E5 FOREIGN KEY (pure_user_id) REFERENCES pure_user (id)');
        $this->addSql('ALTER TABLE pure_commande ADD CONSTRAINT FK_3ECA1F5782A6C5E5 FOREIGN KEY (pure_user_id) REFERENCES pure_user (id)');
        $this->addSql('ALTER TABLE pure_produit ADD CONSTRAINT FK_C6DFA578805AB2F FOREIGN KEY (annonce_id) REFERENCES pure_annonce (id)');
        $this->addSql('ALTER TABLE pure_produit ADD CONSTRAINT FK_C6DFA57BCF5E72D FOREIGN KEY (categorie_id) REFERENCES pure_categorie (id)');
        $this->addSql('ALTER TABLE pure_produit ADD CONSTRAINT FK_C6DFA5782A6C5E5 FOREIGN KEY (pure_user_id) REFERENCES pure_user (id)');
        $this->addSql('ALTER TABLE pure_statut ADD CONSTRAINT FK_42A0200DB959D80B FOREIGN KEY (pure_commande_id) REFERENCES pure_commande (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE pure_annonce DROP FOREIGN KEY FK_D39D859582A6C5E5');
        $this->addSql('ALTER TABLE pure_commande DROP FOREIGN KEY FK_3ECA1F5782A6C5E5');
        $this->addSql('ALTER TABLE pure_produit DROP FOREIGN KEY FK_C6DFA578805AB2F');
        $this->addSql('ALTER TABLE pure_produit DROP FOREIGN KEY FK_C6DFA57BCF5E72D');
        $this->addSql('ALTER TABLE pure_produit DROP FOREIGN KEY FK_C6DFA5782A6C5E5');
        $this->addSql('ALTER TABLE pure_statut DROP FOREIGN KEY FK_42A0200DB959D80B');
        $this->addSql('DROP TABLE pure_annonce');
        $this->addSql('DROP TABLE pure_categorie');
        $this->addSql('DROP TABLE pure_commande');
        $this->addSql('DROP TABLE pure_details_commande');
        $this->addSql('DROP TABLE pure_produit');
        $this->addSql('DROP TABLE pure_statut');
        $this->addSql('DROP TABLE pure_user');
        $this->addSql('DROP TABLE messenger_messages');
    }
}
