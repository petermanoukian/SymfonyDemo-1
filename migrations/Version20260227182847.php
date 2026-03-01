<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20260227182847 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE subcat (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(255) NOT NULL, des LONGTEXT DEFAULT NULL, dess LONGTEXT DEFAULT NULL, img VARCHAR(255) DEFAULT NULL, img2 VARCHAR(255) DEFAULT NULL, filer VARCHAR(255) DEFAULT NULL, catid INT NOT NULL, INDEX IDX_FD7614413632DFC5 (catid), UNIQUE INDEX uniq_subcat_cat_name (catid, name), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('ALTER TABLE subcat ADD CONSTRAINT FK_FD7614413632DFC5 FOREIGN KEY (catid) REFERENCES cat (id) ON DELETE CASCADE');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE subcat DROP FOREIGN KEY FK_FD7614413632DFC5');
        $this->addSql('DROP TABLE subcat');
    }
}
