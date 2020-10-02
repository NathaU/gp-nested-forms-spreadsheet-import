# gp-nested-forms-spreadsheet-upload
A plugin that adds a spreadsheet upload to Nested Forms (Gravity Perks). Currently only CSV is supported, you can include a library like PhpSpreadsheet for Excel, Gnumeric or more support.

Install the files as a common Wordpress plugin and activate it. Since there is no admin panel configuration at the moment, you have to touch all files except `class-gpnf-session-override.php` manually for form field mapping and template modifications. You should be capable reading the database, especially `wp_gf_entry_meta` table, because you need to know which field ID should contain which value. Everything you have to change in this code is marked.
