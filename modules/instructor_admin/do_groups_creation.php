<?php
if (!defined("DP_BASE_DIR")) {
    die("You should not access this file directly.");
}

$classId=$_POST["class_id"];
$n = $_POST["number_of_groups"];
if ($n > 50) {
    $n = 50;
}
for ($i = 0; $i < $n; $i++) {
//1. Create company
    $q = new DBQuery();
    $q->addTable("companies");
//user_id is auto increment
    $q->addInsert("company_module", 0);
    $q->addInsert("company_name", "Empresa - Grupo_"); //it will be updated later based on the auto generated id
    $q->addInsert("company_owner", 0); //it will be updated later based on the auto generated id
    $sql = $q->prepare();
    //echo $sql;
    $q->exec();
    $companyId = mysql_insert_id();

//2. Create contact
    $q = new DBQuery();
    $q->addTable("contacts");
//user_id is auto increment
    $q->addInsert("contact_first_name", "Grupo");
    $q->addInsert("contact_last_name", ""); //it will be updated later based on the auto generated id
    $q->addInsert("contact_company", $companyId);
    $q->addInsert("contact_project", 0);
    $q->addInsert("contact_icon", "obj/contact");
    $q->addInsert("contact_owner", 0);
    $q->addInsert("contact_private", 0);
    $sql = $q->prepare();
    //echo $sql;
    $q->exec();
    $contactId = mysql_insert_id();

//3. Create user
    $q = new DBQuery();
    $q->addTable("users");
//user_id is auto increment
    $q->addInsert("user_contact", $contactId);
    $q->addInsert("user_username", "grupo_"); //it will be updated later based on the auto generated id
    $password = rand(100000, 999999); //generate a password of 6 digits
    $q->addInsert("user_password", md5($password));
    $q->addInsert("user_parent", 0);
    $q->addInsert("user_type", 0);
    $q->addInsert("user_company", $companyId);
    $q->addInsert("user_department", 0);
    $q->addInsert("user_owner", 0);
    $sql = $q->prepare();
    //echo $sql;
    $q->exec();

    $userId = mysql_insert_id();

//4. update user - user_name, contact - contact_last_name, and company name based on user generated id
    $userName="grupo_" . $userId;
    $q = new DBQuery();
    $q->addTable("users");
    $q->addUpdate("user_username", $userName);
    $q->addWhere("user_id =$userId");
    $sql = $q->prepare();
    //echo $sql;
    $q->exec();

    $q = new DBQuery();
    $q->addTable("contacts");
    $q->addUpdate("contact_last_name", $userId);
    $q->addUpdate("contact_email", "grupo_$userId@dpp.com");
    $q->addWhere("contact_id = $contactId");
    $sql = $q->prepare();
    //echo $sql;
    $q->exec();

    $companyName = "Empresa - Grupo_" . $userId;
    $q = new DBQuery();
    $q->addTable("companies");
//user_id is auto increment
    $q->addUpdate("company_name", $companyName);
    $q->addUpdate("company_owner", $contactId);
    $q->addWhere("company_id = $companyId");
    $sql = $q->prepare();
    //echo $sql;
    $q->exec();

    echo "Password: " . $password;

//5. create access rights entry
    /* gacl_aco: define access types : access, read, edit, etc.
     * gacl_axo: define system modules
     * gacl_aro: define user permissions
     */

//get acl_aro id (getr last ID to update)
    $q = new DBQuery();
    $q->addTable("gacl_aro_seq");
    $q->addQuery("id");
    $sql = $q->prepare();
    //echo $sql;
    $records = db_loadList($sql);
    foreach ($records as $record) {
        $aclAroId = $record[0];
    }

//update acl id for the next user
    $q = new DBQuery();
    $q->addTable("gacl_aro_seq");
    $q->addUpdate("id", $aclAroId + 1);
    $sql = $q->prepare();
    //echo $sql;
    $q->exec();


    $q = new DBQuery();
    $q->addTable("gacl_aro");
//user_id is auto increment
    $q->addInsert("id", $aclAroId);
    $q->addInsert("section_value", "user"); //it will be updated later based on the auto generated id
    $q->addInsert("value", $userId);
    $q->addInsert("order_value", 1);
    $q->addInsert("name", "grupo_" . $userId);
    $q->addInsert("hidden", 0);
    $sql = $q->prepare();
    //echo $sql;
    $q->exec();

//6. add user in the project worker group
    $q = new DBQuery();
    $q->addTable("gacl_groups_aro_map");
//user_id is auto increment
    $q->addInsert("group_id", 14); //dotp_gacl_aro_groups == 14 (project worker)
    $q->addInsert("aro_id", $aclAroId);
    $sql = $q->prepare();
    //echo $sql;
    $q->exec();

//7. Add user access rights permitions
//fisrt deny permission
//get acl id
    $q = new DBQuery();
    $q->addTable("gacl_acl_seq");
    $q->addQuery("id");
    $sql = $q->prepare();
    //echo $sql;
    $records = db_loadList($sql);
    foreach ($records as $record) {
        $aclIdDeny = $record[0];
    }
    $aclIdDeny++;
    $q = new DBQuery();
    $q->addTable("gacl_acl");
    $q->addInsert("id", $aclIdDeny);
    $q->addInsert("section_value", "user"); //it will be updated later based on the auto generated id
    $q->addInsert("allow", 0);
    $q->addInsert("enabled", 1);
    $q->addInsert("updated_date", time());
    $sql = $q->prepare();
    //echo $sql;
    $q->exec();

    $q = new DBQuery();
    $q->addTable("gacl_aco_map");
    $q->addInsert("acl_id", $aclIdDeny);
    $q->addInsert("section_value", "application");
    $q->addInsert("value", "view");
    $sql = $q->prepare();
    //echo $sql;
    $q->exec();
    
    $q = new DBQuery();
    $q->addTable("gacl_aco_map");
    $q->addInsert("acl_id", $aclIdDeny);
    $q->addInsert("section_value", "application");
    $q->addInsert("value", "add");
    $sql = $q->prepare();
    //echo $sql;
    $q->exec();
    

    $q = new DBQuery();
    $q->addTable("gacl_aro_map");
    $q->addInsert("acl_id", $aclIdDeny);
    $q->addInsert("section_value", "user");
    $q->addInsert("value", $userId);
    $sql = $q->prepare();
    //echo $sql;
    $q->exec();

    $q = new DBQuery();
    $q->addTable("gacl_axo_map");
    $q->addInsert("acl_id", $aclIdDeny);
    $q->addInsert("section_value", "app");
    $q->addInsert("value", "companies");
    $sql = $q->prepare();
    //echo $sql;
    $q->exec();


//update acl id for the next user
    $q = new DBQuery();
    $q->addTable("gacl_acl_seq");
    $q->addUpdate("id", $aclIdDeny);
    $sql = $q->prepare();
    //echo $sql;
    $q->exec();


//include dotpermissions
    
    $q = new DBQuery();
    $q->addTable("dotpermissions");
    $q->addInsert("acl_id", 15); //hardcoded 15 for all users
    $q->addInsert("user_id", $userId);
    $q->addInsert("section", "app");
    $q->addInsert("axo", "companies");
    $q->addInsert("permission", "access");
    $q->addInsert("allow", 1);
    $q->addInsert("enabled", 1);
    $q->addInsert("priority", 1);
    $sql = $q->prepare();
    //echo $sql;
    $q->exec();
    
    
    $q = new DBQuery();
    $q->addTable("dotpermissions");
    $q->addInsert("acl_id", $aclIdDeny);
    $q->addInsert("user_id", $userId);
    $q->addInsert("section", "app");
    $q->addInsert("axo", "companies");
    $q->addInsert("permission", "view");
    $q->addInsert("allow", 0);
    $q->addInsert("enabled", 1);
    $q->addInsert("priority", 1);
    $sql = $q->prepare();
    //echo $sql;
    $q->exec();
    
    $q = new DBQuery();
    $q->addTable("dotpermissions");
    $q->addInsert("acl_id", $aclIdDeny);
    $q->addInsert("user_id", $userId);
    $q->addInsert("section", "app");
    $q->addInsert("axo", "companies");
    $q->addInsert("permission", "add");
    $q->addInsert("allow", 1);
    $q->addInsert("enabled", 1);
    $q->addInsert("priority", 1);
    $sql = $q->prepare();
    //echo $sql;
    $q->exec();

//second: dotp_gacl_axo: grant access especifically to one company 
//insert new user in acl table
    $aclId = $aclIdDeny + 1; //next acl entry (has to be updated in gacl_acl_seq table)
    $q = new DBQuery();
    $q->addTable("gacl_acl");
    $q->addInsert("id", $aclId);
    $q->addInsert("section_value", "user"); //it will be updated later based on the auto generated id
    $q->addInsert("allow", 1);
    $q->addInsert("enabled", 1);
    $q->addInsert("updated_date", time());
    $sql = $q->prepare();
    //echo $sql;
    $q->exec();

//update acl id for the next user
    $q = new DBQuery();
    $q->addTable("gacl_acl_seq");
    $q->addUpdate("id", $aclId);
    $sql = $q->prepare();
    //echo $sql;
    $q->exec();

//bind acl with current user
    $q = new DBQuery();
    $q->addTable("gacl_aro_map");
    $q->addInsert("acl_id", $aclId); //dotp_gacl_aro_groups == 14 (project worker)
    $q->addInsert("section_value", "user");
    $q->addInsert("value", $userId);
    $sql = $q->prepare();
    //echo $sql;
    $q->exec();

//gacl_aco_map: include 3 rows for user acl id (access for view, update and access)
    $q = new DBQuery();
    $q->addTable("gacl_aco_map");
    $q->addInsert("acl_id", $aclId); //dotp_gacl_aro_groups == 14 (project worker)
    $q->addInsert("section_value", "application");
    $q->addInsert("value", "access");
    $sql = $q->prepare();
    //echo $sql;
    $q->exec();

    $q = new DBQuery();
    $q->addTable("gacl_aco_map");
    $q->addInsert("acl_id", $aclId); //dotp_gacl_aro_groups == 14 (project worker)
    $q->addInsert("section_value", "application");
    $q->addInsert("value", "edit");
    $sql = $q->prepare();
    //echo $sql;
    $q->exec();

    $q = new DBQuery();
    $q->addTable("gacl_aco_map");
    $q->addInsert("acl_id", $aclId); //dotp_gacl_aro_groups == 14 (project worker)
    $q->addInsert("section_value", "application");
    $q->addInsert("value", "view");
    $sql = $q->prepare();
    //echo $sql;
    $q->exec();


//include dotpermissions
    $q = new DBQuery();
    $q->addTable("dotpermissions");
    $q->addInsert("acl_id", $aclId);
    $q->addInsert("user_id", $userId);
    $q->addInsert("section", "companies");
    $q->addInsert("axo", $companyId);
    $q->addInsert("permission", "view");
    $q->addInsert("allow", 1);
    $q->addInsert("enabled", 1);
    $q->addInsert("priority", 1);
    $sql = $q->prepare();
    //echo $sql;
    $q->exec();

    $q = new DBQuery();
    $q->addTable("dotpermissions");
    $q->addInsert("acl_id", $aclId);
    $q->addInsert("user_id", $userId);
    $q->addInsert("section", "companies");
    $q->addInsert("axo", $companyId);
    $q->addInsert("permission", "edit");
    $q->addInsert("allow", 1);
    $q->addInsert("enabled", 1);
    $q->addInsert("priority", 1);
    $sql = $q->prepare();
    //echo $sql;
    $q->exec();

    $q = new DBQuery();
    $q->addTable("dotpermissions");
    $q->addInsert("acl_id", $aclId);
    $q->addInsert("user_id", $userId);
    $q->addInsert("section", "companies");
    $q->addInsert("axo", $companyId);
    $q->addInsert("permission", "access");
    $q->addInsert("allow", 1);
    $q->addInsert("enabled", 1);
    $q->addInsert("priority", 1);
    $sql = $q->prepare();
    //echo $sql;
    $q->exec();


//get last id and update
//get axo id
    $q = new DBQuery();
    $q->addTable("gacl_axo_seq");
    $q->addQuery("id");
    $sql = $q->prepare();
    //echo $sql;
    $records = db_loadList($sql);
    foreach ($records as $record) {
        $axoId = $record[0];
    }
    $axoId++;
    $q = new DBQuery();
    $q->addTable("gacl_axo_seq");
    $q->addUpdate("id", $axoId);
    $sql = $q->prepare();
    //echo $sql;
    $q->exec();


//insert new record
    $q = new DBQuery();
    $q->addTable("gacl_axo");
    $q->addInsert("id", $axoId);
    $q->addInsert("section_value", "companies");
    $q->addInsert("value", $companyId);
    $q->addInsert("order_value", 0);
    $q->addInsert("hidden", 0);
    $q->addInsert("name", $companyName);
    $sql = $q->prepare();
    //echo $sql;
    $q->exec();


    $q = new DBQuery();
    $q->addTable("gacl_axo_map");
    $q->addInsert("acl_id", $aclId);
    $q->addInsert("section_value", "companies");
    $q->addInsert("value", $companyId);
    $sql = $q->prepare();
    //echo $sql;
    $q->exec();



//include generic permissions for all modules - thus enabling basic navegation into screens
    $modules = array(
        "calendar",
        "events",
        //"companies", takeout to not allow the creation of new companies, beyond the automatically created to this user
        "contacts",
        "departments",
        "files",
        "file_folders",
        "forums",
        "help",
        "projects",
        "tasks",
        "task_log",
        "ticketsmith",
        "public",
        "closure",
        "communication",
        "costs",
        "monitoringandcontrol",
        "dotproject_plus",
        "human_resources",
        "risks",
        "timeplanning");

    $accessTypes = array("access", "view", "edit", "delete", "add");
    foreach ($modules as $module) {
        foreach ($accessTypes as $access) {
            $q = new DBQuery();
            $q->addTable("dotpermissions");
            $q->addInsert("acl_id", 15); //hardcoded 15 for all users
            $q->addInsert("user_id", $userId);
            $q->addInsert("section", "app");
            $q->addInsert("axo", $module);
            $q->addInsert("permission", $access);
            $q->addInsert("allow", 1);
            $q->addInsert("enabled", 1);
            $q->addInsert("priority", 4);
            $sql = $q->prepare();
            //echo $sql;
            $q->exec();
        }
    }
    
    
    //insert the new user in the class
    $q = new DBQuery();
    $q->addTable("dpp_classes_users");
    $q->addInsert("class_id", $classId); //hardcoded 15 for all users
    $q->addInsert("user_id", $userId);
    $q->addInsert("user_login", $userName);
    $q->addInsert("user_password", $password);
    $q->addInsert("user_company", $companyId);
    $sql = $q->prepare();
    //echo $sql;
    $q->exec();
}

$AppUI->setMsg($AppUI->_("LBL_GROUPS_WERE_SUCESSFULLY CREATE"), UI_OUTPUT_HTML, UI_MSG_OK, false);
$AppUI->redirect();
?>