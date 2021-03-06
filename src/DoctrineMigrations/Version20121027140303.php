<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration,
    Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your need!
 */
class Version20121027140303 extends AbstractMigration
{
    public function up(Schema $schema)
    {
        // this up() migration is autogenerated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != "mysql");
        
        $this->addSql("CREATE TABLE ext_log_entries (id INT AUTO_INCREMENT NOT NULL, action VARCHAR(8) NOT NULL, logged_at DATETIME NOT NULL, object_id VARCHAR(32) DEFAULT NULL, object_class VARCHAR(255) NOT NULL, version INT NOT NULL, data LONGTEXT DEFAULT NULL COMMENT '(DC2Type:array)', username VARCHAR(255) DEFAULT NULL, INDEX log_class_lookup_idx (object_class), INDEX log_date_lookup_idx (logged_at), INDEX log_user_lookup_idx (username), PRIMARY KEY(id)) ENGINE = InnoDB");
        $this->addSql("CREATE TABLE ext_translations (id INT AUTO_INCREMENT NOT NULL, locale VARCHAR(8) NOT NULL, object_class VARCHAR(255) NOT NULL, field VARCHAR(32) NOT NULL, foreign_key VARCHAR(64) NOT NULL, content LONGTEXT DEFAULT NULL, INDEX translations_lookup_idx (locale, object_class, foreign_key), UNIQUE INDEX lookup_unique_idx (locale, object_class, foreign_key, field), PRIMARY KEY(id)) ENGINE = InnoDB");
        $this->addSql("CREATE TABLE Account (id INT AUTO_INCREMENT NOT NULL, player_id INT DEFAULT NULL, username VARCHAR(255) NOT NULL, UNIQUE INDEX UNIQ_B28B6F3899E6F5DF (player_id), PRIMARY KEY(id)) ENGINE = InnoDB");
        $this->addSql("CREATE TABLE Game (id INT AUTO_INCREMENT NOT NULL, murderer_id INT DEFAULT NULL, requiredPositiveSuspicionRate DOUBLE PRECISION NOT NULL, durationOfPreliminaryProceedingsInMinutes INT NOT NULL, finished TINYINT(1) NOT NULL, photoSet_id VARCHAR(255) DEFAULT NULL, INDEX IDX_83199EB2273EE305 (photoSet_id), INDEX IDX_83199EB2FCD3229D (murderer_id), PRIMARY KEY(id)) ENGINE = InnoDB");
        $this->addSql("CREATE TABLE MannerOfDeath (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(255) NOT NULL, PRIMARY KEY(id)) ENGINE = InnoDB");
        $this->addSql("CREATE TABLE Murder (id INT AUTO_INCREMENT NOT NULL, game_id INT DEFAULT NULL, victim_id INT DEFAULT NULL, timeOfOffense DATETIME NOT NULL, INDEX IDX_1B5F53B7E48FD905 (game_id), UNIQUE INDEX UNIQ_1B5F53B744972A0E (victim_id), PRIMARY KEY(id)) ENGINE = InnoDB");
        $this->addSql("CREATE TABLE Photo (id VARCHAR(255) NOT NULL, title VARCHAR(255) NOT NULL, url VARCHAR(255) NOT NULL, photoSet_id VARCHAR(255) DEFAULT NULL, INDEX IDX_D576AB1C273EE305 (photoSet_id), PRIMARY KEY(id)) ENGINE = InnoDB");
        $this->addSql("CREATE TABLE PhotoSet (id VARCHAR(255) NOT NULL, title VARCHAR(255) NOT NULL, PRIMARY KEY(id)) ENGINE = InnoDB");
        $this->addSql("CREATE TABLE Player (id INT AUTO_INCREMENT NOT NULL, game_id INT DEFAULT NULL, photo_id VARCHAR(255) DEFAULT NULL, mannerOfDeath_id INT DEFAULT NULL, INDEX IDX_9FB57F53E48FD905 (game_id), INDEX IDX_9FB57F537E9E4C8C (photo_id), INDEX IDX_9FB57F531E60B485 (mannerOfDeath_id), PRIMARY KEY(id)) ENGINE = InnoDB");
        $this->addSql("CREATE TABLE Suspicion (id INT AUTO_INCREMENT NOT NULL, murder_id INT DEFAULT NULL, suspect_id INT DEFAULT NULL, witness_id INT DEFAULT NULL, timestamp DATETIME NOT NULL, INDEX IDX_2D1CB7D74EE3EDEF (murder_id), INDEX IDX_2D1CB7D771812EB2 (suspect_id), INDEX IDX_2D1CB7D7F28D7E1C (witness_id), PRIMARY KEY(id)) ENGINE = InnoDB");
        $this->addSql("ALTER TABLE Account ADD CONSTRAINT FK_B28B6F3899E6F5DF FOREIGN KEY (player_id) REFERENCES Player(id) ON DELETE CASCADE");
        $this->addSql("ALTER TABLE Game ADD CONSTRAINT FK_83199EB2273EE305 FOREIGN KEY (photoSet_id) REFERENCES PhotoSet(id)");
        $this->addSql("ALTER TABLE Game ADD CONSTRAINT FK_83199EB2FCD3229D FOREIGN KEY (murderer_id) REFERENCES Player(id)");
        $this->addSql("ALTER TABLE Murder ADD CONSTRAINT FK_1B5F53B7E48FD905 FOREIGN KEY (game_id) REFERENCES Game(id)");
        $this->addSql("ALTER TABLE Murder ADD CONSTRAINT FK_1B5F53B744972A0E FOREIGN KEY (victim_id) REFERENCES Player(id) ON DELETE CASCADE");
        $this->addSql("ALTER TABLE Photo ADD CONSTRAINT FK_D576AB1C273EE305 FOREIGN KEY (photoSet_id) REFERENCES PhotoSet(id)");
        $this->addSql("ALTER TABLE Player ADD CONSTRAINT FK_9FB57F53E48FD905 FOREIGN KEY (game_id) REFERENCES Game(id)");
        $this->addSql("ALTER TABLE Player ADD CONSTRAINT FK_9FB57F537E9E4C8C FOREIGN KEY (photo_id) REFERENCES Photo(id) ON DELETE CASCADE");
        $this->addSql("ALTER TABLE Player ADD CONSTRAINT FK_9FB57F531E60B485 FOREIGN KEY (mannerOfDeath_id) REFERENCES MannerOfDeath(id) ON DELETE CASCADE");
        $this->addSql("ALTER TABLE Suspicion ADD CONSTRAINT FK_2D1CB7D74EE3EDEF FOREIGN KEY (murder_id) REFERENCES Murder(id) ON DELETE CASCADE");
        $this->addSql("ALTER TABLE Suspicion ADD CONSTRAINT FK_2D1CB7D771812EB2 FOREIGN KEY (suspect_id) REFERENCES Player(id) ON DELETE CASCADE");
        $this->addSql("ALTER TABLE Suspicion ADD CONSTRAINT FK_2D1CB7D7F28D7E1C FOREIGN KEY (witness_id) REFERENCES Player(id) ON DELETE CASCADE");
    }

