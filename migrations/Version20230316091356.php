<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230316091356 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE commentaires_hashtag (commentaires_id INT NOT NULL, hashtag_id INT NOT NULL, INDEX IDX_B829727717C4B2B0 (commentaires_id), INDEX IDX_B8297277FB34EF56 (hashtag_id), PRIMARY KEY(commentaires_id, hashtag_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE commentaires_hashtag ADD CONSTRAINT FK_B829727717C4B2B0 FOREIGN KEY (commentaires_id) REFERENCES commentaires (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE commentaires_hashtag ADD CONSTRAINT FK_B8297277FB34EF56 FOREIGN KEY (hashtag_id) REFERENCES hashtag (id) ON DELETE CASCADE');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE commentaires_hashtag DROP FOREIGN KEY FK_B829727717C4B2B0');
        $this->addSql('ALTER TABLE commentaires_hashtag DROP FOREIGN KEY FK_B8297277FB34EF56');
        $this->addSql('DROP TABLE commentaires_hashtag');
    }
}
