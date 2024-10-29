<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20241023063704 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE stock DROP FOREIGN KEY FK_4B3656604ACC9A20');
        $this->addSql('DROP INDEX UNIQ_4B3656604ACC9A20 ON stock');
        $this->addSql('ALTER TABLE stock CHANGE card_id card_set_id INT NOT NULL');
        $this->addSql('ALTER TABLE stock ADD CONSTRAINT FK_4B36566062C45E6C FOREIGN KEY (card_set_id) REFERENCES card_set (id)');
        $this->addSql('CREATE INDEX IDX_4B36566062C45E6C ON stock (card_set_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE stock DROP FOREIGN KEY FK_4B36566062C45E6C');
        $this->addSql('DROP INDEX IDX_4B36566062C45E6C ON stock');
        $this->addSql('ALTER TABLE stock CHANGE card_set_id card_id INT NOT NULL');
        $this->addSql('ALTER TABLE stock ADD CONSTRAINT FK_4B3656604ACC9A20 FOREIGN KEY (card_id) REFERENCES card (id) ON UPDATE NO ACTION ON DELETE NO ACTION');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_4B3656604ACC9A20 ON stock (card_id)');
    }
}