    public function down(Schema $schema)
    {
        // this down() migration is autogenerated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != "mysql");
        
        $this->addSql("ALTER TABLE Murder DROP FOREIGN KEY FK_1B5F53B7E48FD905");
        $this->addSql("ALTER TABLE Player DROP FOREIGN KEY FK_9FB57F53E48FD905");
        $this->addSql("ALTER TABLE Player DROP FOREIGN KEY FK_9FB57F531E60B485");
        $this->addSql("ALTER TABLE Suspicion DROP FOREIGN KEY FK_2D1CB7D74EE3EDEF");
        $this->addSql("ALTER TABLE Player DROP FOREIGN KEY FK_9FB57F537E9E4C8C");
        $this->addSql("ALTER TABLE Game DROP FOREIGN KEY FK_83199EB2273EE305");
        $this->addSql("ALTER TABLE Photo DROP FOREIGN KEY FK_D576AB1C273EE305");
        $this->addSql("ALTER TABLE Account DROP FOREIGN KEY FK_B28B6F3899E6F5DF");
        $this->addSql("ALTER TABLE Game DROP FOREIGN KEY FK_83199EB2FCD3229D");
        $this->addSql("ALTER TABLE Murder DROP FOREIGN KEY FK_1B5F53B744972A0E");
        $this->addSql("ALTER TABLE Suspicion DROP FOREIGN KEY FK_2D1CB7D771812EB2");
        $this->addSql("ALTER TABLE Suspicion DROP FOREIGN KEY FK_2D1CB7D7F28D7E1C");
        $this->addSql("DROP TABLE ext_log_entries");
        $this->addSql("DROP TABLE ext_translations");
        $this->addSql("DROP TABLE Account");
        $this->addSql("DROP TABLE Game");
        $this->addSql("DROP TABLE MannerOfDeath");
        $this->addSql("DROP TABLE Murder");
        $this->addSql("DROP TABLE Photo");
        $this->addSql("DROP TABLE PhotoSet");
        $this->addSql("DROP TABLE Player");
        $this->addSql("DROP TABLE Suspicion");
    }
}
