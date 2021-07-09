# hello Module to export node REST API

## Requirement 

```
composer require 'drupal/restui:^1.20'
```
## How to use
1. Placed this module to <drupal_root>/module/custom
2. Enable this module
3. go to http://<domain>/admin/config/services/rest
4. Search Hello Node Resource from resource list and enable it.
5. select "Resource" in "Granularity".
6. checked "GET" Methods
7. checked "hal_json" from "Accepted request formats"
8. checked cookie based authentication 
9. save the configuration
10. go to /admin/config/system/site-information
11. type the api key (for example 12345)
12. now create dummy node let say (nid is 27)
13. now go to /v2/node/12345/27 (/v2/node/{apikey}/{nid})
14. you can see your export as a json