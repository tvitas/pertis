<?php
$cfg_maintenance_mode = FALSE;
$cfg_custom_toolbars_dir = "sites/pertis/layout/toolbars";
$cfg_custom_process_dir = "sites/pertis/process";
$cfg_custom_templates_dir = "lib/templates/pertis";
$cfg_custom_forms_dir = "sites/pertis/forms";
//$cfg_custom_scripts_dir = "sites/pertis/process";
$cfg_custom_th_dir = "sites/pertis/th";
$cfg_custom_tr_dir = "sites/pertis/tr";
$cfg_custom_report_th_dir = "sites/pertis/reports/th";
$cfg_custom_report_tr_dir = "sites/pertis/reports/tr";
$cfg_custom_layout_dir = "sites/pertis/layout";
//loginti plain text?
$cfg_log_flat = true;
$cfg_help_dir = "sites/pertis/help";
//naudojami tik šie user atributai iš AD, (samaccountname=$user_login)
$cfg_ad_attributes = array('givenname' => 'Vardas', 'sn' => 'Pavardė',
'cn' => 'Pilnas vardas', 'title' => 'Pareigos', 'department' => 'Padalinys',
'mail' => 'El. paštas', 'telephonenumber' => 'Tel. Nr.', 'mobile' => 'Mobil.',
'facsimiletelephonenumber' => 'Faks. Nr.');

/*lentelių atvaizdavimas.
$view_queries['schema_lentelė'] – lentelės SQL default be papildomų sąlygų, t. y. ištraukti visus įrašus.
Vaiko lentelės traukiamos formuojant SQL iš:
$view_queries['schema_lentelė'] IR
lentelės 'ryšiai' – system_schema.relations
Jei netinka vaiko lentelės default SQL tėvo lentelės formoje, tada vaiko lentelės SQL aprašoma
$view_queries['tėvo_schema_tėvo_lentelė_vaiko_schema_vaiko_lentelė']
*/

//
//schema administration – administravimas
//
$view_queries['administration_users'] = "SELECT *
FROM administration.users
LEFT JOIN administration.roles ON role_id = user_role_id
LEFT JOIN system_schema.languages ON lang_id = user_preffered_lang_id
LEFT JOIN administration.users_attributes ON user_id = user_attribute_user_id AND user_attribute_name = 'cn'";

$view_queries['administration_users_attributes'] = "SELECT *
FROM administration.users_attributes
LEFT JOIN administration.ad_attributes ON user_attribute_name = ad_attribute_name";
//
//Schema structure – struktūra
//

//structure.structure – struktūra
$view_queries['structure_structure'] = "SELECT * FROM structure.structure_list_ordered";

//structure.places – vietos
$view_queries['structure_places'] = "SELECT * FROM structure.places_list_ordered";

//structure.staff – darbuotojai, etatai
$view_queries['structure_staff'] = "SELECT *, x.user_login AS user_login_cn
FROM structure.staff a
LEFT JOIN structure.positions ON a.staff_position_id = position_id
LEFT JOIN structure.structure_tree_list ON a.staff_structure_node_path = structure_node_path
LEFT JOIN administration.users x ON a.staff_login_id = x.user_id
LEFT JOIN administration.users y ON a.staff_user_id = y.user_id
LEFT JOIN lists.degrees ON staff_degree_id = degrees_id";

//structure.positions – pareigybės
$view_queries['structure_positions'] = "SELECT * FROM structure.positions
LEFT JOIN administration.users ON position_user_id = user_id";
//
//schema pert – PERT
//
//pert.objects – objektai
$view_queries['pert_objects'] = "SELECT * FROM pert.objects
LEFT JOIN administration.users ON object_user_id = user_id";

//pert.budgets prie structure.structure – sąmatos struktūros formoje
$view_queries['structure_structure_pert_budgets'] = "SELECT
budget_id, budget_no, budget_title,
sum(budget_row_amount) AS budget_amount, sum(budget_row_total) AS budget_total,
budget_total_rest, budget_expenditures
FROM pert.budgets
LEFT JOIN pert.budgets_rows ON budget_id = budget_row_budget_id
LEFT JOIN structure.structure ON budget_row_structure_node_path = structure_node_path ";

//pert.objects prie structure.structure – objektai struktūros formoje
$view_queries['structure_structure_pert_objects'] = "SELECT object_id, object_no, object_title,
sum(budget_row_total) AS object_amount, object_amount_used
FROM pert.objects
LEFT JOIN pert.budgets ON budget_object_id = object_id
LEFT JOIN pert.budgets_rows ON budget_id = budget_row_budget_id ";

