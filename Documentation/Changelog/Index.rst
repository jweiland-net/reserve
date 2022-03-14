.. include:: ../Includes.txt

.. _changelog:

=========
Changelog
=========

Version 2.0.2
-------------

   2022-03-14 [TASK] Fix injection of dispatcher for v11 (Commit cb556ed by Pascal Rinker)
   2022-03-14 [TASK] Use di for dispatcher in validator (Commit 63d93ae by Pascal Rinker)
   2022-03-14 [TASK] Fix ManagementController namespace (Commit 70b543e by Pascal Rinker)

This list has been created by using `git log $(git describe --tags --abbrev=0)..HEAD --abbrev-commit --pretty='%ad %s (Commit %h by %an)' --date=short`.

Version 2.0.1
-------------

List of changes ::

   2022-03-11 [BUGFIX] Fix order of periods in list table (Commit 78ed415 by Pascal Rinker)

This list has been created by using `git log $(git describe --tags --abbrev=0)..HEAD --abbrev-commit --pretty='%ad %s (Commit %h by %an)' --date=short`.

Version 2.0.0
-------------

List of changes ::

   2022-03-10 [TASK] Fix isBookable() condition, use redirect instead of forward (Commit 952ed9c by Pascal Rinker)
   2022-03-10 Merge pull request #107 from jweiland-net/typo311 (Commit db041f8 by Pascal Rinker)
   2022-03-10 [TASK] Fix CheckoutServiceTest (Commit 54df921 by Pascal Rinker)
   2022-03-10 [TASK] Fix fixture (Commit c508e5e by Pascal Rinker)
   2022-03-10 [TASK] Remove PHP 7.2 from CI (Commit cf1982d by Pascal Rinker)
   2022-03-10 [TASK] Check in formAction and createAction if period if bookable (Commit 23e0355 by Pascal Rinker)
   2022-03-09 [TASK] Use Environent class instead of checking constant (Commit a6f4046 by Pascal Rinker)
   2022-03-09 [TASK] Add qr-code library to composer.json and fallback without phar, fix tests for v11 (Commit 051f0e1 by Pascal Rinker)
   2022-03-04 [TASK] Fix tests (Commit e05d5eb by Pascal Rinker)
   2022-03-04 [TASK] Increase testing framework version (Commit e359301 by Pascal Rinker)
   2022-03-04 [TASK] Update workflow (Commit 7fda09c by Pascal Rinker)
   2022-02-11 [TASK] Use new way to register and configure plugin (Commit cb0a3ef by Pascal Rinker)
   2022-02-11 [TASK] Add TYPO3 v11 compatibility and remove TYPO3 v9 support (Commit 7de6eda by Pascal Rinker)

This list has been created by using `git log $(git describe --tags --abbrev=0)..HEAD --abbrev-commit --pretty='%ad %s (Commit %h by %an)' --date=short`.

Version 1.2.5
-------------

List of changes ::

   2021-12-16 Add organization and remarks to possible order fields (Commit 515089d by Stefan Froemken)

This list has been created by using `git log $(git describe --tags --abbrev=0)..HEAD --abbrev-commit --pretty='%ad %s (Commit %h by %an)' --date=short`.

Version 1.2.4
-------------

List of changes ::

   2021-12-16 Add tests with negative values (Commit d8d8667 by Stefan Froemken)
   2021-12-16 Add much more tests. Fix canBeBooked (Commit 2266665 by Stefan Froemken)

This list has been created by using `git log $(git describe --tags --abbrev=0)..HEAD --abbrev-commit --pretty='%ad %s (Commit %h by %an)' --date=short`.

Version 1.2.3
-------------

List of changes ::

   2021-11-30 [TASK] Remove required field check for further participants (Commit 1df8d5a by Pascal Rinker)

This list has been created by using `git log $(git describe --tags --abbrev=0)..HEAD --abbrev-commit --pretty='%ad %s (Commit %h by %an)' --date=short`.

Version 1.2.2
-------------

