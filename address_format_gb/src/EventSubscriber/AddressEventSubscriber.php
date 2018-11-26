<?php

namespace Drupal\address_format_gb\EventSubscriber;

use CommerceGuys\Addressing\AddressFormat\AdministrativeAreaType;
use Drupal\address\Event\AddressEvents;
use Drupal\address\Event\AddressFormatEvent;
use Drupal\address\Event\SubdivisionsEvent;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

/**
 * Adds a county field and a predefined list of counties for Great Britain.
 */
class AddressEventSubscriber implements EventSubscriberInterface {

  /**
   * {@inheritdoc}
   */
  public static function getSubscribedEvents() {
    $events[AddressEvents::ADDRESS_FORMAT][] = ['onAddressFormat'];
    $events[AddressEvents::SUBDIVISIONS][] = ['onSubdivisions'];
    return $events;
  }

  /**
   * Alters the address format for Great Britain.
   *
   * @param \Drupal\address\Event\AddressFormatEvent $event
   *   The address format event.
   */
  public function onAddressFormat(AddressFormatEvent $event) {
    $definition = $event->getDefinition();
    if ($definition['country_code'] == 'GB') {
      // Insert county name into address format.
      $insert_point = "\n%postalCode";
      $replace = "\n%administrativeArea$insert_point";
      $definition['format'] = str_replace($insert_point, $replace, $definition['format']);

      $definition['administrative_area_type'] = AdministrativeAreaType::COUNTY;
      $definition['subdivision_depth'] = 1;
      $event->setDefinition($definition);
    }
  }

