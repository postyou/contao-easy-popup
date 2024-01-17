# Contao Easy Popup

This adds easy to use popups to the [Contao CMS](https://www.contao.org).

## Dependencies

-   [terminal42/contao-node](https://github.com/terminal42/contao-node)
-   [oveleon/contao-component-style-manager](https://github.com/oveleon/contao-component-style-manager) (optional)

## How to create a popup

1. Include the `js_easypopup.html5` template in the page layout
2. Create a node article in the backend module `Content > Nodes`
3. Select the created node in a link picker widget or use the insert tag directly: `<a href="{{popup_url::NODE_ID}}">Open popup</a>`
4. Done! The popup content is inserted at the end of the HTML body and can be displayed by clicking the link

## Configuration

You can configure the popup by activating the `Easy popup settings` checkbox within the node. There it is possible to add css classes to the popup container via the Style Manager (if installed) or in the `Popup CSS-Class` field

You can also customize the `easy_popup.html5` template to change the popup container.
