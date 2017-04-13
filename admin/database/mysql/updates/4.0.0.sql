ALTER TABLE `#__jsn_uniform_forms` ADD `form_meta_keywords` TEXT NOT NULL DEFAULT '';
ALTER TABLE `#__jsn_uniform_forms` ADD `form_meta_desc` TEXT NOT NULL DEFAULT '';
ALTER TABLE `#__jsn_uniform_forms` ADD `form_meta_title` tinyint(1) unsigned NOT NULL DEFAULT 0;

ALTER TABLE `#__jsn_uniform_forms` ADD `form_script_display` TEXT NOT NULL DEFAULT '';
ALTER TABLE `#__jsn_uniform_forms` ADD `form_script_on_process` TEXT NOT NULL DEFAULT '';
ALTER TABLE `#__jsn_uniform_forms` ADD `form_script_on_processed` TEXT NOT NULL DEFAULT '';

