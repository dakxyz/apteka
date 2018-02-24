<?php declare(strict_types=1);

namespace Xyz\Akulov\Apteka\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

class Version20180224125039 extends AbstractMigration
{
    public function up(Schema $schema)
    {
        $this->addSql('CREATE TABLE roles (id SERIAL NOT NULL, name VARCHAR(31) NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_B63E2EC75E237E06 ON roles (name)');
        $this->addSql('CREATE TABLE users (id SERIAL NOT NULL, created_at TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL, updated_at TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL, email VARCHAR(63) NOT NULL, password_hash VARCHAR(64) NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_1483A5E9E7927C74 ON users (email)');
        $this->addSql('CREATE TABLE users_roles (user_id INT NOT NULL, role_id INT NOT NULL, PRIMARY KEY(user_id, role_id))');
        $this->addSql('CREATE INDEX IDX_51498A8EA76ED395 ON users_roles (user_id)');
        $this->addSql('CREATE INDEX IDX_51498A8ED60322AC ON users_roles (role_id)');
        $this->addSql('CREATE TABLE tasks (id SERIAL NOT NULL, created_at TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL, updated_at TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL, owner_id INT NOT NULL, type VARCHAR(15) NOT NULL, payload JSONB NOT NULL, status VARCHAR(15) NOT NULL, PRIMARY KEY(id))');
        $this->addSql('COMMENT ON COLUMN tasks.payload IS \'(DC2Type:json_array)\'');
        $this->addSql('CREATE TABLE steps (id SERIAL NOT NULL, task_id INT DEFAULT NULL, created_at TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL, updated_at TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL, type VARCHAR(15) NOT NULL, status VARCHAR(15) NOT NULL, payload JSONB NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_34220A728DB60186 ON steps (task_id)');
        $this->addSql('COMMENT ON COLUMN steps.payload IS \'(DC2Type:json_array)\'');
        $this->addSql('CREATE TABLE files (id SERIAL NOT NULL, created_at TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL, updated_at TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL, name VARCHAR(255) NOT NULL, extension VARCHAR(15) NOT NULL, hash VARCHAR(64) NOT NULL, purpose VARCHAR(15) NOT NULL, owner_id INT NOT NULL, source_file_name VARCHAR(255) NOT NULL, source_size INT NOT NULL, source_mime_type VARCHAR(127) NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE UNIQUE INDEX unique_path_key ON files (owner_id, purpose, name, extension)');
        $this->addSql('ALTER TABLE users_roles ADD CONSTRAINT FK_51498A8EA76ED395 FOREIGN KEY (user_id) REFERENCES users (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE users_roles ADD CONSTRAINT FK_51498A8ED60322AC FOREIGN KEY (role_id) REFERENCES roles (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE steps ADD CONSTRAINT FK_34220A728DB60186 FOREIGN KEY (task_id) REFERENCES tasks (id) NOT DEFERRABLE INITIALLY IMMEDIATE');

        $this->addSql(Helper::sqlInsertDictionary('roles', ['root', 'customer', 'pharmacy']));

        $this->addSql(Helper::sqlAppendSetTimestampAsTrigger('tasks'));
        $this->addSql(Helper::sqlAppendSetTimestampAsTrigger('steps'));
        $this->addSql(Helper::sqlAppendSetTimestampAsTrigger('users'));
        $this->addSql(Helper::sqlAppendSetTimestampAsTrigger('files'));
    }

    public function down(Schema $schema)
    {
        $this->addSql('ALTER TABLE users_roles DROP CONSTRAINT FK_51498A8ED60322AC');
        $this->addSql('ALTER TABLE users_roles DROP CONSTRAINT FK_51498A8EA76ED395');
        $this->addSql('ALTER TABLE steps DROP CONSTRAINT FK_34220A728DB60186');
        $this->addSql('DROP TABLE roles');
        $this->addSql('DROP TABLE users');
        $this->addSql('DROP TABLE users_roles');
        $this->addSql('DROP TABLE tasks');
        $this->addSql('DROP TABLE steps');
        $this->addSql('DROP TABLE files');
    }
}
