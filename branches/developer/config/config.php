<?php
	## NEED TO DO SOMETHING WITH THIS ##
	//$config['default_layout'] = "index";
	
	Config::register("Branch.active", true);
	Config::register("Branch.requiredSystemMode", "development");
	Config::register("Branch.minimumSystemVersion", "1.0.0b");
	//Config::register("Branch.maximumSystemVersion", "1.0.0");
	
	## Database Setup ##
	Config::register("Database.host", "localhost_dev");
	Config::register("Database.username", "root_dev");
	Config::register("Database.password", "root_dev");
	Config::register("Database.database", "developer_database");
	Config::register("Database.driver", "MySQL");
	
	## Routes ##
	Config::registerRoute("/apples(.*)", "/main/foobar/$1");
?>