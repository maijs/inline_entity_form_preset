# Inline Entity Form Template

`inline_entity_form_template` is a module that allows adding existing entities from a template to an inline entity form widget.

## Usage

Assuming that you have installed [`Inline Entity Form`][inline_entity_form] module and selected `Inline Entity Form - Complex` widget for an entity reference field:

1. Install `Inline Entity Form Template` module.
2. On the form display management page of a selected entity type click on the cog next to the entity reference field to configure the `Inline Entity Form - Complex` widget.
3. Check `Allow users to add existing entities from a template` and save the settings.
4. Create a entity of selected entity type which will be the template and add entity references to it.
5. Create or edit an entity of selected entity type, click on `Add existing from a template` in the inline entity form widget, type the name of the template entity and click `Add` to copy the entity references.

Note: Entity references from the template will be appended to the current list of entity references.

## Example

1. You have an entity type `Notification` with a reference field `Users` which is intended to send a notification to selected users.
2. Almost every `Notification` should be sent to the management team (5 users).
3. You are tired of selecting at least 5 users from the management team in an `Inline entity form` widget every time you create a notification.
4. You want to click `Add existing from a template`, select a notification entity called `Notification to the management team (template)`, click `Add` and have 5 users from the template added to the `Inline entity form` widget.

[inline_entity_form]: https://www.drupal.org/project/inline_entity_form
