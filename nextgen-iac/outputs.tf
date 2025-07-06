output "app_url" {
  value = azurerm_linux_web_app.laravel_app.default_hostname
}
