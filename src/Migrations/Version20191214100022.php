<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20191214100022 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE evaluation (id INT AUTO_INCREMENT NOT NULL, formation_id INT NOT NULL, user_id INT DEFAULT NULL, description VARCHAR(255) NOT NULL, date DATETIME NOT NULL, INDEX IDX_1323A5755200282E (formation_id), INDEX IDX_1323A575A76ED395 (user_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE formateur (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(255) NOT NULL, field VARCHAR(255) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE formation (id INT AUTO_INCREMENT NOT NULL, formateur_id INT DEFAULT NULL, designation VARCHAR(255) NOT NULL, description LONGTEXT NOT NULL, image LONGTEXT NOT NULL, start_date DATETIME NOT NULL, end_date DATETIME DEFAULT NULL, state INT NOT NULL, INDEX IDX_404021BF155D8F51 (formateur_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE formation_student (formation_id INT NOT NULL, student_id INT NOT NULL, INDEX IDX_F2DCEA485200282E (formation_id), INDEX IDX_F2DCEA48CB944F1A (student_id), PRIMARY KEY(formation_id, student_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE formation_topic (formation_id INT NOT NULL, topic_id INT NOT NULL, INDEX IDX_EBCB406E5200282E (formation_id), INDEX IDX_EBCB406E1F55203D (topic_id), PRIMARY KEY(formation_id, topic_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE personne (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(255) NOT NULL, firstname VARCHAR(255) NOT NULL, age INT NOT NULL, cin INT NOT NULL, path VARCHAR(255) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE student (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(255) NOT NULL, statut VARCHAR(255) NOT NULL, etablissement VARCHAR(255) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE topic (id INT AUTO_INCREMENT NOT NULL, designation VARCHAR(255) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE user (id INT AUTO_INCREMENT NOT NULL, formateur_id INT DEFAULT NULL, student_id INT DEFAULT NULL, email VARCHAR(180) NOT NULL, roles Text NOT NULL, password VARCHAR(255) NOT NULL, UNIQUE INDEX UNIQ_8D93D649E7927C74 (email), UNIQUE INDEX UNIQ_8D93D649155D8F51 (formateur_id), UNIQUE INDEX UNIQ_8D93D649CB944F1A (student_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('ALTER TABLE evaluation ADD CONSTRAINT FK_1323A5755200282E FOREIGN KEY (formation_id) REFERENCES formation (id)');
        $this->addSql('ALTER TABLE evaluation ADD CONSTRAINT FK_1323A575A76ED395 FOREIGN KEY (user_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE formation ADD CONSTRAINT FK_404021BF155D8F51 FOREIGN KEY (formateur_id) REFERENCES formateur (id)');
        $this->addSql('ALTER TABLE formation_student ADD CONSTRAINT FK_F2DCEA485200282E FOREIGN KEY (formation_id) REFERENCES formation (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE formation_student ADD CONSTRAINT FK_F2DCEA48CB944F1A FOREIGN KEY (student_id) REFERENCES student (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE formation_topic ADD CONSTRAINT FK_EBCB406E5200282E FOREIGN KEY (formation_id) REFERENCES formation (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE formation_topic ADD CONSTRAINT FK_EBCB406E1F55203D FOREIGN KEY (topic_id) REFERENCES topic (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE user ADD CONSTRAINT FK_8D93D649155D8F51 FOREIGN KEY (formateur_id) REFERENCES formateur (id)');
        $this->addSql('ALTER TABLE user ADD CONSTRAINT FK_8D93D649CB944F1A FOREIGN KEY (student_id) REFERENCES student (id)');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE formation DROP FOREIGN KEY FK_404021BF155D8F51');
        $this->addSql('ALTER TABLE user DROP FOREIGN KEY FK_8D93D649155D8F51');
        $this->addSql('ALTER TABLE evaluation DROP FOREIGN KEY FK_1323A5755200282E');
        $this->addSql('ALTER TABLE formation_student DROP FOREIGN KEY FK_F2DCEA485200282E');
        $this->addSql('ALTER TABLE formation_topic DROP FOREIGN KEY FK_EBCB406E5200282E');
        $this->addSql('ALTER TABLE formation_student DROP FOREIGN KEY FK_F2DCEA48CB944F1A');
        $this->addSql('ALTER TABLE user DROP FOREIGN KEY FK_8D93D649CB944F1A');
        $this->addSql('ALTER TABLE formation_topic DROP FOREIGN KEY FK_EBCB406E1F55203D');
        $this->addSql('ALTER TABLE evaluation DROP FOREIGN KEY FK_1323A575A76ED395');
        $this->addSql('DROP TABLE evaluation');
        $this->addSql('DROP TABLE formateur');
        $this->addSql('DROP TABLE formation');
        $this->addSql('DROP TABLE formation_student');
        $this->addSql('DROP TABLE formation_topic');
        $this->addSql('DROP TABLE personne');
        $this->addSql('DROP TABLE student');
        $this->addSql('DROP TABLE topic');
        $this->addSql('DROP TABLE user');
    }
}
