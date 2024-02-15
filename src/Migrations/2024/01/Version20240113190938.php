<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240113190938 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE adresse (id INT AUTO_INCREMENT NOT NULL, cree_par_id INT DEFAULT NULL, modifie_par_id INT DEFAULT NULL, rue VARCHAR(255) NOT NULL, codepostal VARCHAR(255) NOT NULL, pays VARCHAR(255) NOT NULL, ville VARCHAR(255) NOT NULL, date_creation DATETIME DEFAULT NULL, date_modification DATETIME DEFAULT NULL, INDEX IDX_C35F0816FC29C013 (cree_par_id), INDEX IDX_C35F0816553B2554 (modifie_par_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE document (id INT AUTO_INCREMENT NOT NULL, cree_par_id INT DEFAULT NULL, modifie_par_id INT DEFAULT NULL, nom VARCHAR(512) NOT NULL, path VARCHAR(1024) NOT NULL, brouillon TINYINT(1) NOT NULL, date_creation DATETIME DEFAULT NULL, date_modification DATETIME DEFAULT NULL, discr VARCHAR(255) NOT NULL, INDEX IDX_D8698A76FC29C013 (cree_par_id), INDEX IDX_D8698A76553B2554 (modifie_par_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE document_accueil (id INT AUTO_INCREMENT NOT NULL, document_id INT NOT NULL, cree_par_id INT DEFAULT NULL, modifie_par_id INT DEFAULT NULL, date_creation DATETIME DEFAULT NULL, date_modification DATETIME DEFAULT NULL, UNIQUE INDEX UNIQ_A6F6EA62C33F7837 (document_id), INDEX IDX_A6F6EA62FC29C013 (cree_par_id), INDEX IDX_A6F6EA62553B2554 (modifie_par_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE message (id INT AUTO_INCREMENT NOT NULL, destinataire INT NOT NULL, expediteur INT NOT NULL, cree_par_id INT DEFAULT NULL, modifie_par_id INT DEFAULT NULL, objet VARCHAR(255) NOT NULL, contenu LONGTEXT NOT NULL, favoris TINYINT(1) NOT NULL, lu TINYINT(1) NOT NULL, date_lecture DATETIME DEFAULT NULL, date_envoi DATETIME NOT NULL, supprime TINYINT(1) NOT NULL, date_creation DATETIME DEFAULT NULL, date_modification DATETIME DEFAULT NULL, INDEX IDX_B6BD307FFEA9FF92 (destinataire), INDEX IDX_B6BD307FABA4CF8E (expediteur), INDEX IDX_B6BD307FFC29C013 (cree_par_id), INDEX IDX_B6BD307F553B2554 (modifie_par_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE message_document (message_id INT NOT NULL, document_id INT NOT NULL, INDEX IDX_D14F4E67537A1329 (message_id), INDEX IDX_D14F4E67C33F7837 (document_id), PRIMARY KEY(message_id, document_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE utilisateur (id INT AUTO_INCREMENT NOT NULL, cree_par_id INT DEFAULT NULL, modifie_par_id INT DEFAULT NULL, nom VARCHAR(255) NOT NULL, prenom VARCHAR(255) NOT NULL, civilite VARCHAR(255) NOT NULL, nb_connexion_KO INT DEFAULT NULL, email VARCHAR(70) NOT NULL, email_canonical VARCHAR(70) DEFAULT NULL, username_canonical VARCHAR(255) DEFAULT NULL, roles JSON NOT NULL, password VARCHAR(255) NOT NULL, salt VARCHAR(255) DEFAULT NULL, locked TINYINT(1) NOT NULL, enabled TINYINT(1) NOT NULL, expired TINYINT(1) NOT NULL, expires_at DATETIME DEFAULT NULL, confirmation_token VARCHAR(255) DEFAULT NULL, password_requested_at DATETIME DEFAULT NULL, recevoir_notifications_goldy TINYINT(1) NOT NULL, force_change_password TINYINT(1) DEFAULT NULL, date_creation DATETIME DEFAULT NULL, date_modification DATETIME DEFAULT NULL, UNIQUE INDEX UNIQ_1D1C63B3E7927C74 (email), UNIQUE INDEX UNIQ_1D1C63B3A0D96FBF (email_canonical), UNIQUE INDEX UNIQ_1D1C63B392FC23A8 (username_canonical), INDEX IDX_1D1C63B3FC29C013 (cree_par_id), INDEX IDX_1D1C63B3553B2554 (modifie_par_id), FULLTEXT INDEX IDX_1D1C63B36C6E55B5A625945B (nom, prenom), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE messenger_messages (id BIGINT AUTO_INCREMENT NOT NULL, body LONGTEXT NOT NULL, headers LONGTEXT NOT NULL, queue_name VARCHAR(190) NOT NULL, created_at DATETIME NOT NULL, available_at DATETIME NOT NULL, delivered_at DATETIME DEFAULT NULL, INDEX IDX_75EA56E0FB7336F0 (queue_name), INDEX IDX_75EA56E0E3BD61CE (available_at), INDEX IDX_75EA56E016BA31DB (delivered_at), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE adresse ADD CONSTRAINT FK_C35F0816FC29C013 FOREIGN KEY (cree_par_id) REFERENCES utilisateur (id)');
        $this->addSql('ALTER TABLE adresse ADD CONSTRAINT FK_C35F0816553B2554 FOREIGN KEY (modifie_par_id) REFERENCES utilisateur (id)');
        $this->addSql('ALTER TABLE document ADD CONSTRAINT FK_D8698A76FC29C013 FOREIGN KEY (cree_par_id) REFERENCES utilisateur (id)');
        $this->addSql('ALTER TABLE document ADD CONSTRAINT FK_D8698A76553B2554 FOREIGN KEY (modifie_par_id) REFERENCES utilisateur (id)');
        $this->addSql('ALTER TABLE document_accueil ADD CONSTRAINT FK_A6F6EA62C33F7837 FOREIGN KEY (document_id) REFERENCES document (id)');
        $this->addSql('ALTER TABLE document_accueil ADD CONSTRAINT FK_A6F6EA62FC29C013 FOREIGN KEY (cree_par_id) REFERENCES utilisateur (id)');
        $this->addSql('ALTER TABLE document_accueil ADD CONSTRAINT FK_A6F6EA62553B2554 FOREIGN KEY (modifie_par_id) REFERENCES utilisateur (id)');
        $this->addSql('ALTER TABLE message ADD CONSTRAINT FK_B6BD307FFEA9FF92 FOREIGN KEY (destinataire) REFERENCES utilisateur (id)');
        $this->addSql('ALTER TABLE message ADD CONSTRAINT FK_B6BD307FABA4CF8E FOREIGN KEY (expediteur) REFERENCES utilisateur (id)');
        $this->addSql('ALTER TABLE message ADD CONSTRAINT FK_B6BD307FFC29C013 FOREIGN KEY (cree_par_id) REFERENCES utilisateur (id)');
        $this->addSql('ALTER TABLE message ADD CONSTRAINT FK_B6BD307F553B2554 FOREIGN KEY (modifie_par_id) REFERENCES utilisateur (id)');
        $this->addSql('ALTER TABLE message_document ADD CONSTRAINT FK_D14F4E67537A1329 FOREIGN KEY (message_id) REFERENCES message (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE message_document ADD CONSTRAINT FK_D14F4E67C33F7837 FOREIGN KEY (document_id) REFERENCES document (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE utilisateur ADD CONSTRAINT FK_1D1C63B3FC29C013 FOREIGN KEY (cree_par_id) REFERENCES utilisateur (id)');
        $this->addSql('ALTER TABLE utilisateur ADD CONSTRAINT FK_1D1C63B3553B2554 FOREIGN KEY (modifie_par_id) REFERENCES utilisateur (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE adresse DROP FOREIGN KEY FK_C35F0816FC29C013');
        $this->addSql('ALTER TABLE adresse DROP FOREIGN KEY FK_C35F0816553B2554');
        $this->addSql('ALTER TABLE document DROP FOREIGN KEY FK_D8698A76FC29C013');
        $this->addSql('ALTER TABLE document DROP FOREIGN KEY FK_D8698A76553B2554');
        $this->addSql('ALTER TABLE document_accueil DROP FOREIGN KEY FK_A6F6EA62C33F7837');
        $this->addSql('ALTER TABLE document_accueil DROP FOREIGN KEY FK_A6F6EA62FC29C013');
        $this->addSql('ALTER TABLE document_accueil DROP FOREIGN KEY FK_A6F6EA62553B2554');
        $this->addSql('ALTER TABLE message DROP FOREIGN KEY FK_B6BD307FFEA9FF92');
        $this->addSql('ALTER TABLE message DROP FOREIGN KEY FK_B6BD307FABA4CF8E');
        $this->addSql('ALTER TABLE message DROP FOREIGN KEY FK_B6BD307FFC29C013');
        $this->addSql('ALTER TABLE message DROP FOREIGN KEY FK_B6BD307F553B2554');
        $this->addSql('ALTER TABLE message_document DROP FOREIGN KEY FK_D14F4E67537A1329');
        $this->addSql('ALTER TABLE message_document DROP FOREIGN KEY FK_D14F4E67C33F7837');
        $this->addSql('ALTER TABLE utilisateur DROP FOREIGN KEY FK_1D1C63B3FC29C013');
        $this->addSql('ALTER TABLE utilisateur DROP FOREIGN KEY FK_1D1C63B3553B2554');
        $this->addSql('DROP TABLE adresse');
        $this->addSql('DROP TABLE document');
        $this->addSql('DROP TABLE document_accueil');
        $this->addSql('DROP TABLE message');
        $this->addSql('DROP TABLE message_document');
        $this->addSql('DROP TABLE utilisateur');
        $this->addSql('DROP TABLE messenger_messages');
    }
}
