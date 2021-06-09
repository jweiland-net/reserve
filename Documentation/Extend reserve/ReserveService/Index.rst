.. include:: ../Includes.txt

.. _reserve-service:

===============
Reserve service
===============

The class `JWeiland\Reserve\Service\ReserveService` is the official public API for ext:reserve.
You can use this class in your own extension to get some information like remaining participants of a period.

The following example shows a controller that uses FlexForms or TypoScript for Facility and DateTime selection. This selection will be used
to get the remaining participants of a matching period.

.. code-block:: php

   <?php

   declare(strict_types=1);

   /*
    * This file is part of the package my/example.
    *
    * For the full copyright and license information, please read the
    * LICENSE file that was distributed with this source code.
    */

   namespace My\Example\Controller;

   use JWeiland\Reserve\Service\ReserveService;
   use TYPO3\CMS\Extbase\Mvc\Controller\ActionController;

   class ExampleController extends ActionController
   {
       /**
        * @var ReserveService
        */
       protected $reserveService;

       public function __construct(ReserveService $reserveService)
       {
           $this->reserveService = $reserveService;
       }

       public function showAction(): void
       {
           $dateTime = new \DateTime();
           $dateTime->setTimestamp((int)$this->settings['dateTimeOfEvent']);
           $this->view->assign(
               'remainingParticipants',
               $this->reserveService->getRemainingParticipants((int)$this->settings['facility'], $dateTime)
           );
       }
   }


The FlexForms.xml may look like this.

.. code-block:: xml

   <?xml version="1.0" encoding="utf-8" standalone="yes" ?>
   <T3DataStructure>
      <sheets>
         <sDEF>
            <ROOT>
               <TCEforms>
                  <sheetTitle>Main</sheetTitle>
               </TCEforms>
               <type>array</type>
               <el>
                  <settings.dateTimeOfEvent>
                     <label>Date of the event</label>
                     <config>
                        <type>input</type>
                        <size>10</size>
                        <renderType>inputDateTime</renderType>
                        <eval>datetime</eval>
                        <default>0</default>
                     </config>
                  </settings.dateTimeOfEvent>
                  <settings.facility>
                     <TCEforms>
                        <label>Choose a Facility</label>
                        <config>
                           <type>group</type>
                           <internal_type>db</internal_type>
                           <allowed>tx_reserve_domain_model_facility</allowed>
                           <maxitems>1</maxitems>
                           <minitems>1</minitems>
                           <size>1</size>
                           <default>0</default>
                        </config>
                     </TCEforms>
                  </settings.facility>
               </el>
            </ROOT>
         </sDEF>
      </sheets>
   </T3DataStructure>
