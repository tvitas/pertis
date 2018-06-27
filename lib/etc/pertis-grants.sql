--
-- find-replace user, then execute sql to make grants on database pertis
--

-- SCHEMAS
-- pert
GRANT ALL ON SCHEMA pert TO You_User_Name;
GRANT EXECUTE ON FUNCTION pert.task_recources_2_store_docs(bigint) TO You_User_Name;
GRANT EXECUTE ON FUNCTION pert.tr2t() TO You_User_Name;
GRANT ALL ON SEQUENCE pert.budgets_budget_id_seq TO You_User_Name;
GRANT ALL ON SEQUENCE pert.budgets_rows_budgets_rows_id_seq TO You_User_Name;
GRANT ALL ON SEQUENCE pert.objects_history_object_history_id_seq TO You_User_Name;
GRANT ALL ON SEQUENCE pert.objects_object_id_seq TO You_User_Name;
GRANT ALL ON SEQUENCE pert.tasks_effectors_task_effector_id_seq TO You_User_Name;
GRANT ALL ON SEQUENCE pert.tasks_recources_task_recource_id_seq TO You_User_Name;
GRANT ALL ON SEQUENCE pert.tasks_registry_task_registry_id_seq TO You_User_Name;
GRANT ALL ON SEQUENCE pert.tasks_task_id_seq TO You_User_Name;
GRANT ALL ON TABLE pert.budgets TO You_User_Name;
GRANT ALL ON TABLE pert.budgets_rows TO You_User_Name;
GRANT ALL ON TABLE pert.objects TO You_User_Name;
GRANT ALL ON TABLE pert.objects_history TO You_User_Name;
GRANT ALL ON TABLE pert.tasks TO You_User_Name;
GRANT ALL ON TABLE pert.tasks_effectors TO You_User_Name;
GRANT ALL ON TABLE pert.tasks_recources TO You_User_Name;
GRANT ALL ON TABLE pert.tasks_registry TO You_User_Name;
GRANT EXECUTE ON FUNCTION pert.budget_relate_to_object() TO You_User_Name;
GRANT EXECUTE ON FUNCTION pert.budget_row_calc() TO You_User_Name;
GRANT EXECUTE ON FUNCTION pert.object_calc() TO You_User_Name;
GRANT EXECUTE ON FUNCTION pert.task_2_task_registry() TO You_User_Name;
GRANT EXECUTE ON FUNCTION pert.task_recources_calc() TO You_User_Name;
GRANT EXECUTE ON FUNCTION pert.tasks_registry_2_task() TO You_User_Name;
-- admin
GRANT ALL ON SCHEMA administration TO You_User_Name;
GRANT ALL ON SEQUENCE administration.ad_attributes_ad_attribute_id_seq TO You_User_Name;
GRANT ALL ON SEQUENCE administration.role_rights_role_rights_id_seq TO You_User_Name;
GRANT ALL ON SEQUENCE administration.roles_role_id_seq TO You_User_Name;
GRANT ALL ON SEQUENCE administration.users_attributes_user_attribute_id_seq TO You_User_Name;
GRANT ALL ON SEQUENCE administration.users_user_id_seq TO You_User_Name;
GRANT ALL ON TABLE administration.ad_attributes TO You_User_Name;
GRANT ALL ON TABLE administration.role_rights TO You_User_Name;
GRANT ALL ON TABLE administration.roles TO You_User_Name;
GRANT ALL ON TABLE administration.users TO You_User_Name;
GRANT ALL ON TABLE administration.users_attributes TO You_User_Name;
GRANT EXECUTE ON FUNCTION administration.role_rights_add_tables_list() TO You_User_Name;
GRANT EXECUTE ON FUNCTION administration.roles_deffer_role_id_eq_1_2() TO You_User_Name;
GRANT EXECUTE ON FUNCTION administration.users_check_is_defferable() TO You_User_Name;
-- lists
GRANT ALL ON SCHEMA lists TO You_User_Name;
GRANT ALL ON SEQUENCE lists.degrees_degrees_id_seq TO You_User_Name;
GRANT ALL ON SEQUENCE lists.dimensions_dim_id_seq TO You_User_Name;
GRANT ALL ON SEQUENCE lists.fuel_fuel_id_seq TO You_User_Name;
GRANT ALL ON SEQUENCE lists.tasks_priorities_task_priority_id_seq TO You_User_Name;
GRANT ALL ON SEQUENCE lists.tasks_status_task_status_id_seq TO You_User_Name;
GRANT ALL ON SEQUENCE lists.vehicles_fuel_quota_using_vehicle_fuel_quota_using_id_seq TO You_User_Name;
GRANT ALL ON TABLE lists.degrees TO You_User_Name;
GRANT ALL ON TABLE lists.dimensions TO You_User_Name;
GRANT ALL ON TABLE lists.fuel TO You_User_Name;
GRANT ALL ON TABLE lists.tasks_priorities TO You_User_Name;
GRANT ALL ON TABLE lists.tasks_status TO You_User_Name;
GRANT ALL ON TABLE lists.vehicles_fuel_quota_using TO You_User_Name;
-- reports
GRANT ALL ON SCHEMA reports TO You_User_Name;
GRANT ALL ON SEQUENCE reports.db_events_db_event_id_seq TO You_User_Name;
GRANT ALL ON SEQUENCE reports.reports_report_id_seq TO You_User_Name;
GRANT ALL ON SEQUENCE reports.reports_sections_report_section_id_seq TO You_User_Name;
GRANT ALL ON TABLE reports.db_events TO You_User_Name;
GRANT ALL ON TABLE reports.reports TO You_User_Name;
GRANT ALL ON TABLE reports.reports_sections TO You_User_Name;
-- store
GRANT ALL ON SCHEMA store TO You_User_Name;
GRANT EXECUTE ON FUNCTION store.goods_recalc_categories_prices() TO You_User_Name;
GRANT ALL ON SEQUENCE store.accounts_account_id_seq TO You_User_Name;
GRANT ALL ON SEQUENCE store.classes_class_id_seq TO You_User_Name;
GRANT ALL ON SEQUENCE store.contractor_details_contractor_details_id_seq TO You_User_Name;
GRANT ALL ON SEQUENCE store.contractors_contractor_id_seq TO You_User_Name;
GRANT ALL ON SEQUENCE store.doc_types_doc_type_id_seq TO You_User_Name;
GRANT ALL ON SEQUENCE store.documents_details_doc_details_id_seq TO You_User_Name;
GRANT ALL ON SEQUENCE store.documents_doc_id_seq TO You_User_Name;
GRANT ALL ON SEQUENCE store.goods_good_id_seq TO You_User_Name;
GRANT ALL ON SEQUENCE store.goods_history_goods_history_id_seq TO You_User_Name;
GRANT ALL ON SEQUENCE store.goods_locations_good_location_id_seq TO You_User_Name;
GRANT ALL ON SEQUENCE store.invoices_invoice_id_seq TO You_User_Name;
GRANT ALL ON SEQUENCE store.store_docs_details_doc_details_id_seq TO You_User_Name;
GRANT ALL ON SEQUENCE store.store_documents_store_doc_id_seq TO You_User_Name;
GRANT ALL ON SEQUENCE store.stores_store_id_seq TO You_User_Name;
GRANT ALL ON TABLE store.accounts TO You_User_Name;
GRANT ALL ON TABLE store.classes TO You_User_Name;
GRANT ALL ON TABLE store.contractor_details TO You_User_Name;
GRANT ALL ON TABLE store.contractors TO You_User_Name;
GRANT ALL ON TABLE store.documents TO You_User_Name;
GRANT ALL ON TABLE store.documents_classes TO You_User_Name;
GRANT ALL ON TABLE store.documents_rows TO You_User_Name;
GRANT ALL ON TABLE store.goods TO You_User_Name;
GRANT ALL ON TABLE store.goods_history TO You_User_Name;
GRANT ALL ON TABLE store.goods_location TO You_User_Name;
GRANT ALL ON TABLE store.store_doc_rows TO You_User_Name;
GRANT ALL ON TABLE store.store_documents TO You_User_Name;
GRANT ALL ON TABLE store.stores TO You_User_Name;
GRANT EXECUTE ON FUNCTION store.deffer_class() TO You_User_Name;
GRANT EXECUTE ON FUNCTION store.deffer_contractors_eq_1() TO You_User_Name;
GRANT EXECUTE ON FUNCTION store.deffer_document_class_eq_1() TO You_User_Name;
GRANT EXECUTE ON FUNCTION store.deffer_store_eq_1() TO You_User_Name;
GRANT EXECUTE ON FUNCTION store.documents_calc() TO You_User_Name;
GRANT EXECUTE ON FUNCTION store.documents_rows_calc() TO You_User_Name;
GRANT EXECUTE ON FUNCTION store.goods_calc_amount() TO You_User_Name;
GRANT EXECUTE ON FUNCTION store.store_documents_calc() TO You_User_Name;
GRANT EXECUTE ON FUNCTION store.store_documents_rows_calc() TO You_User_Name;
-- structure
GRANT ALL ON SCHEMA structure TO You_User_Name;
GRANT EXECUTE ON FUNCTION structure.get_places_node_path(integer) TO You_User_Name;
GRANT EXECUTE ON FUNCTION structure.get_structure_node_path(integer) TO You_User_Name;
GRANT ALL ON SEQUENCE structure.places_place_id_seq TO You_User_Name;
GRANT ALL ON SEQUENCE structure.positions_postion_id_seq TO You_User_Name;
GRANT ALL ON SEQUENCE structure.staff_staff_id_seq TO You_User_Name;
GRANT ALL ON SEQUENCE structure.structure_structure_id_seq TO You_User_Name;
GRANT ALL ON TABLE structure.places TO You_User_Name;
GRANT ALL ON TABLE structure.positions TO You_User_Name;
GRANT ALL ON TABLE structure.staff TO You_User_Name;
GRANT ALL ON TABLE structure.structure TO You_User_Name;
GRANT EXECUTE ON FUNCTION structure.set_staff_title() TO You_User_Name;
GRANT ALL ON TABLE structure.places_list_ordered TO You_User_Name;
GRANT ALL ON TABLE structure.places_tree_list TO You_User_Name;
GRANT ALL ON TABLE structure.structure_list_ordered TO You_User_Name;
GRANT ALL ON TABLE structure.structure_list_ordered_0 TO You_User_Name;
GRANT ALL ON TABLE structure.structure_tree_list TO You_User_Name;
GRANT ALL ON TABLE structure.structure_tree_list_0 TO You_User_Name;
-- system_schema
GRANT ALL ON SCHEMA system_schema TO You_User_Name;
GRANT ALL ON SEQUENCE system_schema.dashboard_dashboard_id_seq TO You_User_Name;
GRANT ALL ON SEQUENCE system_schema.dashboard_items_dashboard_item_id_seq TO You_User_Name;
GRANT ALL ON SEQUENCE system_schema.files_file_id_seq TO You_User_Name;
GRANT ALL ON SEQUENCE system_schema.languages_lang_id_seq TO You_User_Name;
GRANT ALL ON SEQUENCE system_schema.properties_properties_id_seq TO You_User_Name;
GRANT ALL ON SEQUENCE system_schema.relations_relations_id_seq TO You_User_Name;
GRANT ALL ON SEQUENCE system_schema.selects_selects_id_seq TO You_User_Name;
GRANT ALL ON SEQUENCE system_schema.selects_sql_select_sql_id_seq TO You_User_Name;
GRANT ALL ON SEQUENCE system_schema.strings_string_id_seq TO You_User_Name;
GRANT ALL ON SEQUENCE system_schema.translates_translate_id_seq TO You_User_Name;
GRANT ALL ON SEQUENCE system_schema.trees_trees_id_seq TO You_User_Name;
GRANT ALL ON TABLE system_schema.dashboard TO You_User_Name;
GRANT ALL ON TABLE system_schema.dashboard_items TO You_User_Name;
GRANT ALL ON TABLE system_schema.files TO You_User_Name;
GRANT ALL ON TABLE system_schema.languages TO You_User_Name;
GRANT ALL ON TABLE system_schema.properties TO You_User_Name;
GRANT ALL ON TABLE system_schema.relations TO You_User_Name;
GRANT ALL ON TABLE system_schema.selects TO You_User_Name;
GRANT ALL ON TABLE system_schema.selects_sql TO You_User_Name;
GRANT ALL ON TABLE system_schema.strings TO You_User_Name;
GRANT ALL ON TABLE system_schema.translates TO You_User_Name;
GRANT ALL ON TABLE system_schema.trees TO You_User_Name;
-- tvt
GRANT ALL ON SCHEMA tvt TO You_User_Name;
GRANT EXECUTE ON FUNCTION tvt.get_cars_brands_node_path(integer) TO You_User_Name;
GRANT EXECUTE ON FUNCTION tvt.vehicle_2_vehicle_run(bigint) TO You_User_Name;
GRANT ALL ON SEQUENCE tvt.cars_brands_car_brand_id_seq TO You_User_Name;
GRANT ALL ON SEQUENCE tvt.vehicles_history_vehicle_history_id_seq TO You_User_Name;
GRANT ALL ON SEQUENCE tvt.vehicles_runs_vehicle_run_id_seq TO You_User_Name;
GRANT ALL ON SEQUENCE tvt.vehicles_vehicle_id_seq TO You_User_Name;
GRANT ALL ON TABLE tvt.cars_brands TO You_User_Name;
GRANT ALL ON TABLE tvt.vehicles TO You_User_Name;
GRANT ALL ON TABLE tvt.vehicles_history TO You_User_Name;
GRANT ALL ON TABLE tvt.vehicles_runs TO You_User_Name;
GRANT EXECUTE ON FUNCTION tvt.vehicle_calc_and_log() TO You_User_Name;
GRANT EXECUTE ON FUNCTION tvt.vehicles_runs_calc_and_log() TO You_User_Name;
GRANT ALL ON TABLE tvt.cars_brands_list_ordered TO You_User_Name;
GRANT ALL ON TABLE tvt.vehicles_full TO You_User_Name;
