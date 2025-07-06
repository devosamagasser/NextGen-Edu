provider "azurerm" {
  features {}
}

resource "azurerm_resource_group" "main" {
  name     = var.resource_group_name
  location = var.location
}

resource "azurerm_app_service_plan" "laravel_plan" {
  name                = var.app_service_plan_name
  location            = azurerm_resource_group.main.location
  resource_group_name = azurerm_resource_group.main.name
  kind                = "Linux"
  reserved            = true

  sku {
    tier = "Basic"
    size = "B1"
  }
}

resource "azurerm_linux_web_app" "laravel_app" {
  name                = var.app_name
  location            = azurerm_resource_group.main.location
  resource_group_name = azurerm_resource_group.main.name
  service_plan_id     = azurerm_app_service_plan.laravel_plan.id
  https_only          = true
  virtual_network_subnet_id = var.subnet_id

  site_config {
    linux_fx_version = "PHP|8.3"
    always_on        = false
  }

  app_settings = {
    "APP_DEBUG"      = "true"
    "APP_KEY"        = "base64:Dsz40HWwbCqnq0oxMsjq7fItmKIeBfCBGORfspaI1Kw="
    "APP_URL"        = "nextgenedu-database.azurewebsites.net"
    "DB_CONNECTION"  = "mysql"
    "DB_DATABASE"    = "nextgenedu"
    "DB_HOST"        = "nextgenedu.mysql.database.azure.com"
    "DB_PASSWORD"    = "73eQf4b1%55568802"
    "DB_PORT"        = "3306"
    "DB_USERNAME"    = "nextgeneduadmin@nextgenedu"
   
  }
}
