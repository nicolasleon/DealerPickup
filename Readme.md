# Dealer Pickup

This module allows customers to have their products delivered to a local store.

## Installation


* Copy the module into ```<thelia_root>/local/modules/``` directory and be sure that the name of the module is DealerPickup.
* Activate it in your thelia administration panel

Please note that the [Dealer module](http://modules.thelia.net/dealer.html) is required and must be installed before installing the Dealer Pickup module.


## Usage

The module uses Thelia's hooks and doesn't require any configuration.

## Hook

The module uses the following Thelia's hooks:

* `order-delivery.extra`
* `order-delivery.stylesheet`
* `order-invoice.delivery-address`
* `account-order.delivery-address `

## Todo
Add a 'Available in store' status with the dedicated customser email notofication.


# Dealer Pickup

Ce module ajoute un mode de livraison permettant au client de choisir leur magasins de livraison.

## Installation


* Copier le module dans le dossier ```<thelia_root>/local/modules/``` sous le nom DalerPickup.
* Activez le module dans le panneau d'administration de Thelia.

Nota Bene : ce module nécessite l'installation préalble du module [Dealer module](http://modules.thelia.net/dealer.html).



## Utilisation

Le module utilise les hooks de Thelia et ne nécessite aucune configuration particulière.

## Hook

Le module utilise les hooks Thelia suivants :


* `order-delivery.extra`
* `order-delivery.stylesheet`
* `order-invoice.delivery-address`
* `account-order.delivery-address `

## Todo
Ajouter un status "Disponible en magasin" associé à l'envoi de la notifcation email adaptée.
