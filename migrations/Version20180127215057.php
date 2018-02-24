<?php declare(strict_types=1);

namespace Xyz\Akulov\Apteka\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

class Version20180127215057 extends AbstractMigration
{
    public function up(Schema $schema)
    {
        $this->addSql(<<<SQL
CREATE OR REPLACE FUNCTION trigger_set_timestamp()
  RETURNS TRIGGER AS $$
BEGIN
  IF TG_OP = 'INSERT' THEN
    NEW.created_at = NOW();
    NEW.updated_at = NULL ;
  ELSE
    NEW.created_at = OLD.created_at;
    NEW.updated_at = NOW();
  END IF;
  
  RETURN NEW;
END;
$$ LANGUAGE plpgsql;
SQL
        );
    }

    public function down(Schema $schema)
    {
        $this->addSql('DROP FUNCTION trigger_set_timestamp()');
    }
}
