# Contao Easy Popup

This adds easy to use popups to the [Contao CMS](https://www.contao.org).

## Dependencies

-   [terminal42/contao-node](https://github.com/terminal42/contao-node)
-   [oveleon/contao-component-style-manager](https://github.com/oveleon/contao-component-style-manager) (optional)

## How to create a popup

1. Create a node article in the backend module `Content > Nodes`
2. Select the created node in a link picker widget or use the insert tag directly: `<a href="{{popup_url::NODE_ID}}">Open popup</a>`
3. Done! The popup content is inserted at the end of the HTML body and can be displayed by clicking the link from the previous step.

## Configuration

You can configure the popup by activating the `Easy popup settings` checkbox within the node.

-   `CSS-Class`: Add CSS classes to the popup container via the Style Manager (if installed) or the `Popup CSS-Class` field.
-   `Delay`: Enter a delay that should pass before the popup is displayed automatically.
-   `Timeout`: Enter a timeout in which the popup should not be displayed again.
-   `Show before leaving the page`: Activate the checkbox to show the popup before the user leaves the page.

You can also extend the `easy_popup/popup.html.twig` template to change the popup container.
