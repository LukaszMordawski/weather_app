<?php declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20181204145330 extends AbstractMigration
{
    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE air_conditions (id INT AUTO_INCREMENT NOT NULL, weather_record_id INT DEFAULT NULL, pressure SMALLINT NOT NULL, humidity SMALLINT NOT NULL, min_temp DOUBLE PRECISION NOT NULL, max_temp DOUBLE PRECISION NOT NULL, clouds SMALLINT NOT NULL, UNIQUE INDEX weather_record_unique (weather_record_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE cities (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(50) NOT NULL, country_code VARCHAR(2) NOT NULL, lat NUMERIC(4, 2) NOT NULL, lon NUMERIC(5, 2) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE weather_conditions (id INT AUTO_INCREMENT NOT NULL, weather_record_id INT DEFAULT NULL, description VARCHAR(30) NOT NULL, INDEX IDX_4B9CE63E6773F43F (weather_record_id), UNIQUE INDEX weather_record_unique (weather_record_id, description), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE weather_records (id INT AUTO_INCREMENT NOT NULL, city_id INT DEFAULT NULL, date DATETIME NOT NULL, sunrise DATETIME NOT NULL, sunset DATETIME NOT NULL, INDEX IDX_38A2BB0D8BAC62AF (city_id), UNIQUE INDEX weather_record_unique (city_id, date), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE wind_conditions (id INT AUTO_INCREMENT NOT NULL, weather_record_id INT DEFAULT NULL, speed NUMERIC(4, 1) NOT NULL, direction SMALLINT DEFAULT NULL, UNIQUE INDEX weather_record_unique (weather_record_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('ALTER TABLE air_conditions ADD CONSTRAINT FK_7DD490786773F43F FOREIGN KEY (weather_record_id) REFERENCES weather_records (id)');
        $this->addSql('ALTER TABLE weather_conditions ADD CONSTRAINT FK_4B9CE63E6773F43F FOREIGN KEY (weather_record_id) REFERENCES weather_records (id)');
        $this->addSql('ALTER TABLE weather_records ADD CONSTRAINT FK_38A2BB0D8BAC62AF FOREIGN KEY (city_id) REFERENCES cities (id)');
        $this->addSql('ALTER TABLE wind_conditions ADD CONSTRAINT FK_4BEE6F9C6773F43F FOREIGN KEY (weather_record_id) REFERENCES weather_records (id)');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE weather_records DROP FOREIGN KEY FK_38A2BB0D8BAC62AF');
        $this->addSql('ALTER TABLE air_conditions DROP FOREIGN KEY FK_7DD490786773F43F');
        $this->addSql('ALTER TABLE weather_conditions DROP FOREIGN KEY FK_4B9CE63E6773F43F');
        $this->addSql('ALTER TABLE wind_conditions DROP FOREIGN KEY FK_4BEE6F9C6773F43F');
        $this->addSql('DROP TABLE air_conditions');
        $this->addSql('DROP TABLE cities');
        $this->addSql('DROP TABLE weather_conditions');
        $this->addSql('DROP TABLE weather_records');
        $this->addSql('DROP TABLE wind_conditions');
    }
}
