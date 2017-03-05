# Inline Entity Form Template

`inline_entity_form_template` is a module that allows adding existing entities from a template to an inline entity form widget.

## Usage

Assuming that you have installed [`Inline Entity Form`][inline_entity_form] module and selected `Inline Entity Form - Complex` widget for an entity reference field:

1. Install `Inline Entity Form Template` module.
2. On the form display management page of a selected entity type click on the cog to configure the `Inline Entity Form - Complex` widget.
3. Check `Allow users to add existing entities from a template`.
4. Now you're able to select an entity of the same type as an entity containing the entity reference field and copy over the references.

## Example

1. You have a content type `Notification` with a reference field `Users` which is intended to send a notification to selected users.
2. Almost every `Notification` should be sent to the management team (5 users).
3. You are tired of selecting at least 5 users from the management team in an Inline entity form widget every time you create a notification.
4. You want to click `Add from existing template`, select a notification node called `Notification to management team (template)`, click `Add` and have 5 users in the template listed in an Inline entity form widget.

[inline_entity_form]: https://www.drupal.org/project/inline_entity_form
