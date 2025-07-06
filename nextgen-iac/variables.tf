variable "resource_group_name" {
  type        = string
  description = "اسم resource group"
  default     = "NextGenRg"
}

variable "location" {
  type        = string
  default     = "Italy North"
}

variable "app_name" {
  type        = string
  description = "Name Of Laravel app"
  default     = "nextgenedu-database"
}

variable "app_service_plan_name" {
  type        = string
  description = " Name Of App Service Plan"
  default     = "ASP-NextGenRg-9d92"
}

variable "subnet_id" {
  type        = string
  description = "Subnet ID الخاص بالـ VNET integration"
  default     = "/subscriptions/8d65d79f-55c5-4270-8b57-7b981a3d581b/resourceGroups/NextGenRg/providers/Microsoft.Network/virtualNetworks/ProjectFW-vnet/subnets/AppService-Integration-Subnet"
}
