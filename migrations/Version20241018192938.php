<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20241018192938 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE pure_annonce (id INT AUTO_INCREMENT NOT NULL, categorie_id INT DEFAULT NULL, pure_user_id INT NOT NULL, nom VARCHAR(255) NOT NULL, slug VARCHAR(255) NOT NULL, description LONGTEXT NOT NULL, date_creation DATETIME NOT NULL, image VARCHAR(255) DEFAULT NULL, prix NUMERIC(5, 2) NOT NULL, approuvee TINYINT(1) DEFAULT NULL, UNIQUE INDEX UNIQ_D39D8595989D9B62 (slug), INDEX IDX_D39D8595BCF5E72D (categorie_id), INDEX IDX_D39D859582A6C5E5 (pure_user_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE pure_categorie (id INT AUTO_INCREMENT NOT NULL, nom VARCHAR(255) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE pure_commande (id INT AUTO_INCREMENT NOT NULL, statut_id INT NOT NULL, pure_annonce_id INT DEFAULT NULL, pure_user_id INT DEFAULT NULL, date_commande DATETIME NOT NULL, quantite INT NOT NULL, total NUMERIC(10, 2) NOT NULL, INDEX IDX_3ECA1F57F6203804 (statut_id), INDEX IDX_3ECA1F5755D33C0F (pure_annonce_id), INDEX IDX_3ECA1F5782A6C5E5 (pure_user_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE pure_statut (id INT AUTO_INCREMENT NOT NULL, intitule VARCHAR(255) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE pure_user (id INT AUTO_INCREMENT NOT NULL, email VARCHAR(180) NOT NULL, roles JSON NOT NULL, password VARCHAR(255) NOT NULL, prenom VARCHAR(255) NOT NULL, nom VARCHAR(255) NOT NULL, telephone VARCHAR(255) NOT NULL, adresse VARCHAR(255) NOT NULL, rgpd DATETIME NOT NULL, is_verified TINYINT(1) NOT NULL, UNIQUE INDEX UNIQ_IDENTIFIER_EMAIL (email), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE reset_password_request (id INT AUTO_INCREMENT NOT NULL, user_id INT NOT NULL, selector VARCHAR(20) NOT NULL, hashed_token VARCHAR(100) NOT NULL, requested_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', expires_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', INDEX IDX_7CE748AA76ED395 (user_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE messenger_messages (id BIGINT AUTO_INCREMENT NOT NULL, body LONGTEXT NOT NULL, headers LONGTEXT NOT NULL, queue_name VARCHAR(190) NOT NULL, created_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', available_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', delivered_at DATETIME DEFAULT NULL COMMENT \'(DC2Type:datetime_immutable)\', INDEX IDX_75EA56E0FB7336F0 (queue_name), INDEX IDX_75EA56E0E3BD61CE (available_at), INDEX IDX_75EA56E016BA31DB (delivered_at), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE pure_annonce ADD CONSTRAINT FK_D39D8595BCF5E72D FOREIGN KEY (categorie_id) REFERENCES pure_categorie (id)');
        $this->addSql('ALTER TABLE pure_annonce ADD CONSTRAINT FK_D39D859582A6C5E5 FOREIGN KEY (pure_user_id) REFERENCES pure_user (id)');
        $this->addSql('ALTER TABLE pure_commande ADD CONSTRAINT FK_3ECA1F57F6203804 FOREIGN KEY (statut_id) REFERENCES pure_statut (id)');
        $this->addSql('ALTER TABLE pure_commande ADD CONSTRAINT FK_3ECA1F5755D33C0F FOREIGN KEY (pure_annonce_id) REFERENCES pure_annonce (id)');
        $this->addSql('ALTER TABLE pure_commande ADD CONSTRAINT FK_3ECA1F5782A6C5E5 FOREIGN KEY (pure_user_id) REFERENCES pure_user (id)');
        $this->addSql('ALTER TABLE reset_password_request ADD CONSTRAINT FK_7CE748AA76ED395 FOREIGN KEY (user_id) REFERENCES pure_user (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE pure_annonce DROP FOREIGN KEY FK_D39D8595BCF5E72D');
        $this->addSql('ALTER TABLE pure_annonce DROP FOREIGN KEY FK_D39D859582A6C5E5');
        $this->addSql('ALTER TABLE pure_commande DROP FOREIGN KEY FK_3ECA1F57F6203804');
        $this->addSql('ALTER TABLE pure_commande DROP FOREIGN KEY FK_3ECA1F5755D33C0F');
        $this->addSql('ALTER TABLE pure_commande DROP FOREIGN KEY FK_3ECA1F5782A6C5E5');
        $this->addSql('ALTER TABLE reset_password_request DROP FOREIGN KEY FK_7CE748AA76ED395');
        $this->addSql('DROP TABLE pure_annonce');
        $this->addSql('DROP TABLE pure_categorie');
        $this->addSql('DROP TABLE pure_commande');
        $this->addSql('DROP TABLE pure_statut');
        $this->addSql('DROP TABLE pure_user');
        $this->addSql('DROP TABLE reset_password_request');
        $this->addSql('DROP TABLE messenger_messages');
    }
}
