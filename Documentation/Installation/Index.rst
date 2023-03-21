..  include:: /Includes.rst.txt


..  _installation:

=============
Installation
=============

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