//pert.budgets – sąmatos
$view_queries['pert_budgets'] = "SELECT * FROM pert.budgets
LEFT JOIN structure.staff ON budget_owner_id = staff_id
LEFT JOIN pert.tasks ON budget_task_id = task_id
LEFT JOIN pert.objects ON budget_object_id = object_id
LEFT JOIN administration.users x ON budget_user_id = x.user_id";

//pert_budgets_rows – sąmatos eilutės
$view_queries['pert_budgets_rows'] = "SELECT * FROM pert.budgets_rows
LEFT JOIN lists.dimensions ON budget_row_dim_id = dim_id
LEFT JOIN structure.structure_tree_list ON budget_row_structure_node_path = structure_node_path
LEFT JOIN pert.budgets ON budget_row_budget_id = budget_id
LEFT JOIN administration.users ON budget_row_user_id = user_id";

//pert.objects_history – objekto istorija
$view_queries['pert_objects_history'] = "SELECT * FROM pert.objects_history
LEFT JOIN pert.objects ON object_history_object_id = object_id
LEFT JOIN administration.users ON object_history_user_id = user_id ";

//pert.tasks_registry – užduočių registras
$view_queries['pert_tasks_registry'] = "SELECT *
FROM pert.tasks_registry
LEFT JOIN administration.users_attributes ON task_registry_initiator_id = user_attribute_user_id AND user_attribute_name = 'cn'
LEFT JOIN administration.users ON task_registry_user_id = user_id
LEFT JOIN structure.structure_tree_list ON task_registry_initiator_structure_node_path = structure_node_path
LEFT JOIN structure.places_tree_list ON task_registry_place_node_path = place_node_path
LEFT JOIN structure.structure_tree_list_0 ON task_registry_effector_structure_node_path = structure_node_path_0
LEFT JOIN lists.tasks_priorities ON task_registry_priority_id = task_priority_weight";

//pert.tasks – užduotys
$view_queries['pert_tasks'] = "SELECT *,
initiator.user_attribute_value AS initiator_user_attribute_value,
effector.user_attribute_value AS effector_user_attribute_value,
inistruct.structure_fullname AS inistruct_structure_fullname,
effstruct.structure_fullname AS effstruct_structure_fullname,
changers.user_login AS changers_user_login
FROM pert.tasks
LEFT JOIN pert.tasks_registry ON task_reg_id = task_registry_id
LEFT JOIN administration.users_attributes AS initiator ON initiator.user_attribute_user_id = task_initiator_id AND initiator.user_attribute_name = 'cn'
LEFT JOIN administration.users_attributes AS effector ON effector.user_attribute_user_id = task_effector_id AND effector.user_attribute_name = 'cn'
LEFT JOIN administration.users z ON task_effector_id = z.user_id
LEFT JOIN administration.users AS changers ON task_user_id = changers.user_id
LEFT JOIN structure.structure_tree_list AS effstruct ON effstruct.structure_node_path = task_structure_node_path
LEFT JOIN structure.structure_tree_list AS inistruct ON inistruct.structure_node_path = task_initiator_structure_node_path
LEFT JOIN lists.tasks_priorities ON task_task_priority_id = task_priority_weight
LEFT JOIN lists.tasks_status ON task_task_status_id = task_status_id
LEFT JOIN structure.places_tree_list ON task_place_node_path = place_node_path";

//pert.tasks_recources – medžiagos, sunaudotos užduotyje
$view_queries['pert_tasks_recources'] = "SELECT *,
goods_title||CASE WHEN x.goods_ser_no !='' THEN ', '||x.goods_ser_no ELSE '' END AS goods_title,
budget_title||CASE WHEN y.object_title !='' THEN '. '||y.object_title ELSE '' END AS budget_fullname
FROM pert.tasks_recources
LEFT JOIN store.goods x ON goods_id = task_recource_goods_id
LEFT JOIN pert.budgets ON budget_id = task_recource_budget_id
LEFT JOIN pert.objects y ON budget_object_id = object_id
LEFT JOIN pert.tasks ON task_recource_task_id = task_id
LEFT JOIN administration.users ON user_id = task_recource_user_id ";

//pert.tasks_effectors - užduoties vykdytojai
$view_queries['pert_tasks_effectors'] = "SELECT *, staff_title||CASE WHEN position_title !='' THEN '. '||position_title ELSE '' END AS staff_fullname
FROM pert.tasks_effectors
LEFT JOIN structure.structure_tree_list ON task_effector_structure_node_path = structure_node_path
LEFT JOIN structure.staff ON task_effector_staff_id = staff_id
LEFT JOIN administration.users ON user_id = task_effector_user_id
LEFT JOIN structure.positions ON staff_position_id = position_id";

//
//schema store – sandėliai
//
//store.goods – prekių sąrašas, prekės
$view_queries['store_goods'] = "SELECT * FROM store.goods
LEFT JOIN lists.dimensions ON goods_dim_id = dim_id
LEFT JOIN store.accounts ON goods_account_id = account_id
LEFT JOIN administration.users ON goods_user_id = user_id";