List of changes ::

   2021-11-23 [BUGFIX] Change return type of staticRender back to string in QrCodeViewHelper (Commit by Stefan Froemken)

Version 1.2.1
-------------

List of changes ::

   2021-11-19 [BUGFIX] Allow NULL as return value for getBegin (Commit by Stefan Froemken)

Version 1.2.0
-------------

The list of changes includes changes before 1.1.1 because it was no TER release.

List of changes ::

   2021-11-12 [TASK] Prepare for release 1.2.0 (Commit d3bc6d0 by Pascal Rinker)
   2021-10-29 Merge pull request #95 from jweiland-net/addMissingStrictTypes (Commit bfc371c by Pascal Rinker)
   2021-10-29 [TASK] Fix CheckoutController return type of actions with redirect (Commit ce8f636 by Pascal Rinker)
   2021-10-27  Update dependencies to secure TYPO3 versions (Commit 9f3538c by Stefan Froemken)
   2021-10-27 Add ChangLog and update version to 1.1.1 (Commit 0e3fddc by Stefan Froemken)
   2021-10-27 Use source insteadof source in translation file (Commit 63ed80f by Stefan Froemken)
   2021-10-22 Merge pull request #97 from jweiland-net/multipleFixes (Commit 0935cef by Pascal Rinker)
   2021-10-22 Merge pull request #96 from jweiland-net/modifyExtEmConf (Commit cf0c758 by Pascal Rinker)
   2021-10-22 [TASK] cs fixer (Commit 895d8a6 by Pascal Rinker)
   2021-10-22 [TASK] Add missing pluginName to PeriodRegistrationViewHelper examples (Commit 1ba2792 by Pascal Rinker)
   2021-10-22 [TASK] Shorten nearly endless row in VH (Commit 6f9af29 by Pascal Rinker)
   2021-10-22 [TASK] Remove unnecessary items from ext_emconf (Commit b207517 by Pascal Rinker)
   2021-10-22 [TASK] Add missing strict types (Commit 17890c5 by Pascal Rinker)
   2021-10-15 Add softRefParser to RTE fields (Commit 990937d by Stefan Frömken)
   2021-10-15 Add softRefParser to body field (Commit 6a19138 by Stefan Frömken)
   2021-06-11 Merge pull request #83 from jweiland-net/featureDynamicButton (Commit 3e64d37 by Pascal Rinker)
   2021-06-11 [TASK] Add sprintf (Commit 9b6b950 by Pascal Rinker)
   2021-06-11 [TASK] Update findByDateAndBegin and make DateTime more readable (Commit 05167ea by Pascal Rinker)
   2021-06-10 [TASK] Move test fluid templates into Fixtures directory (Commit a612f4e by Pascal Rinker)
   2021-06-09 [DOCS] Update docs (Commit a134f64 by Pascal Rinker)
   2021-06-09 [TASK] Add "as" option to PeriodRegistrationViewHelper, update tests (Commit d22e364 by Pascal Rinker)
   2021-06-09 [DOCS] Add documentation for the new PeriodRegistrationViewHelper (Commit 8e0f9f8 by Pascal Rinker)
   2021-06-09 [FEATURE] Add ViewHelper that can display remaining participants of a period (Commit abd3592 by Pascal Rinker)
   2021-06-09 Merge pull request #82 from jweiland-net/featureApi (Commit f81cc83 by Pascal Rinker)
   2021-06-08 [TASK] Some fixes (Commit 1591a06 by Pascal Rinker)
   2021-06-08 [TASK] Fix tests (Commit c9a4625 by Pascal Rinker)
   2021-06-08 [TASK] Use dynamic dates in functional tests (Commit 8c3ca98 by Pascal Rinker)
   2021-06-08 [TASK] Update ci (Commit 1ab6d89 by Pascal Rinker)
   2021-06-08 [FEATURE] Add ReserveService as part of a public API (Commit f473d31 by Pascal Rinker)
   2021-06-07 [TASK] Remove unused method (Commit 8c3a297 by Pascal Rinker)
   2021-06-07 Merge branch 'master' of github.com:jweiland-net/reserve (Commit 293ac7a by Pascal Rinker)
   2021-06-07 [FEATURE] Support multiple facilities in one list view (Commit 7898add by Pascal Rinker)
   2021-06-07 Add new fields to register for an EXT:events2 location (Commit 4269885 by Stefan Froemken)
   2021-03-26 [TASK] Add reserve class to scanner modal (Commit c1d8007 by Pascal Rinker)

