<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230608142312 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        // $this->addSql('CREATE TABLE session (idSession INT AUTO_INCREMENT NOT NULL, token VARCHAR(500) NOT NULL, idUser INT NOT NULL, statut TINYINT(1) NOT NULL, dateDebut DATE NOT NULL, dateFin DATE NOT NULL, PRIMARY KEY(idSession)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE IF NOT EXISTS session (
            idSession INT AUTO_INCREMENT NOT NULL,
            token VARCHAR(500) NOT NULL,
            idUser INT NOT NULL,
            statut TINYINT(1) NOT NULL,
            dateDebut DATE NOT NULL,
            dateFin DATE NOT NULL,
            PRIMARY KEY(idSession)
        ) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE token DROP FOREIGN KEY FK_5F37A13BA76ED395');
        $this->addSql('DROP TABLE token');
        $this->addSql('ALTER TABLE avis DROP FOREIGN KEY FK_8F91ABF0E05BD40A');
        $this->addSql('ALTER TABLE avis DROP FOREIGN KEY FK_8F91ABF0F76E51E7');
        $this->addSql('DROP INDEX UsersID ON avis');
        $this->addSql('DROP INDEX ProduitID ON avis');
        $this->addSql('ALTER TABLE chat DROP FOREIGN KEY FK_659DF2AAF76E51E7');
        $this->addSql('DROP INDEX UsersID ON chat');
        $this->addSql('ALTER TABLE commandes DROP FOREIGN KEY FK_35D4282CF76E51E7');
        $this->addSql('DROP INDEX UsersID ON commandes');
        $this->addSql('ALTER TABLE commandes ADD idUser INT NOT NULL, ADD idProduit INT NOT NULL, ADD Quantite INT NOT NULL, DROP Total, DROP UsersID');
        $this->addSql('ALTER TABLE contact DROP FOREIGN KEY FK_4C62E638F76E51E7');
        $this->addSql('DROP INDEX UsersID ON contact');
        $this->addSql('ALTER TABLE panier DROP FOREIGN KEY FK_24CC0DF2E05BD40A');
        $this->addSql('ALTER TABLE panier DROP FOREIGN KEY FK_24CC0DF258746832');
        $this->addSql('DROP INDEX UserID ON panier');
        $this->addSql('DROP INDEX ProduitID ON panier');
        $this->addSql('ALTER TABLE panier DROP PrixUnitaire');
        $this->addSql('ALTER TABLE produits DROP FOREIGN KEY FK_BE2DDF8C43D52186');
        $this->addSql('ALTER TABLE produits DROP FOREIGN KEY FK_BE2DDF8CF76E51E7');
        $this->addSql('DROP INDEX UsersID ON produits');
        $this->addSql('DROP INDEX CategorieID ON produits');
        $this->addSql('ALTER TABLE produits DROP UsersID');
        $this->addSql('ALTER TABLE reglement DROP FOREIGN KEY FK_EBE4C14CFBAA0F66');
        $this->addSql('DROP INDEX CommandeID ON reglement');
        $this->addSql('DROP INDEX Email ON users');
        $this->addSql('ALTER TABLE users CHANGE Email email VARCHAR(100) NOT NULL');
        
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE token (id INT AUTO_INCREMENT NOT NULL, user_id INT NOT NULL, revoked TINYINT(1) NOT NULL, INDEX IDX_5F37A13BA76ED395 (user_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB COMMENT = \'\' ');
        $this->addSql('ALTER TABLE token ADD CONSTRAINT FK_5F37A13BA76ED395 FOREIGN KEY (user_id) REFERENCES users (ID) ON UPDATE NO ACTION ON DELETE NO ACTION');
        $this->addSql('DROP TABLE session');
        $this->addSql('ALTER TABLE avis ADD CONSTRAINT FK_8F91ABF0E05BD40A FOREIGN KEY (ProduitID) REFERENCES produits (ID) ON UPDATE NO ACTION ON DELETE NO ACTION');
        $this->addSql('ALTER TABLE avis ADD CONSTRAINT FK_8F91ABF0F76E51E7 FOREIGN KEY (UsersID) REFERENCES users (ID) ON UPDATE NO ACTION ON DELETE NO ACTION');
        $this->addSql('CREATE INDEX UsersID ON avis (UsersID)');
        $this->addSql('CREATE INDEX ProduitID ON avis (ProduitID)');
        $this->addSql('ALTER TABLE produits ADD UsersID INT DEFAULT NULL');
        $this->addSql('ALTER TABLE produits ADD CONSTRAINT FK_BE2DDF8C43D52186 FOREIGN KEY (CategorieID) REFERENCES categories (ID) ON UPDATE NO ACTION ON DELETE NO ACTION');
        $this->addSql('ALTER TABLE produits ADD CONSTRAINT FK_BE2DDF8CF76E51E7 FOREIGN KEY (UsersID) REFERENCES users (ID) ON UPDATE NO ACTION ON DELETE NO ACTION');
        $this->addSql('CREATE INDEX UsersID ON produits (UsersID)');
        $this->addSql('CREATE INDEX CategorieID ON produits (CategorieID)');
        $this->addSql('ALTER TABLE reglement ADD CONSTRAINT FK_EBE4C14CFBAA0F66 FOREIGN KEY (CommandeID) REFERENCES commandes (ID) ON UPDATE NO ACTION ON DELETE NO ACTION');
        $this->addSql('CREATE INDEX CommandeID ON reglement (CommandeID)');
        $this->addSql('ALTER TABLE panier ADD PrixUnitaire NUMERIC(10, 2) DEFAULT NULL');
        $this->addSql('ALTER TABLE panier ADD CONSTRAINT FK_24CC0DF2E05BD40A FOREIGN KEY (ProduitID) REFERENCES produits (ID) ON UPDATE NO ACTION ON DELETE NO ACTION');
        $this->addSql('ALTER TABLE panier ADD CONSTRAINT FK_24CC0DF258746832 FOREIGN KEY (UserID) REFERENCES users (ID) ON UPDATE NO ACTION ON DELETE NO ACTION');
        $this->addSql('CREATE INDEX UserID ON panier (UserID)');
        $this->addSql('CREATE INDEX ProduitID ON panier (ProduitID)');
        $this->addSql('ALTER TABLE contact ADD CONSTRAINT FK_4C62E638F76E51E7 FOREIGN KEY (UsersID) REFERENCES users (ID) ON UPDATE NO ACTION ON DELETE NO ACTION');
        $this->addSql('CREATE INDEX UsersID ON contact (UsersID)');
        $this->addSql('ALTER TABLE commandes ADD Total NUMERIC(10, 2) DEFAULT NULL, ADD UsersID INT DEFAULT NULL, DROP idUser, DROP idProduit, DROP Quantite');
        $this->addSql('ALTER TABLE commandes ADD CONSTRAINT FK_35D4282CF76E51E7 FOREIGN KEY (UsersID) REFERENCES users (ID) ON UPDATE NO ACTION ON DELETE NO ACTION');
        $this->addSql('CREATE INDEX UsersID ON commandes (UsersID)');
        $this->addSql('ALTER TABLE users CHANGE email Email VARCHAR(100) DEFAULT NULL');
        $this->addSql('CREATE UNIQUE INDEX Email ON users (Email)');
        $this->addSql('ALTER TABLE chat ADD CONSTRAINT FK_659DF2AAF76E51E7 FOREIGN KEY (UsersID) REFERENCES users (ID) ON UPDATE NO ACTION ON DELETE NO ACTION');
        $this->addSql('CREATE INDEX UsersID ON chat (UsersID)');
    }
}