//store.documents_clases – dokumentų klasės
$view_queries['store_documents_classes'] = "SELECT * FROM store.documents_classes
LEFT JOIN store.classes ON class_class = doc_class_class_id
LEFT JOIN administration.users ON user_id = doc_class_user_id ";

//store.contractors – tiekėjai, gavėjai
$view_queries['store_contractors'] = "SELECT * FROM store.contractors
LEFT JOIN administration.users ON user_id = contractor_user_id
LEFT JOIN store.documents_classes ON doc_class_id = contractor_class_id";

//store.contractor_details tiekėjų, gavėjų rekvizitai
$view_queries['store_contractor_details'] = "SELECT * FROM store.contractor_details
LEFT JOIN administration.users ON contractor_details_user_id = user_id";

//store.documents – pajamos pajamos iš tiekėjų, pardavimai, gražinimai
$view_queries['store_documents'] = "SELECT *
FROM store.documents
LEFT JOIN store.documents_classes ON doc_class_id = docs_class_id
LEFT JOIN store.contractors ON contractor_id = doc_supplier_id
LEFT JOIN store.stores ON store_id = doc_customer_id
LEFT JOIN administration.users AS users ON user_id = doc_user_id
LEFT JOIN administration.users_attributes ON doc_owner_id = user_attribute_user_id AND user_attribute_name = 'cn'";

//store.documents_rows – pajamų, išlaidų dokumentų eilutės
$view_queries['store_documents_rows'] = "SELECT *,
goods_title||CASE WHEN x.goods_ser_no !='' THEN ', '||x.goods_ser_no ELSE '' END AS goods_title,
budget_title||CASE WHEN y.object_title !='' THEN '. '||y.object_title ELSE '' END AS budget_fullname
FROM store.documents_rows
LEFT JOIN store.goods x ON goods_id = doc_row_goods_id
LEFT JOIN pert.budgets ON budget_id = doc_row_budget_id
LEFT JOIN pert.objects y ON budget_object_id = object_id
LEFT JOIN administration.users ON user_id = doc_row_user_id";

//store.store_documents – vidaus apyvarta
$view_queries['store_store_documents'] = "SELECT *,
supplier.store_title AS supplier_store_title,
customer.store_title AS customer_store_title
FROM store.store_documents
LEFT JOIN store.documents_classes ON doc_class_id = store_doc_class_id
LEFT JOIN store.stores AS supplier ON supplier.store_id = store_doc_supplier_id
LEFT JOIN store.stores AS customer ON customer.store_id = store_doc_customer_id
LEFT JOIN administration.users AS users ON user_id = store_doc_user_id
LEFT JOIN administration.users_attributes ON store_doc_owner_id = user_attribute_user_id AND user_attribute_name = 'cn'";


//store.store_doc_rows – vidaus apyvartos dokumentų eilutės
$view_queries['store_store_doc_rows'] = "SELECT *,
goods_title||CASE WHEN x.goods_ser_no !='' THEN ', '||x.goods_ser_no ELSE '' END AS goods_title,
y.budget_title||CASE WHEN z.object_title !='' THEN '. '||z.object_title ELSE '' END AS budget_fullname
FROM store.store_doc_rows
LEFT JOIN store.goods x ON store_doc_row_good_id = x.goods_id
LEFT JOIN pert.budgets y ON store_doc_row_budget_id = y.budget_id
LEFT JOIn pert.objects z ON y.budget_object_id = z.object_id
LEFT JOIN administration.users ON user_id = store_doc_row_user_id";

//store.stores – sandėliai
$view_queries['store_stores'] = "SELECT *, user_attribute_value AS position_fullname FROM store.stores
LEFT JOIN administration.users_attributes ON store_owner_id = user_attribute_user_id AND user_attribute_name = 'cn'
LEFT JOIN administration.users x ON store_user_id = x.user_id";

$view_queries['store_stores_store_goods_location'] = "SELECT *,
goods_title|| CASE WHEN x.goods_ser_no != '' THEN ', '||x.goods_ser_no ELSE '' END AS goods_title
FROM store.goods_location
LEFT JOIN store.goods x ON good_location_good_id = goods_id";

$view_queries['store_goods_location'] = "SELECT * FROM store.goods_location
LEFT JOIN store.stores ON store_id = good_location_store_id";

$view_queries['store_goods_history'] = "SELECT *,
goods_title||CASE WHEN x.goods_ser_no !='' THEN ', '||x.goods_ser_no ELSE '' END AS goods_title
FROM store.goods_history
LEFT JOIN store.goods x ON goods_history_goods_id = goods_id
LEFT JOIN store.stores ON goods_history_customer_id = store_id
LEFT JOIN pg_description ON goods_history_source_oid = objoid AND objsubid = 0
LEFT JOIN administration.users on goods_history_user_id =user_id";

