<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration,
    Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your need!
 */
class Version20121027141527 extends AbstractMigration
{
    public function up(Schema $schema)
    {
        // this up() migration is autogenerated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != "mysql");
        
        $this->addSql("ALTER TABLE Game DROP FOREIGN KEY FK_83199EB2FCD3229D");
        $this->addSql("DROP INDEX IDX_83199EB2FCD3229D ON Game");
        $this->addSql("ALTER TABLE Game ADD murdererPhoto_id VARCHAR(255) DEFAULT NULL, DROP murderer_id");
        $this->addSql("ALTER TABLE Game ADD CONSTRAINT FK_83199EB231CE0272 FOREIGN KEY (murdererPhoto_id) REFERENCES Photo(id)");
        $this->addSql("CREATE INDEX IDX_83199EB231CE0272 ON Game (murdererPhoto_id)");
    }

    public function down(Schema $schema)
    {
        // this down() migration is autogenerated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != "mysql");
        
        $this->addSql("ALTER TABLE Game DROP FOREIGN KEY FK_83199EB231CE0272");
        $this->addSql("DROP INDEX IDX_83199EB231CE0272 ON Game");
        $this->addSql("ALTER TABLE Game ADD murderer_id INT DEFAULT NULL, DROP murdererPhoto_id");
        $this->addSql("ALTER TABLE Game ADD CONSTRAINT FK_83199EB2FCD3229D FOREIGN KEY (murderer_id) REFERENCES player(id)");
        $this->addSql("CREATE INDEX IDX_83199EB2FCD3229D ON Game (murderer_id)");
    }
}
