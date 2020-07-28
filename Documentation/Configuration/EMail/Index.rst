.. include:: ../Includes.txt

.. _email:

==================================
E-Mail templates and configuration
==================================

Configuration per Facility
==========================

.. figure:: ../../Images/EmailSettingsTca.png
   :class: with-shadow
   :alt: TCA
   :width: 500px

You can find all facility based "E-Mail settings" using the tab "E-Mail settings" when editing a facility in the backend.

Custom E-Mail templates
=======================

There are some more options customization options if the customization of texts is not enough for you.

Override mail fluid templates
-----------------------------

You can override the mail fluid templates using the following constants. You can use the constant editor for it too.
Important: Put those constants into Page ID 1 to override them in commands too!

.. code-block:: typoscript

   plugin.tx_reserve {
     view {
       templateRootPath  = EXT:sitepackage/Resources/Private/Templates/
       partialRootPath = EXT:sitepackage/Resources/Private/Partials/
       layoutRootPath = EXT:sitepackage/Resources/Private/Layouts/
     }
   }

Override language labels
------------------------

Sometimes it's just a label that needs to be changed. In this case you can just override the label using
TypoScript.

.. code-block:: typoscript

   # override english label
   plugin.tx_reserve._LOCAL_LANG.default.form.order.submit = Get your ticket!
   # override german label
   plugin.tx_reserve._LOCAL_LANG.de.form.order.submit = Hol dir dein Ticket!
