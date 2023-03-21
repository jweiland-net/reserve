..  include:: /Includes.rst.txt


..  _installation:

============
Installation
============

Composer
========

If your TYPO3 installation works in composer mode, please execute following
command:

..  code-block:: bash

    composer req jweiland/reserve
    vendor/bin/typo3 extension:setup --extension=reserve

If you work with DDEV please execute this command:

..  code-block:: bash

    ddev composer req jweiland/reserve
    ddev exec vendor/bin/typo3 extension:setup --extension=reserve

ExtensionManager
================

On non composer based TYPO3 installations you can install `reserve` still over
the ExtensionManager:

..  rst-class:: bignums

1.  Login

    Login to backend of your TYPO3 installation as an administrator or system
    maintainer.

2.  Open ExtensionManager

    Click on `Extensions` from the left menu to open the ExtensionManager.

3.  Update Extensions

    Choose `Get Extensions` from the upper selectbox and click on
    the `Update now` button at the upper right.

4.  Install `reserve`

    Use the search field to find `reserve`. Choose the `reserve` line from the
    search result and click on the cloud icon to install `reserve`.

Example structure and plugin usage
===================================

..  figure:: ../Images/ExampleStructure.png
    :class: with-shadow
    :alt: Example structure
    :width: 300px

The page "Get tickets" contains the plugin "Display and reserve
periods [reserve_reservation]" that shows a list of available time slots and
allows the user to get a ticket. The static templates "Reserve"
and "Reserve reservation" are required! The
Folder "Facility, Periods and Orders" is set as "Storage folder for orders"
inside the plugin configuration.

The page "Scan tickets" contains the
plugin "Manage reservations and scan reservation codes [reserve_management]" and
is protected using the fe_user login. The plugin must not be accessible by
website visitors! It will be used to activate the reservation codes using a
QR Code scanner. The static templates "Reserve" and "Reserve management" are
required!

Next step
=========

:ref:`Configure reserve <configuration>`.