$view_queries['store_goods_store_goods_history'] = "SELECT *
FROM store.goods_history
LEFT JOIN pg_description ON goods_history_source_oid = objoid AND objsubid = 0
LEFT JOIN store.stores ON goods_history_customer_id = store_id";
//
//Schema lists – įvairūs sąrašai
//
$view_queries['lists_fuel'] = "SELECT *
FROM lists.fuel
LEFT JOIN administration.users ON fuel_user_id = user_id";
//
// Schema TVT – turto valdymo tarnyba. Transportas.
//
$view_queries['tvt_cars_brands'] = "SELECT * FROM tvt.cars_brands_list_ordered
LEFT JOIN administration.users ON car_brand_user_id = user_id";

$view_queries['tvt_vehicles'] = "SELECT * FROM tvt.vehicles
LEFT JOIN tvt.cars_brands_list_ordered ON vehicle_brand_id = car_brand_id
LEFT JOIN lists.fuel ON vehicle_fuel_id = fuel_id
LEFT JOIN lists.vehicles_fuel_quota_using ON vehicle_fuel_quota_column_name = vehicle_fuel_quota_using
LEFT JOIN administration.users_attributes ON vehicle_owner_id = user_attribute_user_id AND user_attribute_name = 'cn'
LEFT JOIN administration.users ON vehicle_user_id = user_id";

$view_queries['tvt_vehicles_history'] = "SELECT * FROM tvt.vehicles_history
LEFT JOIN tvt.vehicles ON vehicle_history_vehicle_id = vehicle_id
LEFT JOIN administration.users ON vehicle_history_user_id = user_id";

$view_queries['tvt_vehicles_runs'] = "SELECT * FROM tvt.vehicles_runs
LEFT JOIN tvt.vehicles_full ON vehicle_run_vehicle_id = vehicle_id
LEFT JOIN administration.users ON vehicle_run_user_id = user_id
LEFT JOIN administration.users_attributes ON user_attribute_user_id = vehicle_run_owner_id AND user_attribute_name = 'cn'";
//
// Schema unipharma – Universiteto vaistinė.
//
// Užsakymų registras.
$view_queries['unipharma_orders_registry'] = "SELECT *
FROM unipharma.orders_registry
LEFT JOIN administration.users_attributes ON order_registry_initiator_id = user_attribute_user_id AND user_attribute_name = 'cn'
LEFT JOIN administration.users ON order_registry_user_id = user_id
LEFT JOIN structure.structure_tree_list ON order_registry_initiator_structure_node_path = structure_node_path
LEFT JOIN structure.structure_tree_list_0 ON order_registry_effector_structure_node_path = structure_node_path_0
LEFT JOIN lists.tasks_priorities ON order_registry_priority_id = task_priority_weight";

// Užsakymo eilutės
$view_queries['unipharma_order_recources'] = "SELECT *
FROM unipharma.order_recources
LEFT JOIN administration.users ON order_recource_user_id = user_id";

//unipharma.orders – užsakymai
$view_queries['unipharma_orders'] = "SELECT *,
initiator.user_attribute_value AS initiator_user_attribute_value,
effector.user_attribute_value AS effector_user_attribute_value,
inistruct.structure_fullname AS inistruct_structure_fullname,
effstruct.structure_fullname AS effstruct_structure_fullname,
changers.user_login AS changers_user_login
FROM unipharma.orders
LEFT JOIN unipharma.orders_registry ON order_reg_id = order_registry_id
LEFT JOIN administration.users_attributes AS initiator ON initiator.user_attribute_user_id = order_initiator_id AND initiator.user_attribute_name = 'cn'
LEFT JOIN administration.users_attributes AS effector ON effector.user_attribute_user_id = order_effector_id AND effector.user_attribute_name = 'cn'
LEFT JOIN administration.users z ON order_effector_id = z.user_id
LEFT JOIN administration.users AS changers ON order_user_id = changers.user_id
LEFT JOIN structure.structure_tree_list AS effstruct ON effstruct.structure_node_path = order_structure_node_path
LEFT JOIN structure.structure_tree_list AS inistruct ON inistruct.structure_node_path = order_initiator_structure_node_path
LEFT JOIN lists.tasks_priorities ON order_order_priority_id = task_priority_weight
LEFT JOIN lists.tasks_status ON order_order_status_id = task_status_id";

//unipharma.products - gaminiai ir žaliavos
$view_queries['unipharma_products'] = "SELECT * FROM unipharma.products
LEFT JOIN lists.dimensions ON product_dim_id = dim_id
LEFT JOIN administration.users ON product_user_id = user_id";

?>
