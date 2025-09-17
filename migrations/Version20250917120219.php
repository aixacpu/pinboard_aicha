<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250917120219 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE figurines DROP FOREIGN KEY FK_FIGURINE_USER');
        $this->addSql('ALTER TABLE figurines CHANGE image_name image_name VARCHAR(500) NOT NULL');
        $this->addSql('ALTER TABLE figurines ADD CONSTRAINT FK_45D9EB61A76ED395 FOREIGN KEY (user_id) REFERENCES users (id)');
        $this->addSql('ALTER TABLE figurines RENAME INDEX idx_figurine_user TO IDX_45D9EB61A76ED395');
        $this->addSql('ALTER TABLE users DROP created_at, DROP updated_at, CHANGE profile_image profile_image VARCHAR(255) DEFAULT \'default.jpg\' NOT NULL');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE figurines DROP FOREIGN KEY FK_45D9EB61A76ED395');
        $this->addSql('ALTER TABLE figurines CHANGE image_name image_name VARCHAR(255) NOT NULL');
        $this->addSql('ALTER TABLE figurines ADD CONSTRAINT FK_FIGURINE_USER FOREIGN KEY (user_id) REFERENCES users (id) ON UPDATE NO ACTION ON DELETE CASCADE');
        $this->addSql('ALTER TABLE figurines RENAME INDEX idx_45d9eb61a76ed395 TO IDX_FIGURINE_USER');
        $this->addSql('ALTER TABLE users ADD created_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', ADD updated_at DATETIME NOT NULL, CHANGE profile_image profile_image VARCHAR(255) DEFAULT \'default.jpg\'');
    }
}
