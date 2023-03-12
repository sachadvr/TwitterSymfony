<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230312224503 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE retweets_commentaires (commentaires_id INT NOT NULL, user_id INT NOT NULL, INDEX IDX_628F8A6417C4B2B0 (commentaires_id), INDEX IDX_628F8A64A76ED395 (user_id), PRIMARY KEY(commentaires_id, user_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE likes_commentaires (commentaires_id INT NOT NULL, user_id INT NOT NULL, INDEX IDX_86C682E717C4B2B0 (commentaires_id), INDEX IDX_86C682E7A76ED395 (user_id), PRIMARY KEY(commentaires_id, user_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE retweets_commentaires ADD CONSTRAINT FK_628F8A6417C4B2B0 FOREIGN KEY (commentaires_id) REFERENCES commentaires (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE retweets_commentaires ADD CONSTRAINT FK_628F8A64A76ED395 FOREIGN KEY (user_id) REFERENCES user (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE likes_commentaires ADD CONSTRAINT FK_86C682E717C4B2B0 FOREIGN KEY (commentaires_id) REFERENCES commentaires (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE likes_commentaires ADD CONSTRAINT FK_86C682E7A76ED395 FOREIGN KEY (user_id) REFERENCES user (id) ON DELETE CASCADE');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE retweets_commentaires DROP FOREIGN KEY FK_628F8A6417C4B2B0');
        $this->addSql('ALTER TABLE retweets_commentaires DROP FOREIGN KEY FK_628F8A64A76ED395');
        $this->addSql('ALTER TABLE likes_commentaires DROP FOREIGN KEY FK_86C682E717C4B2B0');
        $this->addSql('ALTER TABLE likes_commentaires DROP FOREIGN KEY FK_86C682E7A76ED395');
        $this->addSql('DROP TABLE retweets_commentaires');
        $this->addSql('DROP TABLE likes_commentaires');
    }
}
