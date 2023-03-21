..  include:: /Includes.rst.txt


..  _routeenhancers:

=====================================
Route Enhancers example configuration
=====================================

This example contains all actions from CheckoutController.

..  code-block:: yaml

    ReservePlugin:
      type: Extbase
      extension: Reserve
      plugin: Reservation
      routes:
        - routePath: '/checkout/list'
          _controller: 'Checkout::list'
        - routePath: '/checkout/form/{period_uid}'
          _controller: 'Checkout::form'
          _arguments:
            period_uid: period
        - routePath: '/checkout/create'
          _controller: 'Checkout::create'
        - routePath: '/checkout/confirm/{order_email}/{order_activation_code}'
          _controller: 'Checkout::confirm'
          _arguments:
            order_email: email
            order_activation_code: activationCode
        - routePath: '/checkout/cancel/{order_email}/{order_activation_code}'
          _controller: 'Checkout::cancel'
          _arguments:
            order_email: email
            order_activation_code: activationCode
      requirements:
        period_uid: '^[0-9]+$'
        order_activation_code: '^[a-zA-Z0-9]+$'
      defaultController: 'Checkout::list'
      aspects:
        period_uid:
          type: PersistedAliasMapper
          tableName: tx_reserve_domain_model_period
          routeFieldName: uid
