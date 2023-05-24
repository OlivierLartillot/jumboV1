<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230522161303 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE transfer DROP FOREIGN KEY FK_4034A3C01E4E2652');
        $this->addSql('DROP INDEX IDX_4034A3C01E4E2652 ON transfer');
        $this->addSql('ALTER TABLE transfer CHANGE custommer_card_id customer_card_id INT NOT NULL');
        $this->addSql('ALTER TABLE transfer ADD CONSTRAINT FK_4034A3C056113E9E FOREIGN KEY (customer_card_id) REFERENCES customer_card (id)');
        $this->addSql('CREATE INDEX IDX_4034A3C056113E9E ON transfer (customer_card_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE transfer DROP FOREIGN KEY FK_4034A3C056113E9E');
        $this->addSql('DROP INDEX IDX_4034A3C056113E9E ON transfer');
        $this->addSql('ALTER TABLE transfer CHANGE customer_card_id custommer_card_id INT NOT NULL');
        $this->addSql('ALTER TABLE transfer ADD CONSTRAINT FK_4034A3C01E4E2652 FOREIGN KEY (custommer_card_id) REFERENCES customer_card (id)');
        $this->addSql('CREATE INDEX IDX_4034A3C01E4E2652 ON transfer (custommer_card_id)');
    }
}
