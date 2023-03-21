..  include:: /Includes.rst.txt


..  _extend-reserve:

==============
Extend reserve
==============

The class `JWeiland\Reserve\Service\ReserveService` is the official public API for ext:reserve.
You can use this class in your own extension to get some information like remaining participants of a period.

If you want even more, then you can use the other Service classes, Repositories and so on too but make sure that some
functionality can change in upcoming versions. We try to keep compatibility but sometimes breaking changes are required.


..  toctree::
    :maxdepth: 2

    PeriodRegistrationViewHelper/Index
    ReserveService/Index
    UpdateTemplates/Index