  /**
   * Provides counties for Great Britain.
   *
   * @param \Drupal\address\Event\SubdivisionsEvent $event
   *   The subdivisions event.
   */
  public function onSubdivisions(SubdivisionsEvent $event) {
    // For administrative areas $parents is an array with just the country code.
    // Otherwise it also contains the parent subdivision codes. For example,
    // if we were defining cities in California, $parents would be ['US', 'CA'].
    $parents = $event->getParents();
    if ($event->getParents() != ['GB']) {
      return;
    }

    $definitions = [
      'country_code' => $parents[0],
      'parents' => $parents,
      'subdivisions' => [
        'Barking and Dagenham' => [],
        'Barnet' => [],
        'Barnsley' => [],
        'Bath and North East Somerset' => [],
        'Bedford' => [],
        'Bexley' => [],
        'Birmingham' => [],
        'Blackburn with Darwen' => [],
        'Blackpool' => [],
        'Bolton' => [],
        'Bournemouth' => [],
        'Bracknell Forest' => [],
        'Bradford' => [],
        'Brent' => [],
        'Brighton and Hove' => [],
        'Bristol' => [],
        'Bromley' => [],
        'Buckinghamshire' => [],
        'Bury' => [],
        'Calderdale' => [],
        'Cambridgeshire' => [],
        'Camden' => [],
        'Central Bedfordshire' => [],
        'Cheshire East' => [],
        'Cheshire West and Chester' => [],
        'Cornwall' => [],
        'Coventry' => [],
        'Croydon' => [],
        'Cumbria' => [],
        'Darlington' => [],
        'Derby' => [],
        'Derbyshire' => [],
        'Devon' => [],
        'Doncaster' => [],
        'Dorset' => [],
        'Dudley' => [],
        'Durham' => [],
        'Ealing' => [],
        'East Riding of Yorkshire' => [],
        'East Sussex' => [],
        'Enfield' => [],
        'Essex' => [],
        'Gateshead' => [],
        'Gloucestershire' => [],
        'Greenwich' => [],
        'Hackney' => [],
        'Halton' => [],
        'Hammersmith and Fulham' => [],
        'Hampshire' => [],
        'Haringey' => [],
        'Harrow' => [],
        'Hartlepool' => [],
        'Havering' => [],
        'Herefordshire' => [],
        'Hertfordshire' => [],
        'Hillingdon' => [],
        'Hounslow' => [],
        'Isle of Wight' => [],
        'Isles of Scilly' => [],
        'Islington' => [],
        'Kensington and Chelsea' => [],
        'Kent' => [],
        'Kingston upon Hull' => [],
        'Kingston upon Thames' => [],
        'Kirklees' => [],
        'Knowsley' => [],
        'Lambeth' => [],
        'Lancashire' => [],
        'Leeds' => [],
        'Leicester' => [],
        'Leicestershire' => [],
        'Lewisham' => [],
        'Lincolnshire' => [],
        'Liverpool' => [],
        'London' => [],
        'Luton' => [],
        'Manchester' => [],
        'Medway' => [],
        'Merton' => [],
        'Middlesbrough' => [],
        'Milton Keynes' => [],
        'Newcastle upon Tyne' => [],
        'Newham' => [],
        'Norfolk' => [],
        'North East Lincolnshire' => [],
        'North Lincolnshire' => [],
        'North Somerset' => [],
        'North Tyneside' => [],
        'North Yorkshire' => [],
        'Northamptonshire' => [],
        'Northumberland' => [],
        'Nottingham' => [],
        'Nottinghamshire' => [],
        'Oldham' => [],
        'Oxfordshire' => [],
        'Peter' => [],
        'Plymouth' => [],
        'Poole' => [],
        'Portsmouth' => [],
        'Reading' => [],
        'Redbridge' => [],
        'Redcar and Cleveland' => [],
        'Richmond upon Thames' => [],
        'Rochdale' => [],
        'Rotherham' => [],
        'Rutland' => [],
        'Saint Helens' => [],
        'Salford' => [],
        'Sandwell' => [],
        'Sefton' => [],
        'Sheffield' => [],
        'Shropshire' => [],
        'Slough' => [],
        'Solihull' => [],
        'Somerset' => [],
        'South Gloucestershire' => [],
        'South Tyneside' => [],
        'Southampton' => [],
        'Southend-on-Sea' => [],
        'Southwark' => [],
        'Staffordshire' => [],
        'Stockport' => [],
        'Stockton-on-Tees' => [],
        'Stoke-on-Trent' => [],
        'Suffolk' => [],
        'Sunderland' => [],
        'Surrey' => [],
        'Sutton' => [],
        'Swindon' => [],
        'Tameside' => [],
        'Telford and Wrekin' => [],
        'Thurrock' => [],
        'Torbay' => [],
        'Tower Hamlets' => [],
        'Trafford' => [],
        'Wakefield' => [],
        'Walsall' => [],
        'Waltham Forest' => [],
        'Wandsworth' => [],
        'Warrington' => [],
        'Warwickshire' => [],
        'West Berkshire' => [],
        'West Sussex' => [],
        'Westminster' => [],
        'Wigan' => [],
        'Wiltshire' => [],
        'Windsor and Maidenhead' => [],
        'Wirral' => [],
        'Wokingham' => [],
        'Wolverhampton' => [],
        'Worcestershire' => [],
        'York' => [],
        'Antrim' => [],
        'Ards' => [],
        'Armagh' => [],
        'Ballymena' => [],
        'Ballymoney' => [],
        'Banbridge' => [],
        'Belfast' => [],
        'Carrickfergus' => [],
        'Castlereagh' => [],
        'Coleraine' => [],
        'Cookstown' => [],
        'Craigavon' => [],
        'Derry' => [],
        'Down' => [],
        'Dungannon and South Tyrone' => [],
        'Fermanagh' => [],
        'Larne' => [],
        'Limavady' => [],
        'Lisburn' => [],
        'Magherafelt' => [],
        'Moyle' => [],
        'Newry and Mourne' => [],
        'Newtownabbey' => [],
        'North Down' => [],
        'Omagh' => [],
        'Strabane' => [],
        'Aberdeen' => [],
        'Aberdeenshire' => [],
        'Angus' => [],
        'Argyll and Bute' => [],
        'Clackmannanshire' => [],
        'Dumfries and Galloway' => [],
        'Dundee' => [],
        'East Ayrshire' => [],
        'East Dunbartonshire' => [],
        'East Lothian' => [],
        'East Renfrewshire' => [],
        'Edinburgh' => [],
        'Falkirk' => [],
        'Fife' => [],
        'Glasgow' => [],
        'Highland' => [],
        'Inverclyde' => [],
        'Midlothian' => [],
        'Moray' => [],
        'North Ayrshire' => [],
        'North Lanarkshire' => [],
        'Orkney Islands' => [],
        'Outer Hebrides' => [],
        'Perth and Kinross' => [],
        'Renfrewshire' => [],
        'Scottish Borders' => [],
        'Shetland' => [],
        'South Ayrshire' => [],
        'South Lanarkshire' => [],
        'Stirling' => [],
        'West Dunbartonshire' => [],
        'West Lothian' => [],
        'Anglesey' => [],
        'Blaenau Gwent' => [],
        'Bridgend' => [],
        'Caerphilly' => [],
        'Cardiff' => [],
        'Carmarthenshire' => [],
        'Ceredigion' => [],
        'Conwy' => [],
        'Denbighshire' => [],
        'Flintshire' => [],
        'Gwynedd' => [],
        'Merthyr Tydfil' => [],
        'Monmouthshire' => [],
        'Neath Port Talbot' => [],
        'Newport' => [],
        'Pembrokeshire' => [],
        'Powys' => [],
        'Rhondda Cynon Taf' => [],
        'Swansea' => [],
        'Torfaen' => [],
        'Vale of Glamorgan' => [],
        'Wrexham' => [],
      ],
    ];
    $event->setDefinitions($definitions);
  }

}
