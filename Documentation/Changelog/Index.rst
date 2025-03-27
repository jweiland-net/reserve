..  include:: /Includes.rst.txt


..  _changelog:

=========
Changelog
=========

Version 2.2.0
=============

2025-03-18 Backporting QR Code Disabling Functionality
2025-03-26 Backporting SendEmailEvent from new version

Version 2.1.0
=============

2023-03-21 Add better structure to DataTablesService (Commit 821e7de by Stefan Froemken)
2023-03-21 Remove spaces from scanner link list (Commit c4071ff by Stefan Froemken)
2023-03-21 Add entry for newContentElementWizard (Commit 4774955 by Stefan Froemken)
2023-03-21 Merge pull request #118 from jweiland-net/applyNewPhpCsFixerConfiguration (Commit e851e41 by Stefan Frömken)
2023-03-21 Split two GitHub actions by enter (Commit 7513aba by Stefan Froemken)
2023-03-21 Repair tests (Commit 98e8777 by Stefan Froemken)
2023-03-21 Inject Dispatcher as SingletonInterface (Commit ff1aa85 by Stefan Froemken)
2023-03-21 Repair tests (Commit a0bbba0 by Stefan Froemken)
2023-03-21 Apply php-cs-fixer format to Test files (Commit f7705e4 by Stefan Froemken)
2023-03-21 Do not modernize strpos in php-cs-fixer because of PHP 7 support (Commit 8943eb3 by Stefan Froemken)
2023-03-21 Remove tests against PHP 7.3 (Commit 3509bcb by Stefan Froemken)
2023-03-21 Add missing caret to TYPO3 version in composer.json (Commit c1faddf by Stefan Froemken)
2023-03-21 Update version to 2.1.0 (Commit 6807fcc by Stefan Froemken)
2023-03-21 Update installation documentation (Commit 63d9795 by Stefan Froemken)
2023-03-21 Update ChangeLog (Commit 958eee6 by Stefan Froemken)
2023-03-21 Get translations from core instead of EXT:lang (Commit 9c3706d by Stefan Froemken)
2023-03-21 Move QueryBuilder instanciation into own methods (Commit 0da8dee by Stefan Froemken)
2023-03-21 Use logicalAnd with array (Commit 37efb12 by Stefan Froemken)
2023-03-21 Remove unused name attribute from clearPageCacheAndAddFacilityName (Commit 62e3ff2 by Stefan Froemken)
2023-03-21 Remove unused name attribute from clearPageCacheAndAddFacilityName (Commit 62e3ff2 by Stefan Froemken)
2023-03-21 Update indents of docs to 4 spaces (Commit eb788f5 by Stefan Froemken)
2023-03-21 Add new format to HTML templates (Commit 8139cc9 by Stefan Froemken)
2023-03-21 Update format in ext_tables.sql (Commit 41ca215 by Stefan Froemken)
2023-03-21 Update developer in ext_emconf.php (Commit f36c6da by Stefan Froemken)
2023-03-21 Update php-cs-fixer to at least version 3.14 (Commit cf4b900 by Stefan Froemken)
2023-03-21 Add .gitattributes file (Commit c245736 by Stefan Froemken)
2023-03-21 Update README.md (Commit fce9c5c by Stefan Froemken)
2023-03-21 Implement new editor config file (Commit 8124236 by Stefan Froemken)
2023-03-21 Implement new .gitignore (Commit daf38dc by Stefan Froemken)
2023-03-21 Migrate Commands.php to Services.yaml (Commit 804864e by Stefan Froemken)
2023-03-21 Apply php-cs-fixer to TCA files (Commit 9773ff1 by Stefan Froemken)
2023-03-21 CleanUp ViewHelpers. Make ReserveService public in DI (Commit 4dccf22 by Stefan Froemken)
2023-03-21 Migrate AbstractUtility to Trait (Commit 6442822 by Stefan Froemken)
2023-03-21 Remove superflous method annotations from CreateForeignTableColumns (Commit c800908 by Stefan Froemken)
2023-03-21 Remove superflous method annotations from ReserviceService (Commit 8b96ea9 by Stefan Froemken)
2023-03-21 Remove superflous type cast in MailService (Commit da0eecb by Stefan Froemken)
2023-03-21 Move MailService into constructor DI in CheckoutService (Commit 09ca545 by Stefan Froemken)
2023-03-21 Do not instantiate StandaloneView with constructor DI (Commit 65280d8 by Stefan Froemken)
2023-03-21 Use Core PageRenderer instead of instantiating a new one (Commit 9d7f285 by Stefan Froemken)
2023-03-21 Apply php-cs-fixer settings to ext_localconf.php (Commit efcc468 by Stefan Froemken)
2023-03-21 Use 32bit compatible timestamps in validator (Commit dd4bcaf by Stefan Froemken)
2023-03-21 Update structure of ReservationRepository (Commit e567f2a by Stefan Froemken)
2023-03-21 Remove constructor from PeriodRepository (Commit 1adda04 by Stefan Froemken)
2023-03-21 Restructure OrderRepository (Commit 059b194 by Stefan Froemken)
2023-03-21 Move DB functions into own method (Commit 29adfb3 by Stefan Froemken)
2023-03-21 Remove superflous annotations from methods in models (Commit 97e0ffa by Stefan Froemken)
2023-03-21 Move DB instantiation into own methods (Commit 9dbc6b3 by Stefan Froemken)
2023-03-21 Set hook classes as public in DI (Commit f49a0b6 by Stefan Froemken)
2023-03-21 Move instantiation of classes into own methods (Commit e7f1bab by Stefan Froemken)
2023-03-21 Remove superflous annotations from FacilityRepository (Commit 2a6f08a by Stefan Froemken)
2023-03-21 Move additional default config into own method (Commit 3f3ec56 by Stefan Froemken)
2023-03-21 Remove superflous condition from ManagementController (Commit caf7675 by Stefan Froemken)
2023-03-21 Move instantiation of View in to own method (Commit 5160787 by Stefan Froemken)
2023-03-21 Implement better structure to DataTablesService (Commit 577a9b5 by Stefan Froemken)
2023-03-21 Remove superfluous type cast in QrCodeController (Commit 2cfac54 by Stefan Froemken)
2023-03-21 Move instantiation of ExtConf into own method (Commit c5df780 by Stefan Froemken)
2023-03-21 Declare ExtConf as public for DI (Commit 65920c6 by Stefan Froemken)
2023-03-21 Use DI for ExtensionConfiguration in ExtConf (Commit 7cf249f by Stefan Froemken)
2023-03-21 Implement new php-cs-fixer config (Commit a014fa5 by Stefan Froemken)
2023-03-21 Use secure dependencies to TYPO3 (Commit 4910755 by Stefan Froemken)
2023-03-21 Use secure homepage in composer.json (Commit e07f214 by Stefan Froemken)
2023-03-21 Update developers in composer.json (Commit cb3762c by Stefan Froemken)

