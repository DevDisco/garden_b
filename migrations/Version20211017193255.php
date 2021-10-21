<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20211017193255 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE article (id INT AUTO_INCREMENT NOT NULL, title TINYTEXT NOT NULL, body LONGTEXT NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE garden (id INT AUTO_INCREMENT NOT NULL, address VARCHAR(64) NOT NULL, zip VARCHAR(7) NOT NULL, municipality VARCHAR(64) NOT NULL, intro VARCHAR(255) NOT NULL, description LONGTEXT NOT NULL, size SMALLINT NOT NULL, anno SMALLINT NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('DROP TABLE garden_example');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE garden_example (id TINYINT(1) NOT NULL, address VARCHAR(64) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_general_ci`, zip VARCHAR(7) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_general_ci`, municipality VARCHAR(32) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_general_ci`, intro VARCHAR(256) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_general_ci`, description TEXT CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_general_ci`, size TINYINT(1) NOT NULL, anno TINYINT(1) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE `utf8_unicode_ci` ENGINE = InnoDB COMMENT = \'\' ');
        $this->addSql('DROP TABLE article');
        $this->addSql('DROP TABLE garden');
    }
}
