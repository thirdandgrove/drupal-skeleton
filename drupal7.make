core = 7.x
api = 2

defaults[projects][subdir] = contrib
projects[] = drupal

; Core stuff.
projects[] = libraries
projects[] = masquerade
projects[] = date
projects[] = entity
projects[] = ember
projects[] = ctools
projects[] = views
projects[] = admin_views
projects[] = views_bulk_operations
projects[] = features
projects[] = token
projects[] = panels
projects[] = quickedit
projects[] = ckeditor
projects[] = pathauto
projects[] = filefield_paths
projects[] = redirect
projects[] = globalredirect
projects[] = navbar
projects[] = responsive_preview

; Development helpers never enabled on production.
projects[] = stage_file_proxy
projects[] = reroute_email
projects[] = devel
projects[] = diff
projects[] = module_filter
projects[] = security_review
projects[] = checklistapi

; Performance stuff.
projects[] = entitycache
projects[] = expire

; Acquia platform stuff.
projects[] = acquia_connector
