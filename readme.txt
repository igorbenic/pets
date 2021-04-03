=== Pets ===
Contributors: ibenic
Tags: pets, animals, animal shelter
Requires at least: 4.0
Tested up to: 5.7.0
Stable tag: 1.4.0
Requires PHP: 5.6
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

A plugin to manage websites with pets or animal shelters.

== Description ==

With this plugin you can manage any WordPress site with Pets.

With the custom Fields Manager, you can add as many fields as you need for your own pets.

This plugin comes with 2 custom taxonomies: Breed and Colors.

If you want to accept donation, this plugin integrates with Give, a plugin for donations. People can choose to donate to all your pets or only to one.

Current Features:

- Managing Pets
- Two Taxonomies (Breed and Color)
- Custom Fields & Sections
- Archive Search Form
- Archive Grid Layout
- Widgets
- Shortcodes

Shortcodes:

[pets_single id=0 image=true info=true] (Set image or info to false)

[pets_petfinder] - Show the Petfinder search from and animals. Check https://www.petfinder.com/developers/v2/docs/#get-animals for all attributes.

[pets_archive] - Display anywhere. Attributes: limit - number of pets per page, filter - which fields to use to filter the pets, filter_value - which values should a filter have, hide_nav - if set to 1, it will hide navigation, orderby - if set, it will use that to order

Example:

[pets_archive filter=adoptable filter_value=0] -> Will show every pet that is not adoptable, filter 'adoptable' is the field slug.

Roadmap (planned features):

- Supplies you need for your pets (integrated with Give)
- Favorites
- Partners (such as Vet stations)
- Activities (people can take a dog from shelter for a walk in the park)
- Found/Lost Pets
- Integration with various APIs such as PetFinder
- Better Templates

You're welcome to give your own ideas and features in the support forum.

**There will be a PRO version with a few features. 90% of the money will go into buying supplies for the animal shelter near the contributor(s) location. 10% will be spent on processing fees.**

== Installation ==

1. Install the Pets plugin from the WordPress Plugins Menu
2. You can also install it by uploading the Pets zipped folder in wp-content/plugins/
3. Activate it.


== Frequently Asked Questions ==

= My Give Form does not include pets. What to do? =

Make sure you have set the donation form under Pets > Settings > Give.

= Petfinder Search Form does not work =

Please make sure to create the fields under Pets > Fields to match the Petfinder attributes found on https://www.petfinder.com/developers/v2/docs/#get-animals.

For example, for attribute status, create a field:

- Name: Status (or any other)
- slug: status (Important to be the same as attribute on Petfinder)
- type: dropdown
- options: Adoptable, Found, Adopted

== Changelog ==

= 1.4.0 - 2021-04-04 =
* New

= 1.3.2 - 2020-11-23 =
* Fix: Per Page setting was not used. Was showing only 2 per page.

= 1.3.1 - 2020-11-17 =
* Refactor: Wrapping the software license code in case it exists already.

= 1.3.0 - 2020-11-15 =
* New: Custom Fields can be added to Widgets - Add Pet and Add Missing Pet. You need to check them to be there.
* New: [pets_archive] shortcode to display the archive anywhere.
* Fix: Search form not displayed if there are no search parameters found.
* Fix: Petfinder API fixed to perform queries only on known parameters.
* Update: Freemius service updated.

= 1.2.1 - 2020-08-26 =
* Fix: Petfinder search form fixed and FAQ added

= 1.2.0 - 2020-07-15 =
- New: Add a Pet Form which will always add a new pet.
- New: Setting to define which default status a new pet will be added to.
- New: Setting to email about any new missing pet added.
- New: Setting to email about any new pet added.
- New: Actions on each missing or new pet added.
- Fix: Missing Pet image could not be added.
- Fix: GiveWP Form on a single pet page would load multiple forms.
- Update: License Software

= 1.1.0 - 2019-09-20 =
- New: Widget: Pet - Add Missing. You can now show a widget for reporting missing pets.
- New: Show Missing in Search option - Show all reported missing pets in the search as well.
- New: Missing Post Status. When viewing pets in the admin area, you will see "Missing Pet" state next to it.
- New: Bulk Action - "Set to Missing" - for Pets to set them to missing in the admin area.
- New: Bulk Action - "Set to Published" - for Pets to set them to published if they were missing and found.
- New: PetFinder shortcode attributes.

= 1.0.0 - 2019-09-20 =
- New: Dropdown and Radio fields can be multiselected when searching.
- New: PetFinder integration. Add keys under Settings and a [pets_petfinder] shortcode in a page.

= 0.5.1 =
- Fix: Custom Empty Search Fields would not return all pets.
- Fix: Hiding taxonomies if no terms (colors or breed).

= 0.5.0 =
- Fields can now also be defined as searchable and they'll appear in the search form.
- Pet Locations

= 0.4.1 =
- Fixing error when donating for pets where sponsors would cause a fatal error.

= 0.4.0 =
- Sponsors for Pets
- Give integrated with Sponsors
- Pet Search Widget

= 0.3.0 =
- Added Sections for Fields

= 0.2.1 =
- Fixed styles
- Fixed Single Pet short description

= 0.2.0 =
- Added a Widget for Single Pet
- Added a Shortcode for Single Pet
- Added a Grid Layout on Archive page
- Added a Search form on Archive page

= 0.1.0 =
Basic features added.
