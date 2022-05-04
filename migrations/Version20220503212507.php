<?php

declare (strict_types = 1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20220503212507 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs

        $this->addSql('ALTER TABLE images  ADD name_gallary_images VARCHAR(255) DEFAULT NULL');

        $this->addSql('ALTER TABLE trick ADD CONSTRAINT FK_D8F0A91EA76ED395 FOREIGN KEY (user_id) REFERENCES user (id)');
        $this->addSql('DROP INDEX fk_d8f0a91ea76ed395 ON trick');
        $this->addSql('CREATE INDEX IDX_D8F0A91EA76ED395 ON trick (user_id)');
        $this->addSql('ALTER TABLE user CHANGE profie_image profie_image VARCHAR(255) NOT NULL, CHANGE roles roles INT NOT NULL');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE gallary-images (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(250) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_general_ci`, trick_id INT NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE `utf8_unicode_ci` ENGINE = InnoDB COMMENT = \'\' ');
        $this->addSql('CREATE TABLE gallary_image (id INT AUTO_INCREMENT NOT NULL, trick_id INT NOT NULL, name VARCHAR(255) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci`, INDEX IDX_7C4C8570B281BE2E (trick_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE `utf8_unicode_ci` ENGINE = InnoDB COMMENT = \'\' ');
        $this->addSql('CREATE TABLE gallary_images (id INT AUTO_INCREMENT NOT NULL, trick_id INT NOT NULL, name VARCHAR(250) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_general_ci`, INDEX trick_id (trick_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE `utf8_unicode_ci` ENGINE = InnoDB COMMENT = \'\' ');
        $this->addSql('ALTER TABLE gallary_image ADD CONSTRAINT FK_7C4C8570B281BE2E FOREIGN KEY (trick_id) REFERENCES trick (id)');
        $this->addSql('ALTER TABLE gallary_images ADD CONSTRAINT gallary_images_ibfk_1 FOREIGN KEY (trick_id) REFERENCES trick (id) ON UPDATE NO ACTION ON DELETE NO ACTION');
        $this->addSql('ALTER TABLE images DROP is_main_image, DROP name_gallary_images');
        $this->addSql('ALTER TABLE trick DROP FOREIGN KEY FK_D8F0A91EA76ED395');
        $this->addSql('ALTER TABLE trick DROP FOREIGN KEY FK_D8F0A91EA76ED395');
        $this->addSql('ALTER TABLE trick CHANGE user_id user_id INT DEFAULT NULL');
        $this->addSql('DROP INDEX idx_d8f0a91ea76ed395 ON trick');
        $this->addSql('CREATE INDEX FK_D8F0A91EA76ED395 ON trick (user_id)');
        $this->addSql('ALTER TABLE trick ADD CONSTRAINT FK_D8F0A91EA76ED395 FOREIGN KEY (user_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE user CHANGE profie_image profie_image VARCHAR(255) CHARACTER SET utf8mb4 DEFAULT NULL COLLATE `utf8mb4_unicode_ci`, CHANGE roles roles INT DEFAULT NULL');
    }
}
