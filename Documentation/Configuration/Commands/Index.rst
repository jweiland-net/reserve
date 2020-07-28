.. include:: ../Includes.txt

.. _commands:

=================================
Automation using symfony commands
=================================

This extension uses symfony commands to make some tasks easier and decoupled from frontend.
You can use crontab or the "Execute console commands" scheduler task (TYPO3 >= 9) to execute them.

Archive orders from past periods
=================================

Command: ``reserve:archive_orders``

Purpose: Archive orders from past periods.

By default this command archives all order records from past periods.
In this case archive means removing all personal information from the order and setting the order_type to archived.
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