Version 2.0.3
=============

2022-05-16 Check date for DateTime before calling format() (Commit 9243ba3 by Stefan Froemken)
2022-05-16 Add Test for empty begin in period model (Commit 7a0abe6 by Stefan Froemken)
2022-05-16 Check date for DateTime before calling format() (Commit 03987e9 by Stefan Froemken)
2022-05-16 Remove .DS_Store files (Commit 74f97ac by Stefan Froemken)
2022-03-15 [TASK] Remove strict type from Order::getParticipants (Commit aa773e4 by Pascal Rinker)

Version 2.0.2
=============

2022-03-14 [TASK] Fix injection of dispatcher for v11 (Commit cb556ed by Pascal Rinker)
2022-03-14 [TASK] Use di for dispatcher in validator (Commit 63d93ae by Pascal Rinker)
2022-03-14 [TASK] Fix ManagementController namespace (Commit 70b543e by Pascal Rinker)

This list has been created by using `git log $(git describe --tags --abbrev=0)..HEAD --abbrev-commit --pretty='%ad %s (Commit %h by %an)' --date=short`.

Version 2.0.1
=============

2022-03-11 [BUGFIX] Fix order of periods in list table (Commit 78ed415 by Pascal Rinker)

Version 2.0.0
=============

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
=============

2021-12-16 Add organization and remarks to possible order fields (Commit 515089d by Stefan Froemken)

Version 1.2.4
=============

2021-12-16 Add tests with negative values (Commit d8d8667 by Stefan Froemken)
2021-12-16 Add much more tests. Fix canBeBooked (Commit 2266665 by Stefan Froemken)

This list has been created by using `git log $(git describe --tags --abbrev=0)..HEAD --abbrev-commit --pretty='%ad %s (Commit %h by %an)' --date=short`.

Version 1.2.3
=============

2021-11-30 [TASK] Remove required field check for further participants (Commit 1df8d5a by Pascal Rinker)

Version 1.2.2
=============

2021-11-23 [BUGFIX] Change return type of staticRender back to string in QrCodeViewHelper (Commit by Stefan Froemken)

Version 1.2.1
=============

2021-11-19 [BUGFIX] Allow NULL as return value for getBegin (Commit by Stefan Froemken)

Version 1.2.0
=============

The list of changes includes changes before 1.1.1 because it was no TER release.

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
=============

2021-10-27 Use source instead of target in translation file (Commit 2f80f5d by Stefan Froemken)

Version 1.1.0
=============

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
=============

2021-01-28 [DOCS] Add route enhancers example configuration (Commit 6f29df9 by Pascal Rinker)
2021-01-27 [TASK] Add some more functional tests for CheckoutService (Commit ba2f6fb by Pascal Rinker)
2021-01-27 [TASK] Fix unit test (Commit 92851bf by Pascal Rinker)
2021-01-27 [FEATURE] Allow time slots with open end (Commit b009b72 by Pascal Rinker)
2021-01-27 [TASK] Remove unused stuff for TYPO3 v8 (Commit 26ea89d by Pascal Rinker)
2021-01-27 Merge branch 'master' of https://github.com/jweiland-net/reserve (Commit 742f39a by Pascal Rinker)
2021-01-27 [FEATURE] Add more customization options for frontend (Commit d5600b4 by Pascal Rinker)

This list has been created by using `git log $(git describe --tags --abbrev=0)..HEAD --abbrev-commit --pretty='%ad %s (Commit %h by %an)' --date=short`.

Version 0.0.2
=============

Bugfix: Use correct Namespace for QR Controller

Version 0.0.1
=============

Initial upload
