..  include:: /Includes.rst.txt


..  _commands:

=================================
Automation using symfony commands
=================================

This extension uses symfony commands to make some tasks easier and decoupled from frontend.
You can use crontab or the "Execute console commands" scheduler task to execute them.

Remove past periods and related data
====================================

Command: ``reserve:remove_past_periods``

Purpose: Remove past periods and all it's related data like orders and reservations.

By default this command removes all periods and related records from past periods.
Attention: This task removes the records using the DataHandler. This means that "remove" does not mean it is
removed from the database. The records are still in the database with a deleted flag.
You have to add a second scheduler Task / command `cleanup:deletedrecords` or scheduler task `Recycler: Remove deleted records`
to remove them permanently from the database!

Use the option ``--ended-since <amountOfSeconds>`` if you want to archive orders after a period is <amountOfSeconds> over.

Remove inactive orders
======================

Command: ``reserve:remove_inactive_orders``

Purpose: Remove inactive orders (orders with active = 0) after a given time.

Options:

+-----------------------+------------------------------------------------------------------------------------------------------------------+-----------+-----------+
| Option                | Description                                                                                                      | Default   | Required? |
+=======================+==================================================================================================================+===========+===========+
| ``--expiration-time`` | Expiration time of an inactive order in seconds                                                                  | 3600      | no        |
+-----------------------+------------------------------------------------------------------------------------------------------------------+-----------+-----------+
| ``--locale``          | Locale to be used inside templates and translations. Value that is available inside the Locales class (TYPO3     | default   | no        |
|                       | \\CMS\\Core\\Localization\\Locales). Example: "default" for english, "de" for german                             | = english |           |
+-----------------------+------------------------------------------------------------------------------------------------------------------+-----------+-----------+

Send mails
==========

Command: ``reserve:send_mails``

Purpose: Send mails using all active tx_reserve_domain_model_mail records as queue items.

Options:

+-----------------+------------------------------------------------------------------------------------------------------------------+-----------+-----------+
| Option          | Description                                                                                                      | Default   | Required? |
+=================+==================================================================================================================+===========+===========+
| ``--mailLimit`` | How many mails per execution?                                                                                    | 100       | no        |
+-----------------+------------------------------------------------------------------------------------------------------------------+-----------+-----------+
| ``--locale``    | Locale to be used inside templates and translations. Value that is available inside the Locales class (TYPO3     | default   | no        |
|                 | \\CMS\\Core\\Localization\\Locales). Example: "default" for english, "de" for german                             | = english |           |
+-----------------+------------------------------------------------------------------------------------------------------------------+-----------+-----------+

This command is a must have if you wanna notify your visitors when times of a period have changed! Notification mails will be sent using this
mail queue!