This list has been created by using `git log $(git describe --tags --abbrev=0)..HEAD --abbrev-commit --pretty='%ad %s (Commit %h by %an)' --date=short`.

Version 1.1.1
-------------

List of changes ::

   2021-10-27 Use source instead of target in translation file (Commit 2f80f5d by Stefan Froemken)


Version 1.1.0
-------------

List of changes ::

   2021-03-22 Merge branch 'master' of github.com:jweiland-net/reserve (Commit ce140aa by Pascal Rinker)
   2021-03-22 [TASK] Add flash message if order could not be canceled (Commit 1b26235 by Pascal Rinker)
   2021-03-22 Merge pull request #77 from DanielSiepmann/feature/8417-reset-countdown-after-cancel (Commit eec2529 by Pascal Rinker)
   2021-03-22 [TASK] Check if period update affects orders before rendering the modal (Commit 7675c37 by Pascal Rinker)
   2021-03-22 [TASK] Update github workflow (Commit 58c210d by Pascal Rinker)
   2021-03-22 [BUGFIX] Flash message is beeing cached in Checkout::listAction (Commit b53b7bc by Pascal Rinker)
   2021-03-22 [FEATURE] Add setting to decide which form fields are marked as required (Commit a7e65a4 by Pascal Rinker)
   2021-03-10 Reset session after cancel was successful (Commit 0587968 by Daniel Siepmann)
   2021-03-09 [TASK] Fix scanner exception (Commit 51538a1 by Pascal Rinker)
   2021-03-04 Add extension key to composer.json #73 (Commit 0954547 by Stefan Frömken)
   2021-02-26 Update locallang.xlf (Commit 6f15f3b by og-fox)
   2021-02-26 Use correct spaces in de.locallang.xlf (Commit abe1fc2 by Stefan Frömken)
   2021-02-26 Add space to make translation available in CrowdIn (Commit ff75ddf by Stefan Frömken)
   2021-02-26 [BUGFIX] Fix max amount of further participants if further participants is a number field (Commit 38e3357 by Pascal Rinker)

This list has been created by using `git log $(git describe --tags --abbrev=0)..HEAD --abbrev-commit --pretty='%ad %s (Commit %h by %an)' --date=short`.

Version 1.0.0
-------------

List of changes ::

   2021-01-28 [DOCS] Add route enhancers example configuration (Commit 6f29df9 by Pascal Rinker)
   2021-01-27 [TASK] Add some more functional tests for CheckoutService (Commit ba2f6fb by Pascal Rinker)
   2021-01-27 [TASK] Fix unit test (Commit 92851bf by Pascal Rinker)
   2021-01-27 [FEATURE] Allow time slots with open end (Commit b009b72 by Pascal Rinker)
   2021-01-27 [TASK] Remove unused stuff for TYPO3 v8 (Commit 26ea89d by Pascal Rinker)
   2021-01-27 Merge branch 'master' of https://github.com/jweiland-net/reserve (Commit 742f39a by Pascal Rinker)
   2021-01-27 [FEATURE] Add more customization options for frontend (Commit d5600b4 by Pascal Rinker)


This list has been created by using `git log $(git describe --tags --abbrev=0)..HEAD --abbrev-commit --pretty='%ad %s (Commit %h by %an)' --date=short`.

Version 0.0.2
-------------

Bugfix: Use correct Namespace for QR Controller

Version 0.0.1
-------------

Initial upload
