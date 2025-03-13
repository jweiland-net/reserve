..  _eventsTutorial:

======
Events
======

Several events can be used to modify the behaviour of EXT:reserve.

.. contents::
      :local:
      :depth: 2


Available events
----------------

When register to an event you can always access the class where the event is
fired. For additional items see column "Access to" in the table below.

.. csv-table:: Events
   :header: "Event class", "Fired in class", "Access to"

   "SendReservationEmailEvent", "CheckoutService", "getMailMessage()"
   "ValidateOrderEvent", "OrderValidator", "getOrder();getErrorResults()"



Examples
--------

Alter the Mail Object send after confirmation
~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~

To connect to an event, you need to register an event listener in your custom
extension. All what it needs is an entry in your
:file:`Configuration/Services.yaml` file:

..  code-block:: yaml

    services:
      Vendor\Extension\EventListener\YourListener:
        tags:
          - name: event.listener
            identifier: 'your-self-choosen-identifier'
            method: 'methodToConnectToEvent'
            event: JWeiland\Reserve\Event\SendReservationEmailEvent

An example event listener can look like this:

.. code-block:: php

    <?php

    declare(strict_types=1);

    namespace Vendor\Extension\EventListener;

    use JWeiland\Reserve\Event\SendReservationEmailEvent;

    class YourListener
    {
        /**
         * Do what you want...
         */
        public function methodToConnectToEvent(SendReservationEmailEvent $event): void
        {
            $mailMessage = $event->getMailMessage();

            // Do some stuff

            $event->setMailMessage($mailMessage);
        }
    }
