# rtShopPlugin

## Security

Administration security (rtShopPageAdmin) is **on** by default and requires the
permission `admin_shop`. This permission can be customized by setting in the
app.yml the `rt_shop_admin_credential` property. This permission is added to the
`admin` group from the rtCorePlugin fixtures file.

Visitor security (rtShopPage) is **off** by default. It can be enabled by setting
in the app.yml the `rt_shop_is_private` property to true. By default no specific
permission is configured. A permission can be configured by setting in the
app.yml the `rt_shop_visitor_credential` property.

