<?php
class InstallModel
{
	function install_session()
	{
		$this->ddl
		->CreateTable($this->prefix.'session')
		->addColumn('id',SQL_INT,10,SQL_PRIMARY)
		->addColumn('session_id',SQL_VARCHAR,32)
		->addColumn('data','text')
		->addColumn('expire',SQL_VARCHAR,12)
		->Compile();
	}
}
?>
