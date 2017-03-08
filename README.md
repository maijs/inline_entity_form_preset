# Inline Entity Form Preset

`inline_entity_form_preset` is a Drupal 8 module that allows adding existing references from a preset entity into an inline entity form widget.

## Example

1. You have an entity type `Notification` with a reference field `Users` which is intended to send notifications to selected users.
2. Almost every `Notification` should be sent to the management team (5 users).
3. You are tired of selecting at least 5 users from the management team in an `Inline entity form` widget every time you create a notification.
4. You want to click `Add existing from a preset`, select a notification entity called `Notification to the management team (preset)`, click `Add` and have references to 5 user entities from the preset copied over to the `Inline entity form` widget.
5. Additionally, if a notification is related to a design topic, you might also want to add 3 users from the design team by clicking on `Add existing from a preset`, select a notification entity called `Notification to the design team (preset)`, click `Add` and have references to 3 user entities from the preset added to the `Inline entity form` widget.

## Usage

Assuming that you have installed [`Inline Entity Form`][inline_entity_form] module and selected `Inline Entity Form - Complex` widget for an entity reference field:

1. Install `Inline Entity Form Preset` module.
2. On the form display management page of a selected entity type click on the cog next to the entity reference field to configure the `Inline Entity Form - Complex` widget.
3. Check `Allow users to add existing references from a preset` and save the settings.
4. Create a entity of selected entity type which will be the preset and add entity references to it.
5. Create or edit an entity of selected entity type, click on `Add existing from a preset` in the inline entity form widget, type the name of the preset entity and click `Add` to copy the entity references.

Note:

* Entity references from the preset will be appended to the current list of entity references.
* Entity references can only be copied from a preset entity which is of the same entity type and bundle.
* Entity references are copied from the same field which has the `Allow users to add existing references from a preset` setting enabled on the widget.

[inline_entity_form]: https://www.drupal.org/project/inline_entity_form
